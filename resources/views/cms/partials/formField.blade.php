@if(!in_array($field->type, ["hidden", "javascript"]))

    <div class="form-group sharp-field sharp-field-{{ $field->type }} {{ session()->has("errors") && session("errors")->first($key) ? 'has-error' : '' }} col-md-{{ $field->field_width ?: '12' }}"

        {{ $field->conditional_display ? 'data-conditional_display='.$field->conditional_display : '' }} >

        {!! \Dvlpp\Sharp\Form\Facades\SharpCmsField::make($key, $field, $instance) !!}

        @if($field->help)
            <p class="help-block">{!! $field->help !!}</p>
        @endif

    </div>

@else

    {!! \Dvlpp\Sharp\Form\Facades\SharpCmsField::make($key, $field, $instance) !!}

@endif