@if(!in_array($field->type, ["hidden", "javascript"]))

    <div class="form-group sharp-field sharp-field-{{ $field->type }} {{ key_name_for_form_field($key) }} col-md-{{ $size }}"

            {{ $field->conditional_display ? 'data-conditional_display='.$field->conditional_display : '' }} >

        {!! \Dvlpp\Sharp\Form\Facades\SharpCmsField::make($key, $field, $instance) !!}

        @if($field->help)
            <p class="help-block">{!! $field->help !!}</p>
        @endif

    </div>

@else

    {!! \Dvlpp\Sharp\Form\Facades\SharpCmsField::make($key, $field, $instance) !!}

@endif