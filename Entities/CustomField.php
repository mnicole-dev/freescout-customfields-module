<?php

namespace Modules\CustomFields\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $table = 'custom_fields';
    protected $fillable = ['name', 'type', 'options', 'sort_order', 'all_mailboxes'];
    protected $casts = ['all_mailboxes' => 'boolean'];

    public function values()
    {
        return $this->hasMany(ConversationCustomField::class, 'custom_field_id');
    }

    public function mailboxes()
    {
        return $this->belongsToMany(\App\Mailbox::class, 'custom_field_mailbox', 'custom_field_id', 'mailbox_id');
    }
}
