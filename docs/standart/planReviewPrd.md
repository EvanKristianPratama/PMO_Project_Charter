## PRD Template
# Feature: Manage Procedure

## Goal
- menambah mekanisme mapping glossary yg sudah ada ke regulation lain, selain membuat data glossary baru agar data konsisten.

## Model
- MstDefinition
- TrsDefinitionRegulation
- MstRegulation

## Business Rules
- hanya menampilkan data MstDefintion yg sudah dimapping terhadap MstRegulation melalui TrsDefinitionRegulation

## Existing Files
Controller:
- ProcedureController.php

Services:
- DefinitionService.php
- ProcedureService.php

Component:
- ManageGlossary.vue

## Result
Before:
- hanya bisa menampilkan glossary yg digunakan regulation terkait melalui mekanisme tambah data baru

After:
- bisa menggunakan glossary yg sudah ada sebelumnya dimapping ke regulation baru tanpa harus menambah data 



## Planning
Read docs/prd/......md

Before writing any code:
1. Analyze current project structure.
2. Identify affected files.
3. Check existing relationship between models.
4. Create an implementation plan.

Do not modify files yet.


## Implementation
Proceed with implementation based on the approved plan.

Requirements:
- Follow existing coding convention.
- Do not refactor unrelated modules.
- Keep backward compatibility.