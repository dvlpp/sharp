@if($entity->duplicable && check_ability('duplicate', $category->key, $entity->key, $instance->id))

    <span class="btn action">
        <a href="{{ route('cms.duplicate', [$category->key, $entity->key, $instance->id]) }}">
            <i class="fa fa-copy"></i>
        </a>
    </span>

@endif