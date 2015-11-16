@extends('sharp::cms/cmslayout')

@section("meta")
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('viewname') sharp-form @stop

@section('navcol')
    @include("sharp::cms.partials.navcol", ["current"=>$entity->key()])
@endsection


@section('contextbar')

    <p class="navbar-text">

        @if($instance->{$entity->idAttribute()})

            @if($instance->__sharp_duplication)
                {{ trans('sharp::ui.form_duplicateTitle', ["entity"=>$entity->label()]) }}
            @else
                {{ trans('sharp::ui.form_updateTitle', ["entity"=>$entity->label()]) }}
            @endif

        @else
            {{ trans('sharp::ui.form_createTitle', ["entity"=>$entity->label()]) }}
        @endif

    </p>

    <button type="submit" form="sharpform" class="btn navbar-btn btn-ok navbar-right">
        <i class='fa fa-check'></i>
        {{ trans('sharp::ui.form_updateBtn') }}
    </button>

    <a href="{{ route("cms.list", [$category->key(), $entity->key()]) }}" class="btn navbar-btn btn-cancel navbar-right">
        <i class="fa fa-times"></i>
        {{ trans('sharp::ui.form_cancelBtn') }}
    </a>

@endsection


@section('content')

    <div id="form-validation-error-message" class="hidden">
        <h1>{{ trans('sharp::ui.form_errors') }}</h1>
        <h2>{{ trans('sharp::ui.form_errors_detail') }}</h2>
    </div>

    {!! Form::model($instance, [
            "route"=>get_entity_update_form_route($category, $entity, $instance),
            "method"=>!$instance->__sharp_duplication && $instance->{$entity->idAttribute()}?"put":"post",
            "id"=>"sharpform"]) !!}

    {!! Form::hidden($entity->idAttribute(), ($instance->__sharp_duplication ? "" : $instance->{$entity->idAttribute()})) !!}

    @if(count($entity->formTemplateTabsConfig()))
        {{-- There are tabs --}}

        <ul class="nav nav-pills entity-tabs" role="tablist">
            @foreach($entity->formTemplateTabsConfig() as $k => $formTab)
                <li class="{{ $k==0?'active':'' }}">
                    <a href="#tab{{ $k }}">{{ $formTab->label() }}</a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">

            @foreach($entity->formTemplateTabsConfig() as $k => $formTab)

                <div role="tabpanel" class="tab-pane {{$k==0?"active":""}}" id="tab{{$k}}">

                    <div class="row">
                        @foreach($formTab->formTemplateColumnsConfig() as $column)

                            <div class="col-sm-{{ $column->width() }}">

                                @include("sharp::cms.partials.form", ["template" => $column])

                            </div>

                        @endforeach
                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="row">
            @foreach($entity->formTemplateColumnsConfig() as $column)

                <div class="col-sm-{{ $column->width() }}">

                    @include("sharp::cms.partials.form", ["template" => $column])

                </div>

            @endforeach
        </div>

    @endif

    {!! Form::close() !!}

    @include('sharp::cms.partials.fields.filefield_dztemplate')

@endsection

@section("scripts")
    @parent
    <script src="/sharp/sharp.form.min.js?v={{ $sharpVersion }}"></script>
@endsection