@extends('sharp::cms/cmslayout')

@section('viewname') sharp-form @stop

@section('navcol')
    @include("sharp::cms.partials.navcol", ["current"=>$entity->key])
@endsection


@section('contextbar')

    @if(isset($isEmbedded) && $isEmbedded)
        <button type="submit" form="sharp_embedded_cancel" class="btn navbar-btn navbar-btn-trail">
            @if($masterInstanceId)
                {{ trans('sharp::ui.form_updateTitle', ["entity" => $masterEntityLabel]) }}
            @else
                {{ trans('sharp::ui.form_createTitle', ["entity" => $masterEntityLabel]) }}
            @endif
        </button>
        <p class="navbar-text">/</p>
    @endif

    <p class="navbar-text">

        @if($instance->{$entity->id_attribute})

            @if($instance->__sharp_duplication)
                {{ trans('sharp::ui.form_duplicateTitle', ["entity"=>$entity->label]) }}
            @else
                {{ trans('sharp::ui.form_updateTitle', ["entity"=>$entity->label]) }}
            @endif

        @else
            {{ trans('sharp::ui.form_createTitle', ["entity"=>$entity->label]) }}
        @endif

    </p>

    <button type="submit" form="sharpform" class="btn navbar-btn btn-ok navbar-right"><i class='fa fa-check'></i> {{ trans('sharp::ui.form_updateBtn') }}</button>

    @if(isset($isEmbedded) && $isEmbedded)
        <button type="submit" form="sharp_embedded_cancel" class="btn navbar-btn btn-cancel navbar-right"><i class="fa fa-times"></i> {{ trans('sharp::ui.form_cancelBtn') }}</button>
    @else
        <a href="{{ route("cms.list", [$category->key, $entity->key]) }}" class="btn navbar-btn btn-cancel navbar-right"><i class="fa fa-times"></i> {{ trans('sharp::ui.form_cancelBtn') }}</a>
    @endif

@endsection


@section('content')

    @if(Session::has("errors"))

        <div class="alert alert-danger" role="alert">
            <h4>{{ trans('sharp::ui.form_errors') }}</h4>
            <ul>
            @foreach(session("errors")->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>

    @endif

    @if(isset($isEmbedded) && $isEmbedded)
        {!! Form::open(["route"=>["cms.embedded.cancel", $masterCategoryKey, $masterEntityKey], "method"=>"POST", "id"=>"sharp_embedded_cancel"]) !!}
            {!! Form::hidden('masterInstanceData', $masterInstanceData) !!}
            {!! Form::hidden('masterInstanceId', $masterInstanceId) !!}
        {!! Form::close() !!}
    @endif

    {!! Form::model($instance, [
            "route"=>(isset($isEmbedded) && $isEmbedded
                ? get_embedded_entity_update_form_route($masterCategoryKey, $masterEntityKey, $masterFieldKey, $category, $entity, $instance)
                : get_entity_update_form_route($category, $entity, $instance)),
            "method"=>!$instance->__sharp_duplication && $instance->{$entity->id_attribute}?"put":"post",
            "id"=>"sharpform"]) !!}

        {!! Form::hidden($entity->id_attribute, ($instance->__sharp_duplication ? "" : $instance->{$entity->id_attribute})) !!}

        @if(isset($isEmbedded) && $isEmbedded)
            {!! Form::hidden('masterInstanceData', $masterInstanceData) !!}
            {!! Form::hidden('masterInstanceId', $masterInstanceId) !!}
            {!! Form::hidden('masterEntityLabel', $masterEntityLabel) !!}
        @endif

        @if(count($entity->form_layout) > 1)
            {{-- There are tabs --}}

            <div class="row">
                <div class="col-xs-12">
                    <ul class="nav nav-pills entity-tabs" role="tablist">
                        <?php $k=0 ?>
                        @foreach($entity->form_layout as $keytab)
                            <li class="{{ $k==0?'active':'' }}"><a href="#tab{{ $k++ }}">{{ $entity->form_layout->$keytab->tab }}</a></li>
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

                            @include("sharp::cms.partials.formField", ["field" => $entity->form_fields->$key])

                        @endforeach

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="row">

                        @foreach($entity->form_layout->$keytab->col2->data as $key)

                            @include("sharp::cms.partials.formField", ["field" => $entity->form_fields->$key])

                        @endforeach

                        </div>
                    </div>
                </div>
            </div>

            @endforeach
        </div>

    {!! Form::close() !!}

@endsection

@section("scripts")
@parent
<script src="/sharp/sharp.form.min.js"></script>
@endsection