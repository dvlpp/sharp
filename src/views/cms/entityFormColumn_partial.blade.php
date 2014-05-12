@foreach($col as $key)

    <div class="form-group sharp-field sharp-field-{{ $col->$key->type }} {{ $errors->first($key) ? 'has-error' : '' }} col-md-{{ $col->$key->field_width ?: '12' }}"
        {{ $col->$key->conditional_display ? 'data-conditional_display='.$col->$key->conditional_display : '' }} >

        {{ \Dvlpp\Sharp\Form\Facades\SharpCmsField::make($key, $col->$key, $instance) }}

        <p class="help-block">
            {{ $errors->first($key) ?: $col->$key->help }}
        </p>
    </div>

@endforeach