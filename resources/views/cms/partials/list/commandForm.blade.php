<div class="modal fade form-command-{{$commandKey}}">
    <div class="modal-dialog">

        {!! Form::open(["class"=>"form-command"]) !!}

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ trans("sharp::ui.command_params_modal_title") }}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach($fields as $key => $field)
                        @include("sharp::cms.partials.formField", ["field" => $field, "instance" => null, "size" => $field->field_width ?: 12])
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-lg">{{ trans("sharp::ui.command_params_modal_btn") }}</button>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>


