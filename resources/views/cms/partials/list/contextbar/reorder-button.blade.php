@if($entity->list_template->reorderable
            && check_ability('reorder', $category->key, $entity->key)
            && (!$entity->list_template->searchable || !Input::get('search')))

    <a id="sharp-reorder" class="btn navbar-btn navbar-right normal-mode">
        <i class="fa fa-sort"></i>
        {{ trans('sharp::ui.list_reorderBtn') }}
    </a>

    <a id="sharp-reorder-ok" href="{{ route('cms.reorder', [$category->key, $entity->key]) }}" class="btn navbar-btn navbar-right reorder-mode">
        <i class="fa fa-check"></i>
        {{ trans('sharp::ui.list_reorderOkBtn') }}
    </a>

    <a href="{{ URL::full() }}" class="btn navbar-btn navbar-right reorder-mode">
        <i class="fa fa-times"></i>
        {{ trans('sharp::ui.list_reorderCancelBtn') }}
    </a>

@endif