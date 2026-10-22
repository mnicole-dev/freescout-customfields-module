<?php

namespace Modules\CustomFields\Tests;

use Modules\CustomFields\Services\CustomFieldService;
use PHPUnit\Framework\TestCase;

/**
 * Verrouille le contrat attendu par le module API & Webhooks.
 *
 * Ces règles ne sont visibles nulle part dans notre code : elles vivent dans
 * ApiWebhooksServiceProvider::formatEntity(). Si quelqu'un renomme un type ou un libellé,
 * l'API se met à exposer un vocabulaire différent sans que rien ne casse — d'où ces tests.
 */
class ApiCompatContractTest extends TestCase
{
    /** Reproduit exactement la dérivation faite par API & Webhooks. */
    private function apiTypeName(string $type): string
    {
        return str_replace(' ', '', strtolower(CustomFieldService::API_TYPE_LABELS[$type]));
    }

    public function test_chaque_type_retombe_sur_son_propre_nom(): void
    {
        foreach (CustomFieldService::TYPES as $type) {
            $this->assertSame(
                $type,
                $this->apiTypeName($type),
                "Le libellé de « $type » ne retombe plus sur le nom du type."
            );
        }
    }

    public function test_les_libelles_couvrent_tous_les_types_et_rien_de_plus(): void
    {
        $this->assertSame(
            CustomFieldService::TYPES,
            array_keys(CustomFieldService::API_TYPE_LABELS)
        );
    }

    public function test_options_map_indexe_par_la_valeur_enregistree(): void
    {
        // L'API lit $field->options[$field->value] : la clé doit être la valeur stockée.
        $this->assertSame(
            ['Rouge' => 'Rouge', 'Vert' => 'Vert'],
            CustomFieldService::optionsMap("Rouge\nVert")
        );
    }

    public function test_options_map_tolere_le_vide(): void
    {
        $this->assertSame([], CustomFieldService::optionsMap(null));
        $this->assertSame([], CustomFieldService::optionsMap(''));
        $this->assertSame([], CustomFieldService::optionsMap("\n  \n"));
    }
}
