<?php

namespace Modules\CustomFields\Entities;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
    protected $table = 'custom_fields';
    protected $fillable = ['name', 'type', 'options', 'sort_order'];

    public function values()
    {
        return $this->hasMany(ConversationCustomField::class, 'custom_field_id');
    }
}
