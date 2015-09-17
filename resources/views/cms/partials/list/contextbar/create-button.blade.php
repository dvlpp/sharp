@if($entity->list_template->creatable && check_ability('create', $category->key, $entity->key))

    <a href="{{ route('cms.create', [$category->key, $entity->key]) }}" class="btn navbar-btn navbar-right normal-mode">
        <i class="fa fa-plus"></i> {{ trans('sharp::ui.list_newBtn') }}
    </a>

@endif