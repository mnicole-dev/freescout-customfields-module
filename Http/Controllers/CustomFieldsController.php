<?php

namespace Modules\CustomFields\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CustomFields\Entities\CustomField;
use Modules\CustomFields\Services\CustomFieldService;

class CustomFieldsController extends Controller
{
    public function index()
    {
        $fields = CustomField::orderBy('sort_order')->orderBy('id')->get();
        return view('customfields::admin.index', ['fields' => $fields]);
    }

    public function create()
    {
        return view('customfields::admin.form', ['field' => new CustomField(), 'types' => CustomFieldService::TYPES]);
    }

    public function store(Request $request)
    {
        $this->validateField($request);
        CustomField::create([
            'name'       => $request->name,
            'type'       => $request->type,
            'options'    => in_array($request->type, ['dropdown', 'multiselect'], true) ? $request->options : null,
            'sort_order' => (int) ($request->sort_order ?? (CustomField::max('sort_order') + 1)),
        ]);
        \Session::flash('flash_success_floating', __('Custom field created.'));
        return redirect()->route('customfields.index');
    }

    public function edit($id)
    {
        return view('customfields::admin.form', ['field' => CustomField::findOrFail($id), 'types' => CustomFieldService::TYPES]);
    }

    public function updateField(Request $request, $id)
    {
        $field = CustomField::findOrFail($id);
        $this->validateField($request);
        $field->update([
            'name'       => $request->name,
            'type'       => $request->type,
            'options'    => in_array($request->type, ['dropdown', 'multiselect'], true) ? $request->options : null,
            'sort_order' => (int) ($request->sort_order ?? $field->sort_order),
        ]);
        \Session::flash('flash_success_floating', __('Custom field updated.'));
        return redirect()->route('customfields.index');
    }

    public function destroy($id)
    {
        $field = CustomField::findOrFail($id);
        // Cascade applicative : supprimer les valeurs liées.
        $field->values()->delete();
        $field->delete();
        \Session::flash('flash_success_floating', __('Custom field deleted.'));
        return redirect()->route('customfields.index');
    }

    private function validateField(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:191',
            'type' => 'required|in:' . implode(',', CustomFieldService::TYPES),
        ]);
    }
}
