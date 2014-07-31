@extends('sharp::cms/cmslayout')

@section('viewname') sharp-form @stop

@section('navcol')
@include("sharp::cms.navcol_partial", ["current"=>$entity->key])
@stop

@section('contextbar')
<p class="navbar-text">
    @if($instance->id)
        {{ trans('sharp::ui.form_updateTitle', ["entity"=>$entity->label]) }}
    @else
        {{ trans('sharp::ui.form_createTitle', ["entity"=>$entity->label]) }}
    @endif
</p>
<button type="submit" form="sharpform" class="btn navbar-btn btn-ok navbar-right"><i class='fa fa-check'></i> {{ trans('sharp::ui.form_updateBtn') }}</button>
<a href="{{ URL::route("cms.list", [$category->key, $entityKey]) }}" class="btn navbar-btn btn-cancel navbar-right"><i class="fa fa-times"></i> {{ trans('sharp::ui.form_cancelBtn') }}</a>
@stop

@section('content')


{{ Form::model($instance, ["route"=>$instance->id?["cms.update", $category->key, $entityKey, $instance->id]:["cms.store", $category->key, $entityKey],
    "method"=>$instance->id?"put":"post", "id"=>"sharpform"]) }}

    @if(count($entity->form_layout) > 1)
        {{-- There are tabs --}}

        <div class="row">
            <div class="col-xs-12">
                <ul class="nav nav-pills nav-justified" role="tablist">
                    <?php $k=0 ?>
                    @foreach($entity->form_layout as $keytab)
                        <li class="{{ $k==0?'active':'' }}"><a href="#tab{{ $k++ }}" role="tab" data-toggle="tab">{{ $entity->form_layout->$keytab->tab }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>

    @endif

    <div class="tab-content">
        <?php $k=0 ?>
        @foreach($entity->form_layout as $keytab)

        <div class="tab-pane {{ $k==0?'active':'' }}" id="tab{{ $k++ }}">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">

                    @foreach($entity->form_layout->$keytab->col1->data as $key)

                        @include("sharp::cms.entityFormField_partial", ["field" => $entity->form_fields->$key])

                    @endforeach

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">

                    @foreach($entity->form_layout->$keytab->col2->data as $key)

                        @include("sharp::cms.entityFormField_partial", ["field" => $entity->form_fields->$key])

                    @endforeach

                    </div>
                </div>
            </div>
        </div>

        @endforeach
    </div>

{{ Form::close() }}

@if($instance->id && \Dvlpp\Sharp\Auth\SharpAccessManager::granted("entity", "delete", $entityKey))

    {{ Form::open(["route"=>["cms.destroy", $category->key, $entityKey, $instance->id], "method"=>"DELETE", "id"=>"sharpdelete"]) }}

        <hr/>
        <button data-confirm="{{ trans('sharp::ui.form_deleteConfirmMsg') }}" type="submit" class="btn btn-lg btn-danger">{{ trans('sharp::ui.form_deleteBtn') }}</button>

    {{ Form::close() }}

@endif

@stop

@section("scripts")
@parent
<script src="/packages/dvlpp/sharp/js/sharp.upload.min.js"></script>
<script src="/packages/dvlpp/sharp/js/sharp.markdown.min.js"></script>
<script src="/packages/dvlpp/sharp/js/sharp.list.min.js"></script>
<script src="/packages/dvlpp/sharp/js/sharp.date.min.js"></script>
<script src="/packages/dvlpp/sharp/js/sharp.ref.min.js"></script>
@stop