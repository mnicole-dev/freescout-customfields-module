# FreeScout Custom Fields Module

Admin-defined custom fields (7 types) stored per conversation, shown and edited in the conversation
sidebar. Agent-only, global (not per-mailbox). Free alternative to the paid custom-fields module.

## Field types
Dropdown, Multiselect, Single line, Multi line, Tags, Number, Date.

## How it works
- **Manage → Custom Fields** (admins): create/edit/delete field definitions.
- Conversation sidebar shows a **Custom Fields** block; fill values and click **Save** (AJAX).
- Storage: `custom_fields` (definitions) + `conversation_custom_field` (values). No core changes.

## Requirements
FreeScout ≥ 1.8.0. No composer dependencies (PHPUnit is dev-only).

## Installation
```bash
cd Modules
git clone https://github.com/mnicole-dev/freescout-customfields-module CustomFields
```
Activate **CustomFields** in **Manage → Modules**. The migrations create the two tables.

> If installing via CLI in a container, run `artisan` as the web user, not root.

## Scope
V1 = display + edit. Required fields and search filtering are planned for V2. Deactivating the module
keeps your data (tables are not dropped).

## License
AGPL-3.0 — see [LICENSE](LICENSE).
