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

<div class="row">
{{ Form::model($instance, ["route"=>$instance->id?["cms.update", $category->key, $entityKey, $instance->id]:["cms.store", $category->key, $entityKey],
    "method"=>$instance->id?"put":"post", "id"=>"sharpform"]) }}

    <div class="col-sm-6">

        @include("sharp::cms.entityFormColumn_partial", ["col" => $entity->form_fields->col1])

    </div>

    <div class="col-sm-6">

        @include("sharp::cms.entityFormColumn_partial", ["col" => $entity->form_fields->col2])

    </div>

{{ Form::close() }}
</div>

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
@stop