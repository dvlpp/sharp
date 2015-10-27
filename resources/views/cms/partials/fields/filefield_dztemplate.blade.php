<div id="dz-template" class="dz-preview dz-file-preview hidden">
    <div class="dz-preview">
        <div class="row">
            <div class="col-xs-3">
                <img class="dz-image img-responsive" data-dz-thumbnail />
            </div>
            <div class="col-xs-9">
                <div class="dz-details">
                    <div class="dz-filename"><span data-dz-name></span></div>
                    <div class="dz-size" data-dz-size></div>
                    <a class="dz-dl hidden">
                        <i class="fa fa-download"></i>
                        {{ trans("sharp::ui.form_fileField_dlBtn") }}
                    </a>
                </div>
            </div>
        </div>

        <div class="dz-progress">
            <div class="dz-upload" data-dz-uploadprogress>
                <div class="baranim"></div>
            </div>
        </div>
        <button type="button" class="close" aria-label="Close" data-dz-remove><span aria-hidden="true">&times;</span></button>
    </div>
</div>