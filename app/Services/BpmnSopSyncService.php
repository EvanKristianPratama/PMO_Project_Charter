<?php

namespace App\Services;

use App\Models\BpmnWorkflow;
use App\Models\MstActor;
use App\Models\MstSop;
use App\Models\TrsMapActorSop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BpmnSopSyncService
{
    /**
     * Sinkronisasikan XML BPMN ke Database SOP (mst_sop & trs_map_actor_sop).
     */
    public static function syncBpmnToSop(BpmnWorkflow $workflow): bool
    {
        $sopType = $workflow->sop_type;
        if (!$sopType || !in_array($sopType, ['A', 'B'])) {
            return false;
        }

        $xmlString = $workflow->bpmn_xml;
        if (empty($xmlString)) {
            return false;
        }

        try {
            DB::beginTransaction();

            $xml = new \SimpleXMLElement($xmlString);
            $xml->registerXPathNamespace('bpmn', 'http://www.omg.org/spec/BPMN/20100524/MODEL');

            // 1. Ekstrak Lanes (Aktor)
            $lanes = [];
            foreach ($xml->xpath('//bpmn:lane') as $laneEl) {
                $laneId = (string) $laneEl['id'];
                $laneName = trim((string) $laneEl['name']);
                
                $nodeRefs = [];
                
                // PERBAIKAN: Baca elemen anak dengan prefix bpmn
                $bpmnChildren = $laneEl->children('bpmn', true);
                if (isset($bpmnChildren->flowNodeRef)) {
                    foreach ($bpmnChildren->flowNodeRef as $ref) {
                        $nodeRefs[] = (string) $ref;
                    }
                } else {
                    // Fallback jika XML tidak menggunakan prefix bpmn
                    foreach ($laneEl->flowNodeRef as $ref) {
                        $nodeRefs[] = (string) $ref;
                    }
                }
                
                $lanes[] = [
                    'id' => $laneId,
                    'name' => $laneName,
                    'nodeRefs' => $nodeRefs,
                ];
            }

            // 2. Ekstrak Nodes (Tasks, Start, End)
            $nodes = [];
            $taskTypes = ['userTask', 'serviceTask', 'scriptTask', 'manualTask', 'sendTask', 'receiveTask', 'businessRuleTask', 'task'];
            
            foreach ($taskTypes as $type) {
                foreach ($xml->xpath("//bpmn:{$type}") as $nodeEl) {
                    $id = (string) $nodeEl['id'];
                    $name = trim((string) $nodeEl['name']);
                    $nodes[$id] = [
                        'id' => $id,
                        'name' => $name ?: 'Langkah Tanpa Nama',
                        'type' => 'task',
                    ];
                }
            }

            foreach ($xml->xpath("//bpmn:startEvent") as $nodeEl) {
                $id = (string) $nodeEl['id'];
                $name = trim((string) $nodeEl['name']);
                $nodes[$id] = [
                    'id' => $id,
                    'name' => $name ?: 'Start',
                    'type' => 'start',
                ];
            }

            foreach ($xml->xpath("//bpmn:endEvent") as $nodeEl) {
                $id = (string) $nodeEl['id'];
                $name = trim((string) $nodeEl['name']);
                $nodes[$id] = [
                    'id' => $id,
                    'name' => $name ?: 'End',
                    'type' => 'end',
                ];
            }

            // 3. Ekstrak Alur (Sequence Flows)
            $edges = [];
            foreach ($xml->xpath('//bpmn:sequenceFlow') as $flowEl) {
                $source = (string) $flowEl['sourceRef'];
                $target = (string) $flowEl['targetRef'];
                $edges[] = [
                    'source' => $source,
                    'target' => $target,
                ];
            }

            // 4. Urutkan Task secara Kronologis (Topological/BFS)
            $startNodeIds = [];
            foreach ($nodes as $id => $node) {
                if ($node['type'] === 'start') {
                    $startNodeIds[] = $id;
                }
            }

            if (empty($startNodeIds)) {
                $targets = array_column($edges, 'target');
                foreach (array_keys($nodes) as $id) {
                    if (!in_array($id, $targets)) {
                        $startNodeIds[] = $id;
                    }
                }
            }

            $visited = [];
            $orderedTasks = [];
            $queue = $startNodeIds;

            while (!empty($queue)) {
                $currentId = array_shift($queue);
                if (in_array($currentId, $visited)) {
                    continue;
                }
                $visited[] = $currentId;

                if (isset($nodes[$currentId]) && $nodes[$currentId]['type'] === 'task') {
                    $orderedTasks[] = $nodes[$currentId];
                }

                foreach ($edges as $edge) {
                    if ($edge['source'] === $currentId) {
                        $queue[] = $edge['target'];
                    }
                }
            }

            // Tambahkan task terisolasi yang tidak terjangkau alur start
            foreach ($nodes as $id => $node) {
                if ($node['type'] === 'task' && !in_array($id, $visited)) {
                    $orderedTasks[] = $node;
                }
            }

            // 5. Bersihkan data SOP lama untuk tipe ini
            // Agar id berurutan dengan rapi sesuai alur baru, kita delete & reinsert
            $oldSopIds = MstSop::where('tipe', $sopType)->pluck('id')->toArray();
            TrsMapActorSop::whereIn('sop_id', $oldSopIds)->delete();
            MstSop::whereIn('id', $oldSopIds)->delete();

            // Default fallback IDs
            $defaultOrgId = DB::table('trs_organization')->value('id') ?: 1;
            $defaultRegId = DB::table('mst_regulation')->value('id');

            // 6. Masukkan data SOP dan pemetaan Aktor baru
            foreach ($orderedTasks as $task) {
                // Cari atau buat Aktor berdasarkan nama Lane
                $mappedActorIds = [];
                foreach ($lanes as $lane) {
                    if (in_array($task['id'], $lane['nodeRefs'])) {
                        $actorName = $lane['name'];
                        if (empty($actorName)) continue;

                        $actor = MstActor::whereRaw('LOWER(name) = ?', [strtolower($actorName)])->first();
                        if (!$actor) {
                            $actor = MstActor::create([
                                'name' => $actorName,
                                'organization_id' => $defaultOrgId,
                            ]);
                        }
                        $mappedActorIds[] = $actor->id;
                    }
                }

                // Buat baris SOP baru
                $sop = MstSop::create([
                    'regulation_id' => $defaultRegId,
                    'tipe' => $sopType,
                    'description' => $task['name'],
                ]);

                // Buat pemetaan Aktor SOP
                foreach ($mappedActorIds as $actorId) {
                    TrsMapActorSop::create([
                        'actor_id' => $actorId,
                        'sop_id' => $sop->id,
                        'tipe' => $sopType,
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal melakukan sinkronisasi BPMN ke SOP: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return false;
        }
    }

    /**
     * Hasilkan XML BPMN 2.0 dinamis secara langsung berdasarkan data Database SOP saat ini.
     */
    public static function generateXmlFromSop(string $sopType): string
    {
        if (!in_array($sopType, ['A', 'B'])) {
            return '';
        }

        // Ambil data SOP & pemetaan Aktor
        $sops = MstSop::with('mapActorSops.actor')
            ->where('tipe', $sopType)
            ->orderBy('id')
            ->get();

        // Kumpulkan Aktor unik yang dipetakan
        $actorsMap = [];
        foreach ($sops as $sop) {
            foreach ($sop->mapActorSops as $map) {
                if ($map->actor) {
                    $actorsMap[$map->actor->id] = $map->actor;
                }
            }
        }
        $actors = array_values($actorsMap);

        // Jika tidak ada data SOP, berikan template kosong
        if ($sops->isEmpty()) {
            return self::getEmptyTemplate();
        }

        // Jika ada SOP tapi tidak ada aktor terpetakan, buat aktor dummy agar swimlane terbuat
        if (empty($actors)) {
            $dummyActor = new MstActor(['id' => 999, 'name' => 'Fungsi ITSP']);
            $actors = [$dummyActor];
        }

        // Kalkulasi ukuran kanvas dinamis
        $laneHeight = 140;
        $participantHeight = max(200, count($actors) * $laneHeight);
        $participantWidth = 350 + (count($sops) * 240) + 150;

        // Mulai membangun XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<bpmn:definitions xmlns:bpmn="http://www.omg.org/spec/BPMN/20100524/MODEL"
                  xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI"
                  xmlns:dc="http://www.omg.org/spec/DD/20100524/DC"
                  xmlns:di="http://www.omg.org/spec/DD/20100524/DI"
                  id="Definitions_1"
                  targetNamespace="http://bpmn.io/schema/bpmn">
  <bpmn:collaboration id="Collaboration_1">
    <bpmn:participant id="Participant_1" name="Proses Utama" processRef="Process_1" />
  </bpmn:collaboration>
  <bpmn:process id="Process_1" isExecutable="true">
';

        // Bangun LaneSet
        $xml .= '    <bpmn:laneSet id="LaneSet_1">
';
        foreach ($actors as $index => $actor) {
            $xml .= "      <bpmn:lane id=\"Lane_Actor_{$actor->id}\" name=\"" . htmlspecialchars($actor->name) . "\">\n";
            
            if ($index === 0) {
                $xml .= "        <bpmn:flowNodeRef>start-event</bpmn:flowNodeRef>\n";
            }
            
            foreach ($sops as $sop) {
                $primaryActorId = $sop->mapActorSops->first()?->actor_id;
                if ($primaryActorId == $actor->id || (!$primaryActorId && $index === 0)) {
                    $xml .= "        <bpmn:flowNodeRef>task-sop-{$sop->id}</bpmn:flowNodeRef>\n";
                }
            }
            
            if ($index === 0) {
                $xml .= "        <bpmn:flowNodeRef>end-event</bpmn:flowNodeRef>\n";
            }
            
            $xml .= "      </bpmn:lane>\n";
        }
        $xml .= '    </bpmn:laneSet>
';

        // Bangun Event & Tasks
        $xml .= '    <bpmn:startEvent id="start-event" name="Mulai">
      <bpmn:outgoing>flow-start</bpmn:outgoing>
    </bpmn:startEvent>
';

        foreach ($sops as $index => $sop) {
            $incoming = ($index === 0) ? 'flow-start' : "flow-task-{$sops[$index - 1]->id}";
            $outgoing = ($index === count($sops) - 1) ? 'flow-end' : "flow-task-{$sop->id}";
            
            $xml .= "    <bpmn:userTask id=\"task-sop-{$sop->id}\" name=\"" . htmlspecialchars($sop->description) . "\">\n";
            $xml .= "      <bpmn:incoming>{$incoming}</bpmn:incoming>\n";
            $xml .= "      <bpmn:outgoing>{$outgoing}</bpmn:outgoing>\n";
            $xml .= "    </bpmn:userTask>\n";
        }

        $lastIncoming = count($sops) > 0 ? "flow-task-{$sops[count($sops) - 1]->id}" : 'flow-start';
        $xml .= "    <bpmn:endEvent id=\"end-event\" name=\"Selesai\">\n";
        $xml .= "      <bpmn:incoming>{$lastIncoming}</bpmn:incoming>\n";
        $xml .= "    </bpmn:endEvent>\n";

        // Tambah SequenceFlows
        if (count($sops) > 0) {
            $xml .= "    <bpmn:sequenceFlow id=\"flow-start\" sourceRef=\"start-event\" targetRef=\"task-sop-{$sops[0]->id}\" />\n";
            for ($i = 0; $i < count($sops) - 1; $i++) {
                $xml .= "    <bpmn:sequenceFlow id=\"flow-task-{$sops[$i]->id}\" sourceRef=\"task-sop-{$sops[$i]->id}\" targetRef=\"task-sop-{$sops[$i+1]->id}\" />\n";
            }
            $xml .= "    <bpmn:sequenceFlow id=\"flow-end\" sourceRef=\"task-sop-{$sops[count($sops) - 1]->id}\" targetRef=\"end-event\" />\n";
        } else {
            $xml .= "    <bpmn:sequenceFlow id=\"flow-start\" sourceRef=\"start-event\" targetRef=\"end-event\" />\n";
        }

        $xml .= '  </bpmn:process>
';

        // Bangun BPMNDiagram (Layout Koordinat Grafis)
        $xml .= '  <bpmndi:BPMNDiagram id="BPMNDiagram_1">
    <bpmndi:BPMNPlane id="BPMNPlane_1" bpmnElement="Process_1">
';

        // Bounds Participant
        $xml .= "      <bpmndi:BPMNShape id=\"Participant_1_di\" bpmnElement=\"Participant_1\" isHorizontal=\"true\">\n";
        $xml .= "        <dc:Bounds x=\"80\" y=\"40\" width=\"{$participantWidth}\" height=\"{$participantHeight}\" />\n";
        $xml .= "      </bpmndi:BPMNShape>\n";

        // Bounds Lanes
        foreach ($actors as $index => $actor) {
            $laneY = 40 + ($index * $laneHeight);
            $xml .= "      <bpmndi:BPMNShape id=\"Lane_Actor_{$actor->id}_di\" bpmnElement=\"Lane_Actor_{$actor->id}\" isHorizontal=\"true\">\n";
            $xml .= "        <dc:Bounds x=\"110\" y=\"{$laneY}\" width=\"" . ($participantWidth - 30) . "\" height=\"{$laneHeight}\" />\n";
            $xml .= "      </bpmndi:BPMNShape>\n";
        }

        // Bounds Start Event (di tengah lane pertama)
        $startEventX = 156;
        $startEventY = 40 + ($laneHeight / 2) - 18;
        $xml .= "      <bpmndi:BPMNShape id=\"start-event_di\" bpmnElement=\"start-event\">\n";
        $xml .= "        <dc:Bounds x=\"{$startEventX}\" y=\"{$startEventY}\" width=\"36\" height=\"36\" />\n";
        $xml .= "        <bpmndi:BPMNLabel>\n";
        $xml .= "          <dc:Bounds x=\"162\" y=\"" . ($startEventY + 41) . "\" width=\"25\" height=\"14\" />\n";
        $xml .= "        </bpmndi:BPMNLabel>\n";
        $xml .= "      </bpmndi:BPMNShape>\n";

        // Bounds Tasks
        $taskCoords = [];
        foreach ($sops as $index => $sop) {
            $taskX = 250 + ($index * 240);
            
            // Cari koordinat lane Y untuk aktor pertama yang dipetakan
            $actorIndex = 0;
            $primaryActorId = $sop->mapActorSops->first()?->actor_id;
            if ($primaryActorId) {
                foreach ($actors as $idx => $act) {
                    if ($act->id == $primaryActorId) {
                        $actorIndex = $idx;
                        break;
                    }
                }
            }
            $laneY = 40 + ($actorIndex * $laneHeight);
            $taskY = $laneY + ($laneHeight / 2) - 40;
            
            $taskCoords[$sop->id] = ['x' => $taskX, 'y' => $taskY];
            
            $xml .= "      <bpmndi:BPMNShape id=\"task-sop-{$sop->id}_di\" bpmnElement=\"task-sop-{$sop->id}\">\n";
            $xml .= "        <dc:Bounds x=\"{$taskX}\" y=\"{$taskY}\" width=\"100\" height=\"80\" />\n";
            $xml .= "      </bpmndi:BPMNShape>\n";
        }

        // Bounds End Event
        $endEventX = 250 + (count($sops) * 240);
        $endEventY = 40 + ($laneHeight / 2) - 18;
        $xml .= "      <bpmndi:BPMNShape id=\"end-event_di\" bpmnElement=\"end-event\">\n";
        $xml .= "        <dc:Bounds x=\"{$endEventX}\" y=\"{$endEventY}\" width=\"36\" height=\"36\" />\n";
        $xml .= "        <bpmndi:BPMNLabel>\n";
        $xml .= "          <dc:Bounds x=\"{$endEventX}\" y=\"" . ($endEventY + 41) . "\" width=\"37\" height=\"14\" />\n";
        $xml .= "        </bpmndi:BPMNLabel>\n";
        $xml .= "      </bpmndi:BPMNShape>\n";

        // Alur Waypoints Sequence Flows
        if (count($sops) > 0) {
            $firstTaskCoords = $taskCoords[$sops[0]->id];
            $xml .= "      <bpmndi:BPMNEdge id=\"flow-start_di\" bpmnElement=\"flow-start\">\n";
            $xml .= "        <di:waypoint x=\"" . ($startEventX + 36) . "\" y=\"" . ($startEventY + 18) . "\" />\n";
            $xml .= "        <di:waypoint x=\"{$firstTaskCoords['x']}\" y=\"" . ($firstTaskCoords['y'] + 40) . "\" />\n";
            $xml .= "      </bpmndi:BPMNEdge>\n";
            
            for ($i = 0; $i < count($sops) - 1; $i++) {
                $srcId = $sops[$i]->id;
                $tgtId = $sops[$i+1]->id;
                $srcC = $taskCoords[$srcId];
                $tgtC = $taskCoords[$tgtId];
                
                $xml .= "      <bpmndi:BPMNEdge id=\"flow-task-{$srcId}_di\" bpmnElement=\"flow-task-{$srcId}\">\n";
                $xml .= "        <di:waypoint x=\"" . ($srcC['x'] + 100) . "\" y=\"" . ($srcC['y'] + 40) . "\" />\n";
                $xml .= "        <di:waypoint x=\"{$tgtC['x']}\" y=\"" . ($tgtC['y'] + 40) . "\" />\n";
                $xml .= "      </bpmndi:BPMNEdge>\n";
            }
            
            $lastTaskCoords = $taskCoords[$sops[count($sops) - 1]->id];
            $xml .= "      <bpmndi:BPMNEdge id=\"flow-end_di\" bpmnElement=\"flow-end\">\n";
            $xml .= "        <di:waypoint x=\"" . ($lastTaskCoords['x'] + 100) . "\" y=\"" . ($lastTaskCoords['y'] + 40) . "\" />\n";
            $xml .= "        <di:waypoint x=\"{$endEventX}\" y=\"" . ($endEventY + 18) . "\" />\n";
            $xml .= "      </bpmndi:BPMNEdge>\n";
        } else {
            $xml .= "      <bpmndi:BPMNEdge id=\"flow-start_di\" bpmnElement=\"flow-start\">\n";
            $xml .= "        <di:waypoint x=\"" . ($startEventX + 36) . "\" y=\"" . ($startEventY + 18) . "\" />\n";
            $xml .= "        <di:waypoint x=\"{$endEventX}\" y=\"" . ($endEventY + 18) . "\" />\n";
            $xml .= "      </bpmndi:BPMNEdge>\n";
        }

        $xml .= '    </bpmndi:BPMNPlane>
  </bpmndi:BPMNDiagram>
</bpmn:definitions>';

        return $xml;
    }

    /**
     * Dapatkan template XML BPMN kosong.
     */
    private static function getEmptyTemplate(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
<bpmn:definitions xmlns:bpmn="http://www.omg.org/spec/BPMN/20100524/MODEL"
                  xmlns:bpmndi="http://www.omg.org/spec/BPMN/20100524/DI"
                  xmlns:dc="http://www.omg.org/spec/DD/20100524/DC"
                  xmlns:di="http://www.omg.org/spec/DD/20100524/DI"
                  id="Definitions_1"
                  targetNamespace="http://bpmn.io/schema/bpmn">
  <bpmn:process id="Process_1" isExecutable="true">
    <bpmn:startEvent id="start-event" name="Mulai">
      <bpmn:outgoing>Flow_1</bpmn:outgoing>
    </bpmn:startEvent>
    <bpmn:endEvent id="end-event" name="Selesai">
      <bpmn:incoming>Flow_1</bpmn:incoming>
    </bpmn:endEvent>
    <bpmn:sequenceFlow id="Flow_1" sourceRef="start-event" targetRef="end-event" />
  </bpmn:process>
  <bpmndi:BPMNDiagram id="BPMNDiagram_1">
    <bpmndi:BPMNPlane id="BPMNPlane_1" bpmnElement="Process_1">
      <bpmndi:BPMNShape id="start-event_di" bpmnElement="start-event">
        <dc:Bounds x="156" y="102" width="36" height="36" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="162" y="145" width="25" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>
      <bpmndi:BPMNShape id="end-event_di" bpmnElement="end-event">
        <dc:Bounds x="352" y="102" width="36" height="36" />
        <bpmndi:BPMNLabel>
          <dc:Bounds x="352" y="145" width="37" height="14" />
        </bpmndi:BPMNLabel>
      </bpmndi:BPMNShape>
      <bpmndi:BPMNEdge id="Flow_1_di" bpmnElement="Flow_1">
        <di:waypoint x="192" y="120" />
        <di:waypoint x="352" y="120" />
      </bpmndi:BPMNEdge>
    </bpmndi:BPMNPlane>
  </bpmndi:BPMNDiagram>
</bpmn:definitions>';
    }
}
