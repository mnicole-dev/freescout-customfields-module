# FreeScout Custom Fields Module

Admin-defined custom fields (7 types) stored per conversation, shown and edited in the conversation
sidebar. Agent-only. Each field targets all mailboxes or a chosen subset. Free alternative to the paid
custom-fields module.

## Field types
Dropdown, Multiselect, Single line, Multi line, Tags, Number, Date.

## How it works
- **Manage → Custom Fields** (admins): create/edit/delete field definitions.
- Conversation sidebar shows a **Custom Fields** block; fill values and click **Save** (AJAX).
- Storage: `custom_fields` (definitions) + `conversation_custom_field` (values). No core changes.

## Name collision with API & Webhooks

This module occupies the folder name (`CustomFields`, alias `customfields`) that the **paid**
custom-fields module would use. The official **API & Webhooks** module treats
`\Module::isActive('customfields')` as proof that the paid module is installed, then calls a global
`\CustomField` class. Until v1.2.0 that class did not exist, so every conversation webhook died on
`Class "CustomField" not found`.

`Entities/CustomFieldCompat.php` provides exactly the surface API & Webhooks expects, and nothing
else — the model, controllers and views are untouched. `start.php` aliases `\CustomField` to it.

Three details are worth knowing before touching this:

- **`aliases` in `module.json` does nothing here.** laravel-modules defines `registerAliases()`, but
  FreeScout never calls it. The alias must be declared with `class_alias()`.
- **The alias cannot be lazy.** `instanceof` does not trigger the autoloader, so a lazily-resolved
  alias would leave `$entity instanceof \CustomField` false and silently drop the fields.
- **`where('mailbox_id', …)` is translated, not supported.** The paid module has that column; we use
  an "all mailboxes" flag plus a pivot, so `CustomFieldCompatBuilder` rewrites that one filter.

The API exposes our own type vocabulary (`dropdown`, `singleline`, …) because the labels in
`CustomFieldService::API_TYPE_LABELS` are chosen to survive the transformation API & Webhooks
applies to them. `Tests/ApiCompatContractTest.php` locks that down.

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
