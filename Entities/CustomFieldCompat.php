<?php

namespace Modules\CustomFields\Entities;

use App\Conversation;
use Modules\CustomFields\Services\CustomFieldService;

/**
 * Surface de compatibilité avec le module officiel « Custom Fields ».
 *
 * Le module API & Webhooks teste `\Module::isActive('customfields')` puis appelle une
 * classe globale `\CustomField`, qui est le modèle du module payant. Notre module occupe
 * cet alias de dossier : le test passait, la classe n'existait pas, et chaque webhook de
 * conversation plantait sur « Class "CustomField" not found ».
 *
 * Tout ce qu'attend API & Webhooks vit ici et nulle part ailleurs. Le modèle
 * {@see CustomField}, les contrôleurs et les vues du module restent inchangés : cette
 * classe ne sert qu'aux appels venus de l'extérieur.
 */
class CustomFieldCompat extends CustomField
{
    /**
     * API & Webhooks compare `$field->type` à ces constantes, et dérive le nom de type
     * de l'API de `$types[$type]` en retirant les espaces et en passant en minuscules.
     * Nos types sont déjà des chaînes : en choisissant les libellés en conséquence,
     * l'API expose exactement notre vocabulaire (« dropdown », « singleline »…).
     */
    const TYPE_DROPDOWN    = 'dropdown';
    const TYPE_MULTISELECT = 'multiselect';
    const TYPE_SINGLE_LINE = 'singleline';
    const TYPE_MULTI_LINE  = 'multiline';
    const TYPE_TAGS        = 'tags';
    const TYPE_NUMBER      = 'number';
    const TYPE_DATE        = 'date';

    /** @var array<string,string> */
    public static $types = CustomFieldService::API_TYPE_LABELS;

    public function newEloquentBuilder($query)
    {
        return new CustomFieldCompatBuilder($query);
    }

    /**
     * Nos options sont stockées en texte libre, une par ligne ; l'API officielle attend
     * une table valeur => libellé, qu'elle indexe par la valeur enregistrée.
     */
    public function getOptionsAttribute($value)
    {
        return CustomFieldService::optionsMap($value);
    }

    /**
     * Notre module n'a pas de notion de champ obligatoire. L'API doit tout de même
     * renvoyer la clé, avec une valeur honnête.
     */
    public function getRequiredAttribute()
    {
        return false;
    }

    /**
     * Un champ s'applique à une boîte s'il vise toutes les boîtes, ou si le pivot la contient.
     */
    public function scopeForMailbox($query, $mailbox_id)
    {
        return $query->where(function ($q) use ($mailbox_id) {
            $q->where('all_mailboxes', true)
              ->orWhereIn('id', function ($sub) use ($mailbox_id) {
                  $sub->select('custom_field_id')
                      ->from('custom_field_mailbox')
                      ->where('mailbox_id', (int) $mailbox_id);
              });
        });
    }

    /**
     * Champs applicables à la boîte, chacun porteur de la valeur saisie sur la conversation.
     *
     * `value` n'est pas une colonne de `custom_fields` : elle n'est posée que pour la
     * lecture, et ces instances ne sont jamais enregistrées.
     */
    public static function getCustomFieldsWithValues($mailbox_id, $conversation_id)
    {
        $fields = static::forMailbox($mailbox_id)->orderBy('sort_order')->orderBy('id')->get();

        if (!$fields->count()) {
            return $fields;
        }

        $values = ConversationCustomField::where('conversation_id', $conversation_id)
            ->pluck('value', 'custom_field_id');

        foreach ($fields as $field) {
            $field->setAttribute('value', $values[$field->id] ?? null);
        }

        return $fields;
    }

    /**
     * Écrit une valeur, en réutilisant la sérialisation par type du module.
     *
     * Le même garde-fou que la sidebar s'applique : on refuse d'écrire sur un champ qui
     * ne cible pas la boîte de la conversation, sinon l'API enregistrerait une valeur que
     * l'interface n'affiche jamais.
     */
    public static function setValue($conversation_id, $custom_field_id, $value)
    {
        $field = static::with('mailboxes')->find($custom_field_id);
        $conversation = Conversation::find($conversation_id);

        if (!$field || !$conversation) {
            return false;
        }

        $applies = CustomFieldService::appliesToMailbox(
            (bool) $field->all_mailboxes,
            (int) $conversation->mailbox_id,
            $field->mailboxes->pluck('id')->all()
        );

        if (!$applies) {
            return false;
        }

        $serialized = CustomFieldService::serialize($field->type, $value);

        if ($serialized === null) {
            ConversationCustomField::where('conversation_id', $conversation->id)
                ->where('custom_field_id', $field->id)
                ->delete();

            return true;
        }

        ConversationCustomField::updateOrCreate(
            ['conversation_id' => $conversation->id, 'custom_field_id' => $field->id],
            ['value' => $serialized]
        );

        return true;
    }
}
