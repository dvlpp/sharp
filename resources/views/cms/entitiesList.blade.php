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

    {{-- Link with HTML5's donwload property used by Donwload commands --}}
    <a href="" download="" class="hidden" id="sharp_command_download_link"></a>

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

                    <div class="btn-group normal-mode">

                        @include("sharp::cms.partials.list.activateDeactivate-button")

                        @include("sharp::cms.partials.list.delete-button")

                        @include("sharp::cms.partials.list.duplicate-button")

                        @include("sharp::cms.partials.list.entity-commands")

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

    @foreach(get_command_forms($category, $entity) as $commandKey => $commandFields)
        @include("sharp::cms.partials.list.commandForm", ["commandKey"=>$commandKey, "fields"=>$commandFields])
    @endforeach

@endsection
