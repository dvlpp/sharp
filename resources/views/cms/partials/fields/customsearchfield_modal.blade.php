<div class="modal fade customSearch-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ $title }}</h4>
            </div>
            <div class="modal-body">
                <div class="loading hidden">
                    <i class="fa fa-refresh fa-spin fa-3x"></i>
                    <span class="message">{{ trans("sharp::ui.form_customSearchField_modalLoading") }}</span>
                </div>
                <ul class="list-group">

                </ul>
            </div>
        </div>
    </div>
</div>