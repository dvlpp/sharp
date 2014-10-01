<div class="r clearfix">

    <div class="col-sm-2 col-sm-offset-1">

        {{ Form::label('row', $row->label, ['class'=>"control-label"]) }}

    </div>

    <div class="col-sm-6 fields">

        <div class="row">
            @foreach($row->fields as $key)
                <div class="form-group col-sm-{{ $row->fields->$key->field_width ?: 12 }} sharp-field sharp-field-{{ $row->fields->$key->type }}">
                    {{ \Dvlpp\Sharp\AdvancedSearch\Facades\SharpAdvancedSearchField::make($key, $row->fields->$key) }}
                </div>
            @endforeach
        </div>

    </div>


</div>

