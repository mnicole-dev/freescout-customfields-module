@extends('layouts.app')

@section('title_full', __('Custom Fields'))

@section('content')
<div class="section-heading">{{ __('Custom Fields') }}</div>
<div class="row-container">
    <div class="row"><div class="col-xs-12">
        <p class="margin-top"><a href="{{ route('customfields.create') }}" class="btn btn-primary btn-sm">{{ __('New Custom Field') }}</a></p>
        <table class="table table-striped">
            <thead><tr><th>{{ __('Name') }}</th><th>{{ __('Type') }}</th><th>{{ __('Mailboxes') }}</th><th></th></tr></thead>
            <tbody>
            @forelse ($fields as $field)
                <tr>
                    <td>{{ $field->name }}</td>
                    <td>{{ __($field->type) }}</td>
                    <td>@if ($field->all_mailboxes){{ __('All mailboxes') }}@else{{ $field->mailboxes->pluck('name')->implode(', ') ?: '—' }}@endif</td>
                    <td class="text-right">
                        <a href="{{ route('customfields.edit', $field->id) }}" class="btn btn-default btn-xs">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('customfields.destroy', $field->id) }}" style="display:inline" onsubmit="return confirm('{{ __('Delete this field and all its values?') }}')">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-link btn-xs text-danger">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-help">{{ __('No custom fields yet.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
