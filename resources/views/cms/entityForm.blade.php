@extends('sharp::cms/cmslayout')

@section("meta")
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('viewname') sharp-form @stop

@section('navcol')
    @include("sharp::cms.partials.navcol", ["current"=>$entity->key])
@endsection


@section('contextbar')

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

    <button type="submit" form="sharpform" class="btn navbar-btn btn-ok navbar-right">
        <i class='fa fa-check'></i>
        {{ trans('sharp::ui.form_updateBtn') }}
    </button>

    <a href="{{ route("cms.list", [$category->key, $entity->key]) }}" class="btn navbar-btn btn-cancel navbar-right">
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
            "method"=>!$instance->__sharp_duplication && $instance->{$entity->id_attribute}?"put":"post",
            "id"=>"sharpform"]) !!}

        {!! Form::hidden($entity->id_attribute, ($instance->__sharp_duplication ? "" : $instance->{$entity->id_attribute})) !!}

        @if(count($entity->form_layout) > 1)
            {{-- There are tabs --}}

            <ul class="nav nav-pills entity-tabs" role="tablist">
                @foreach($entity->form_layout as $k=>$keytab)
                    <li class="{{ $k==0?'active':'' }}">
                        <a href="#tab{{ $k++ }}">{{ $entity->form_layout->$keytab->tab }}</a>
                    </li>
                @endforeach
            </ul>

        @endif

        <div class="tab-content">
            @foreach($entity->form_layout as $k => $keytab)

            <div class="tab-pane {{ $k==0?'active':'' }}" id="tab{{ $k++ }}">

                @if($entity->form_layout->$keytab->template)

                    @include($entity->form_layout->$keytab->template, ["fields" => $entity->form_fields])

                @else
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
                @endif

            </div>

            @endforeach
        </div>

    {!! Form::close() !!}

    @include('sharp::cms.partials.fields.filefield_dztemplate')

@endsection

@section("scripts")
    @parent
    <script src="/sharp/sharp.form.min.js?v={{ $sharpVersion }}"></script>
@endsection