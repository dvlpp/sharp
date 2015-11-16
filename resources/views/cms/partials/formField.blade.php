@if(!in_array($field->type(), ["hidden", "javascript"]))

    <div class="form-group sharp-field sharp-field-{{ $field->type() }} {{ key_name_for_form_field($field->key()) }}"

            {{ $field->isConditionalDisplay() ? 'data-conditional_display='.$field->conditionalDisplayField() : '' }} >

        {!! \Dvlpp\Sharp\Form\Facades\SharpCmsField::make($field, $instance) !!}

        @if($field->helpMessage())
            <p class="help-block">{!! $field->helpMessage() !!}</p>
        @endif

    </div>

@else

    {!! \Dvlpp\Sharp\Form\Facades\SharpCmsField::make($field, $instance) !!}

@endif