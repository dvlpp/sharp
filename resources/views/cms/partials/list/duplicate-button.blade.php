@if($entity->duplicable() && check_ability('duplicate', $category->key(), $entity->key(), $instance->id))
    <li>
        <a href="{{ route('cms.duplicate', [$category->key(), $entity->key(), $instance->id]) }}">
            <i class="fa fa-copy"></i>
            {{ trans("sharp::ui.list_entityDuplicateAction") }}
        </a>
    </li>
@endif