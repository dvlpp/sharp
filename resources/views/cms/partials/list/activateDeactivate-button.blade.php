@if($entity->active_state_field && check_ability('activate', $category->key, $entity->key, $instance->id))

    <span class="btn state {{ get_entity_attribute_value($instance, $entity->active_state_field)?'state-active':'state-inactive' }}">

        <a href="{{ route('cms.deactivate', [$category->key, $entity->key, $instance->id]) }}"
           class="btn-state-active ajax"
           data-success="deactivate"><i class="fa fa-star"></i></a>

        <a href="{{ route('cms.activate', [$category->key, $entity->key, $instance->id]) }}"
           class="btn-state-inactive ajax"
           data-success="activate"><i class="fa fa-star-o"></i></a>

    </span>

@endif