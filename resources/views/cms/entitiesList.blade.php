@extends('sharp::cms/cmslayout')

@section("meta")
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('viewname') sharp-list @stop

@section('navcol')
    @include("sharp::cms.partials.navcol")
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

    {{-- Link with HTML5's donwload property used by Download commands --}}
    <a data-base="{{ route("sharp.download") }}" download class="hidden" id="sharp_command_download_link"></a>

    <table class="table table-responsive table-striped" id="entity-list">
        <thead>
            <tr>
                @foreach($entity->listTemplateColumnsConfig() as $column)
                    <th class="col-xs-{{ $column->size() }}">

                        @if($column->sortable())
                            @if($list->sortedColumn() == $column->key())
                                <a class="sort current"
                                   href="{{ route('sharp.cms.list', array_merge([$category->key(), $entity->key()], Input::except(['page']), ['sort'=>$column->key(), 'dir'=>$list->sortedDirection()=='asc'?'desc':'asc'])) }}">
                                    {{ $column->headingLabel() }} <i class="fa fa-angle-{{ $list->sortedDirection()=='asc'?'up':'down' }}"></i>
                                </a>
                            @else
                                <a class="sort"
                                   href="{{ route('sharp.cms.list', array_merge([$category->key(), $entity->key()], Input::except(['page','dir']), ['sort'=>$column->key()])) }}">
                                    {{ $column->headingLabel() }} <i class="fa fa-angle-up"></i>
                                </a>
                            @endif
                        @else
                            {{ $column->headingLabel() }}
                        @endif

                    </th>
                @endforeach
                <th class="col-xs-2"></th>
            </tr>
        </thead>

        <tbody>
        @foreach($list->instances() as $instance)
            <tr class="entity-row" data-entity_id="{{ $instance->id }}">
                @foreach($entity->listTemplateColumnsConfig() as $column)
                    <td class="entity-data"
                        data-link="{{ check_ability('update', $category->key(), $entity->key(), $instance->id) ? route('sharp.cms.edit', [$category->key(), $entity->key(), $instance->id]) : '' }}">
                        @if($column->columnRenderer())
                            {!! \Dvlpp\Sharp\ListView\Renderers\SharpColumnRendererManager::render($column, $instance) !!}
                        @else
                            {{ get_entity_attribute_value($instance, $column->key()) }}
                        @endif
                    </td>
                @endforeach

                <td class="actions">

                    <div class="btn-group normal-mode">

                        @include("sharp::cms.partials.list.state-button")

                        <div class="btn dropdown">

                            <a class="" data-toggle="dropdown" data-target="#">
                                <i class="fa fa-asterisk"></i>
                            </a>

                            <ul class="dropdown-menu pull-right">

                                @include("sharp::cms.partials.list.delete-button")

                                @include("sharp::cms.partials.list.duplicate-button")

                                @include("sharp::cms.partials.list.entity-commands")

                            </ul>

                        </div>

                    </div>

                    @if(check_ability('reorder', $category->key(), $entity->key()) && $entity->reorderable())
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

@section("scripts")
    @parent

    <script src="/sharp/sharp.form.min.js?v={{ $sharpVersion }}"></script>

    @foreach(get_command_forms($entity) as $command)
        @include("sharp::cms.partials.list.commandForm", ["command"=>$command, "instance"=>null])
    @endforeach

@endsection