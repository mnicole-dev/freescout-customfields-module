<?php

namespace Modules\CustomFields\Entities;

use Illuminate\Database\Eloquent\Model;

class ConversationCustomField extends Model
{
    protected $table = 'conversation_custom_field';
    protected $fillable = ['conversation_id', 'custom_field_id', 'value'];
}
