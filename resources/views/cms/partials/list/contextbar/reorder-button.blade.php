@if($entity->reorderable()
            && check_ability('reorder', $category->key(), $entity->key())
            && (!$entity->searchable() || !Input::get('search')))

    <a id="sharp-reorder" class="btn navbar-btn navbar-right normal-mode">
        <i class="fa fa-sort"></i>
        {{ trans('sharp::ui.list_reorderBtn') }}
    </a>

    <div class="reorder-mode">
        <a id="sharp-reorder-ok" href="{{ route('cms.reorder', [$category->key(), $entity->key()]) }}" class="btn navbar-btn navbar-right">
            <i class="fa fa-check"></i>
            {{ trans('sharp::ui.list_reorderOkBtn') }}
        </a>

        <a href="{{ URL::full() }}" class="btn navbar-btn navbar-right">
            <i class="fa fa-times"></i>
            {{ trans('sharp::ui.list_reorderCancelBtn') }}
        </a>
    </div>

@endif