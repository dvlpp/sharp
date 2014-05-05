@foreach($col as $key)

    <div class="form-group sharp-field-{{ $col->$key->type }} {{ $errors->first($key) ? 'has-error' : '' }}">
        {{ \Dvlpp\Sharp\Form\Facades\SharpCmsField::make($key, $col->$key, $instance) }}
        <p class="help-block">
            {{ $errors->first($key) ?: $col->$key->help }}
        </p>
    </div>

@endforeach