<?php

namespace Modules\CustomFields\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mailbox;
use Illuminate\Http\Request;
use Modules\CustomFields\Entities\CustomField;
use Modules\CustomFields\Services\CustomFieldService;

class CustomFieldsController extends Controller
{
    public function index()
    {
        $fields = CustomField::with('mailboxes')->orderBy('sort_order')->orderBy('id')->get();
        return view('customfields::admin.index', ['fields' => $fields]);
    }

    public function create()
    {
        return view('customfields::admin.form', [
            'field'    => new CustomField(['all_mailboxes' => true]),
            'types'    => CustomFieldService::TYPES,
            'mailboxes' => Mailbox::orderBy('name')->get(),
            'selected' => [],
        ]);
    }

    public function store(Request $request)
    {
        $this->validateField($request);
        $field = CustomField::create($this->fieldData($request));
        $field->mailboxes()->sync($this->mailboxIds($request));
        \Session::flash('flash_success_floating', __('Custom field created.'));
        return redirect()->route('customfields.index');
    }

    public function edit($id)
    {
        $field = CustomField::findOrFail($id);
        return view('customfields::admin.form', [
            'field'    => $field,
            'types'    => CustomFieldService::TYPES,
            'mailboxes' => Mailbox::orderBy('name')->get(),
            'selected' => $field->mailboxes()->pluck('mailboxes.id')->all(),
        ]);
    }

    public function updateField(Request $request, $id)
    {
        $field = CustomField::findOrFail($id);
        $this->validateField($request);
        $field->update($this->fieldData($request));
        $field->mailboxes()->sync($this->mailboxIds($request));
        \Session::flash('flash_success_floating', __('Custom field updated.'));
        return redirect()->route('customfields.index');
    }

    public function destroy($id)
    {
        $field = CustomField::findOrFail($id);
        $field->mailboxes()->detach();
        $field->values()->delete();
        $field->delete();
        \Session::flash('flash_success_floating', __('Custom field deleted.'));
        return redirect()->route('customfields.index');
    }

    private function fieldData(Request $request): array
    {
        return [
            'name'          => $request->name,
            'type'          => $request->type,
            'options'       => in_array($request->type, ['dropdown', 'multiselect'], true) ? $request->options : null,
            'sort_order'    => (int) ($request->sort_order ?? 0),
            'all_mailboxes' => $request->boolean('all_mailboxes'),
        ];
    }

    private function mailboxIds(Request $request): array
    {
        return CustomFieldService::normalizeMailboxSelection($request->boolean('all_mailboxes'), $request->input('mailbox_ids'));
    }

    private function validateField(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:191',
            'type' => 'required|in:' . implode(',', CustomFieldService::TYPES),
        ]);
    }
}
