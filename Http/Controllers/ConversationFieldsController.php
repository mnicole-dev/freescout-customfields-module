<?php

namespace Modules\CustomFields\Http\Controllers;

use App\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CustomFields\Entities\CustomField;
use Modules\CustomFields\Entities\ConversationCustomField;
use Modules\CustomFields\Services\CustomFieldService;

class ConversationFieldsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function save(Request $request, $conversation)
    {
        $conversation = Conversation::findOrFail($conversation);
        $this->authorize('viewCached', $conversation);

        $input = (array) $request->input('cf', []); // cf[<field_id>] = value
        foreach (CustomField::all() as $field) {
            $raw = $input[$field->id] ?? null;
            $value = CustomFieldService::serialize($field->type, $raw);
            if ($value === null) {
                ConversationCustomField::where('conversation_id', $conversation->id)
                    ->where('custom_field_id', $field->id)->delete();
            } else {
                ConversationCustomField::updateOrCreate(
                    ['conversation_id' => $conversation->id, 'custom_field_id' => $field->id],
                    ['value' => $value]
                );
            }
        }

        return response()->json(['status' => 'success', 'msg' => __('Custom fields saved.')]);
    }
}
