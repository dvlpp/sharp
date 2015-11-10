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

    @if(count($entity->form_layout->data) > 1)
        {{-- There are tabs --}}

        <ul class="nav nav-pills entity-tabs" role="tablist">
            @foreach(array_keys($entity->form_layout->data) as $k => $keytab)
                <li class="{{ $k==0?'active':'' }}">
                    <a href="#tab{{ $k }}">{{ $keytab }}</a>
                </li>
            @endforeach
        </ul>

    @endif

    <div class="tab-content">

        @foreach(array_keys($entity->form_layout->data) as $k => $keytab)

            <div class="tab-pane {{ $k==0?'active':'' }}" id="tab{{ $k }}">

                @if(is_string($entity->form_layout->$keytab->data))

                    @include($entity->form_layout->$keytab->data, ["fields" => $entity->form_fields])

                @else

                    <div class="row">

                        @foreach($entity->form_layout->$keytab->data as $col => $rows)

                            {{--Main columns--}}
                            <div class="col-md-{{ 12/sizeof($entity->form_layout->$keytab->data) }}">

                                @foreach((array)$rows as $group => $row)

                                    @if(!is_numeric($group))

                                        {{--Form field panel--}}
                                        <div class="panel fieldset">
                                            <div class="panel-heading">
                                                <label class="control-label">{{ $group }}</label>
                                            </div>

                                            <div class="panel-body">

                                                @foreach((array)$row as $subrows)

                                                    <div class="row">

                                                        @foreach((array)$subrows as $key)

                                                            @include("sharp::cms.partials.formFieldsRow", [
                                                                "key" => $key,
                                                                "entity" => $entity,
                                                                "cols" => $subrows
                                                            ])

                                                        @endforeach

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    @else

                                        <div class="row">

                                            @foreach((array)$row as $key)

                                                @include("sharp::cms.partials.formFieldsRow", [
                                                    "key" => $key,
                                                    "entity" => $entity,
                                                    "cols" => $row
                                                ])

                                            @endforeach

                                        </div>

                                    @endif

                                @endforeach

                            </div>

                        @endforeach

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