<?php

namespace Modules\CustomFields\Entities;

use Illuminate\Database\Eloquent\Builder;

/**
 * Le module API & Webhooks liste les champs d'une boîte avec
 * `\CustomField::where('mailbox_id', $id)`, parce que le module officiel porte une
 * colonne `mailbox_id` : un champ y appartient à une seule boîte.
 *
 * Notre schéma est plus large — un drapeau « toutes les boîtes » plus un pivot — donc
 * cette colonne n'existe pas et la requête échouerait sur « Unknown column ». On traduit
 * ce seul filtre, plutôt que d'ajouter une colonne qui nous ferait perdre le ciblage
 * multi-boîtes.
 */
class CustomFieldCompatBuilder extends Builder
{
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column === 'mailbox_id') {
            // Accepte where('mailbox_id', 3) comme where('mailbox_id', '=', 3).
            $mailbox_id = $value === null ? $operator : $value;

            return $this->getModel()->scopeForMailbox($this, $mailbox_id);
        }

        return parent::where($column, $operator, $value, $boolean);
    }
}
