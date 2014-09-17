<div class="form-group sharp-field sharp-field-{{ $field->type }} {{ $errors->first($key) ? 'has-error' : '' }} col-md-{{ $field->field_width ?: '12' }}"
    {{ $field->conditional_display ? 'data-conditional_display='.$field->conditional_display : '' }} >

    {!! \Dvlpp\Sharp\Form\Facades\SharpCmsField::make($key, $field, $instance) !!}

    <p class="help-block">
        {{ $field->help }}
    </p>
</div>
