import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';

test('frontend smoke check', () => {
  const env = process.env.NODE_ENV ?? 'test';
  assert.ok(typeof env === 'string');
});

test('initiative support keeps business unit toggle hooks', () => {
  const componentPath = path.resolve('resources/js/Components/InitiativeSupport/InitiativeSupport.vue');
  const source = fs.readFileSync(componentPath, 'utf8');

  assert.match(source, /businessUnitToggleLabel/);
  assert.match(source, /initiativeBusinessUnitLabel\(row\.digital\)/);
  assert.match(source, /initiativeBusinessUnitLabel\(initiative\)/);
});
