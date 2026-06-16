@php
    use Modules\CustomFields\Services\CustomFieldService;
@endphp
@if (count($fields))
<div class="cf-block" id="cf-block" data-url="{{ route('customfields.conversation.save', $conversation->id) }}" data-csrf="{{ csrf_token() }}">
    <h4 class="cf-heading">{{ __('Custom Fields') }}</h4>
    @foreach ($fields as $field)
        @include('customfields::partials.field_input', ['field' => $field, 'value' => CustomFieldService::deserialize($field->type, $values[$field->id] ?? null)])
    @endforeach
    <button type="button" class="btn btn-primary btn-xs cf-save">{{ __('Save') }}</button>
    <span class="cf-saved text-success hidden">{{ __('Custom fields saved.') }}</span>
</div>
@endif
