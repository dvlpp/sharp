@extends('sharp::cms/cmslayout')

@section('viewname') sharp-list @stop

@section('navcol')
    @include("sharp::cms.partials.navcol", ["category"=>$category, "entityKey"=>$entityKey])
@endsection

@section('contextbar')

    <p class="navbar-text">
        {{ Lang::choice('sharp::ui.list_title', $totalCount) }}
        @if($pagination && $pagination->lastPage() > 1)
            {{ trans('sharp::ui.list_pagination', ['current'=>$pagination->currentPage(), 'total'=>$pagination->lastPage()]) }}
        @endif
    </p>

    {{-- Commands --}}
    @include("sharp::cms.partials.list.contextbar.entities-commands")

    {{-- Create button --}}
    @include("sharp::cms.partials.list.contextbar.create-button")

    {{-- Reorder --}}
    @include("sharp::cms.partials.list.contextbar.reorder-button")

    {{-- Sublist --}}
    @include("sharp::cms.partials.list.contextbar.listfilters-button")
    @include("sharp::cms.partials.list.contextbar.sublist-button")

    {{-- Quick search --}}
    @include("sharp::cms.partials.list.contextbar.quicksearch")

@endsection

@section('content')

    <form id="formToken">
        {!! Form::token() !!}
    </form>

    @if(session()->has("errorMessage"))
        <div class="alert alert-danger" role="alert">
            <h4>{{ trans('sharp::ui.command_params_validation_error') }}</h4>
            {{ session("errorMessage") }}
        </div>
    @endif

    @include("sharp::cms.partials.list.advancedsearch-form")

    <table class="table table-responsive table-striped" id="entity-list">
        <thead>
            <tr>
                @foreach($entity->list_template->columns as $colkey => $col)
                    <th class="col-xs-{{ $col->width }}">

                        @if($col->sortable)
                            @if($sortedColumn == $colkey)
                                <a class="sort current"
                                   href="{{ route('cms.list', array_merge([$category->key, $entityKey], Input::except(['page']), ['sort'=>$colkey, 'dir'=>$sortedDirection=='asc'?'desc':'asc'])) }}">
                                    {{ $col->header }} <i class="fa fa-angle-{{ $sortedDirection=='asc'?'up':'down' }}"></i>
                                </a>
                            @else
                                <a class="sort" href="{{ route('cms.list', array_merge([$category->key, $entityKey], Input::except(['page','dir']), ['sort'=>$colkey])) }}">
                                    {{ $col->header }} <i class="fa fa-angle-up"></i>
                                </a>
                            @endif
                        @else
                            {{ $col->header }}
                        @endif

                    </th>
                @endforeach
                <th class="col-xs-2"></th>
            </tr>
        </thead>

        <tbody>
        @foreach($instances as $instance)
            <tr class="entity-row" data-entity_id="{{ $instance->id }}">
                @foreach($entity->list_template->columns as $colKey => $col)
                    <td class="entity-data"
                        data-link="{{ sharp_granted('entity', 'update', $entityKey) ? route('cms.edit', [$category->key, $entityKey, $instance->id]) : '' }}">
                        @if($col->renderer)
                            {!! \Dvlpp\Sharp\ListView\Renderers\SharpColumnRendererManager::render($col, $colKey, $instance) !!}
                        @else
                            {{ get_entity_attribute_value($instance, $colKey) }}
                        @endif
                    </td>
                @endforeach

                <td class="actions">

                    <div class="normal-mode">

                        @include("sharp::cms.partials.list.activateDeactivate-button")

                        {{-- Entity commands --}}
                        <div class="btn-group normal-mode">
                            <button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">

                                @if(sharp_granted('entity', 'delete', $entityKey))
                                    <li>
                                        {!! Form::open(["route"=>["cms.destroy", $category->key, $entityKey, $instance->id], "method"=>"DELETE", "id"=>"sharpdelete".$instance->id]) !!}
                                        {!! Form::close() !!}
                                        <a href="#" class="sharp-delete" data-form="{{ "sharpdelete".$instance->id }}" data-confirm="{{ trans('sharp::ui.form_deleteConfirmMsg') }}">
                                            <i class="fa fa-trash"></i>
                                            {{ trans('sharp::ui.list_entityDeleteAction') }}
                                        </a>
                                    </li>
                                @endif

                                @if($entity->duplicable && sharp_granted('entity', 'update', $entityKey))

                                    @if(\Dvlpp\Sharp\Config\SharpSiteConfig::getLanguages())

                                        @foreach(\Dvlpp\Sharp\Config\SharpSiteConfig::getLanguages() as $languageCode => $languageName)
                                            <li>
                                                <a href="{{ route('cms.duplicate', [$category->key, $entityKey, $instance->id, $languageCode]) }}">
                                                    <i class="fa fa-copy"></i>
                                                    {{ trans('sharp::ui.list_entityDuplicateActionLocalized', ['lang' => $languageName]) }}
                                                </a>
                                            </li>
                                        @endforeach

                                    @else

                                        <li>
                                            <a href="{{ route('cms.duplicate', [$category->key, $entityKey, $instance->id]) }}">
                                                <i class="fa fa-copy"></i>
                                                {{ trans('sharp::ui.list_entityDuplicateAction') }}
                                            </a>
                                        </li>

                                    @endif

                                @endif

                                @if(sizeof($entity->commands->data) && sizeof($entity->commands->entity->data))

                                    @if(sharp_granted('entity', 'delete', $entityKey)
                                        || ($entity->duplicable && sharp_granted('entity', 'update', $entityKey)))

                                        <li class="divider"></li>

                                    @endif

                                    @foreach($entity->commands->entity as $command)

                                        @if(sharp_granted('entity', $entity->commands->entity->$command->auth ?: "update", $entityKey))
                                            <li>
                                                <a href="{{ route('cms.entityCommand', array_merge([$category->key, $entityKey, $command, $instance->id], Input::all())) }}"
                                                        class="sharp-command {{ $entity->commands->entity->$command->form ? 'with-form' : '' }}"
                                                        {!! $entity->commands->entity->$command->confirm ? 'data-confirm="'.e($entity->commands->entity->$command->confirm).'"' : '' !!}
                                                        {!! $entity->commands->entity->$command->type=="view" ? 'target="_blank"' : '' !!}>
                                                    {{ $entity->commands->entity->$command->text }}
                                                </a>
                                            </li>
                                        @endif

                                    @endforeach
                                @endif
                            </ul>
                        </div>

                    </div>

                    @if(sharp_granted('entity', 'update', $entityKey) && $entity->list_template->reorderable)
                        <div class="reorder-mode">
                            <a href="#" class="btn reorder-handle"><i class="fa fa-sort"></i></a>
                        </div>
                    @endif

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($pagination)
        {!! $pagination->appends(Input::except(['page']))->render() !!}
    @endif

@endsection

@section("scripts")
    @parent
    @if($entity->advanced_search->data)
        <script src="/sharp/sharp.advancedsearch.min.js"></script>
    @endif
@endsection