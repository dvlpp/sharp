<div class="modal fade form-command-{{$command->key()}}">
    <div class="modal-dialog">

        {!! Form::open(["class"=>"form-command"]) !!}

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ trans("sharp::ui.command_params_modal_title") }}</h4>
            </div>
            <div class="modal-body">

                @include("sharp::cms.partials.formColumn", ["formTemplate" => $command->formTemplateConfig()])

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-lg">{{ trans("sharp::ui.command_params_modal_btn") }}</button>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>


