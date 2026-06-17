@extends('layouts.app')

@section('title_full', $field->id ? __('Edit Custom Field') : __('New Custom Field'))

@section('content')
<div class="section-heading">{{ $field->id ? __('Edit Custom Field') : __('New Custom Field') }}</div>
<div class="row-container"><div class="row"><div class="col-xs-12">
<form class="form-horizontal margin-top" method="POST" action="{{ $field->id ? route('customfields.update', $field->id) : route('customfields.store') }}">
    {{ csrf_field() }}
    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('Name') }}</label>
        <div class="col-sm-6"><input type="text" name="name" class="form-control" value="{{ $field->name }}" required></div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('Type') }}</label>
        <div class="col-sm-6">
            <select name="type" id="cf-type" class="form-control">
                @foreach ($types as $t)
                    <option value="{{ $t }}" @if ($field->type == $t) selected @endif>{{ __($t) }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group" id="cf-options-group">
        <label class="col-sm-2 control-label">{{ __('Options') }}</label>
        <div class="col-sm-6">
            <textarea name="options" class="form-control" rows="4" placeholder="{{ __('One option per line') }}">{{ $field->options }}</textarea>
            <p class="form-help">{{ __('Used by Dropdown and Multiselect. One option per line.') }}</p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">{{ __('Mailboxes') }}</label>
        <div class="col-sm-6">
            <label class="checkbox-inline">
                <input type="checkbox" name="all_mailboxes" id="cf-all-mailboxes" value="1" @if ($field->all_mailboxes) checked @endif> {{ __('All mailboxes') }}
            </label>
            <div id="cf-mailboxes-group" class="margin-top-10">
                @foreach ($mailboxes as $mb)
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="mailbox_ids[]" value="{{ $mb->id }}" @if (in_array($mb->id, $selected)) checked @endif> {{ $mb->name }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="form-group"><div class="col-sm-6 col-sm-offset-2">
        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        <a href="{{ route('customfields.index') }}" class="btn btn-link">{{ __('Cancel') }}</a>
    </div></div>
</form>
</div></div></div>
<script>
(function () {
    function toggleOptions() {
        var t = document.getElementById('cf-type').value;
        document.getElementById('cf-options-group').style.display = (t === 'dropdown' || t === 'multiselect') ? '' : 'none';
    }
    function toggleMailboxes() {
        var all = document.getElementById('cf-all-mailboxes').checked;
        document.getElementById('cf-mailboxes-group').style.display = all ? 'none' : '';
    }
    document.getElementById('cf-type').addEventListener('change', toggleOptions);
    document.getElementById('cf-all-mailboxes').addEventListener('change', toggleMailboxes);
    toggleOptions();
    toggleMailboxes();
})();
</script>
@endsection
