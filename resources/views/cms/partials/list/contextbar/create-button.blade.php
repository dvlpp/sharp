@if($entity->list_template->creatable && sharp_granted('entity', 'create', $entityKey))

    <a href="{{ route('cms.create', [$category->key, $entityKey]) }}" class="btn navbar-btn navbar-right normal-mode">
        <i class="fa fa-plus"></i> {{ trans('sharp::ui.list_newBtn') }}
    </a>

@endif