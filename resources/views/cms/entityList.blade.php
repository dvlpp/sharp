@extends('sharp::cms/cmslayout')

@section("meta")
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('viewname') sharp-list @stop

@section('navcol')
    @include("sharp::cms.partials.navcol", ["category"=>$category, "entityKey"=>$entity->key])
@endsection

@section('contextbar')

    <p class="navbar-text">
        {{ trans_choice('sharp::ui.list_title', $list->count()) }}
        @if($list->paginator() && $list->paginator()->lastPage() > 1)
            {{ trans('sharp::ui.list_pagination', ['current'=>$list->paginator()->currentPage(), 'total'=>$list->paginator()->lastPage()]) }}
        @endif
    </p>

    {{-- Commands --}}
    @include("sharp::cms.partials.list.contextbar.entities-commands")

    {{-- Create button --}}
    @include("sharp::cms.partials.list.contextbar.create-button")

    {{-- Reorder --}}
    @include("sharp::cms.partials.list.contextbar.reorder-button")

    {{-- List filters --}}
    @include("sharp::cms.partials.list.contextbar.listfilters-button")

    {{-- Quick search --}}
    @include("sharp::cms.partials.list.contextbar.quicksearch")

@endsection

@section('content')

    <table class="table table-responsive table-striped" id="entity-list">
        <thead>
            <tr>
                @foreach($entity->list_template->columns as $colkey => $col)
                    <th class="col-xs-{{ $col->width }}">

                        @if($col->sortable)
                            @if($list->sortedColumn() == $colkey)
                                <a class="sort current"
                                   href="{{ route('cms.list', array_merge([$category->key, $entity->key], Input::except(['page']), ['sort'=>$colkey, 'dir'=>$list->sortedDirection()=='asc'?'desc':'asc'])) }}">
                                    {{ $col->header }} <i class="fa fa-angle-{{ $list->sortedDirection()=='asc'?'up':'down' }}"></i>
                                </a>
                            @else
                                <a class="sort" href="{{ route('cms.list', array_merge([$category->key, $entity->key], Input::except(['page','dir']), ['sort'=>$colkey])) }}">
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
        @foreach($list->instances() as $instance)
            <tr class="entity-row" data-entity_id="{{ $instance->id }}">
                @foreach($entity->list_template->columns as $colKey => $col)
                    <td class="entity-data"
                        data-link="{{ check_ability('update', $category->key, $entity->key, $instance->id) ? route('cms.edit', [$category->key, $entity->key, $instance->id]) : '' }}">
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

                                @if(check_ability('delete', $category->key, $entity->key, $instance->id))
                                    <li>
                                        {!! Form::open(["route"=>["cms.destroy", $category->key, $entity->key, $instance->id], "method"=>"DELETE", "id"=>"sharpdelete".$instance->id]) !!}
                                        {!! Form::close() !!}
                                        <a href="#" class="sharp-delete" data-form="{{ "sharpdelete".$instance->id }}" data-confirmdelete="{{ trans('sharp::ui.form_deleteConfirmMsg') }}">
                                            <i class="fa fa-trash"></i>
                                            {{ trans('sharp::ui.list_entityDeleteAction') }}
                                        </a>
                                    </li>
                                @endif

                                @if($entity->duplicable && check_ability('duplicate', $category->key, $entity->key, $instance->id))

                                    @if(\Dvlpp\Sharp\Config\SharpSiteConfig::getLanguages())

                                        @foreach(\Dvlpp\Sharp\Config\SharpSiteConfig::getLanguages() as $languageCode => $languageName)
                                            <li>
                                                <a href="{{ route('cms.duplicate', [$category->key, $entity->key, $instance->id, $languageCode]) }}">
                                                    <i class="fa fa-copy"></i>
                                                    {{ trans('sharp::ui.list_entityDuplicateActionLocalized', ['lang' => $languageName]) }}
                                                </a>
                                            </li>
                                        @endforeach

                                    @else

                                        <li>
                                            <a href="{{ route('cms.duplicate', [$category->key, $entity->key, $instance->id]) }}">
                                                <i class="fa fa-copy"></i>
                                                {{ trans('sharp::ui.list_entityDuplicateAction') }}
                                            </a>
                                        </li>

                                    @endif

                                @endif

                                @if(sizeof($entity->commands->data) && sizeof($entity->commands->entity->data))

                                    @if(check_ability('delete', $category->key, $entity->key, $instance->id)
                                        || ($entity->duplicable && check_ability('update', $category->key, $entity->key, $instance->id)))

                                        <li class="divider"></li>

                                    @endif

                                    @foreach($entity->commands->entity as $command)

                                        @if(check_ability($entity->commands->entity->$command->auth ?: "update", $category->key, $entity->key, $instance->id))
                                            <li>
                                                <a href="{{ route('cms.entityCommand', array_merge([$category->key, $entity->key, $command, $instance->id], Input::all())) }}"
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

                    @if(check_ability('reorder', $category->key, $entity->key) && $entity->list_template->reorderable)
                        <div class="reorder-mode">
                            <a href="#" class="btn"><i class="reorder-handle fa fa-sort"></i></a>
                        </div>
                    @endif

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    @if($list->paginator())
        {!! $list->paginator()->appends(Input::except(['page']))->render() !!}
    @endif

@endsection
