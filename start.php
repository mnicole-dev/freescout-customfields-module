<?php

use Modules\CustomFields\Entities\CustomFieldCompat;

if (!app()->routesAreCached()) {
    require __DIR__ . '/Http/routes.php';
}

// Le module API & Webhooks appelle une classe globale `\CustomField` dès que
// `\Module::isActive('customfields')` est vrai — un nom que notre module occupe.
// L'alias est posé ici, au démarrage du module, et non via `aliases` dans module.json :
//   - laravel-modules définit bien registerAliases(), mais FreeScout ne l'appelle jamais,
//     donc cette clé de module.json n'a aucun effet ;
//   - `instanceof` ne déclenche pas l'autoloader, donc un alias paresseux laisserait
//     `$entity instanceof \CustomField` à faux et les champs seraient absents du webhook.
// Le garde laisse la priorité au module officiel s'il est installé un jour.
if (!class_exists('CustomField', false)) {
    class_alias(CustomFieldCompat::class, 'CustomField');
}
