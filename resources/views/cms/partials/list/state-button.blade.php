@if($entity->state->data)

    <div class="btn dropdown">

        <a data-toggle="dropdown" data-target="#">
            <i class="fa fa-star entity-state"
               style="color:{{ $entity->state->values->{get_entity_attribute_value($instance, $entity->state->property)}->color }}"
               title="{{ $entity->state->values->{get_entity_attribute_value($instance, $entity->state->property)}->label }}"></i>
        </a>

        <ul class="dropdown-menu pull-right">
            @foreach($entity->state->values as $stateId => $state)
                @if(check_ability("state-$stateId", $category->key, $entity->key, $instance->id))
                    <li class="item {{ get_entity_attribute_value($instance, $entity->state->property)==$stateId ? "disabled" : "" }}">
                        <a href="{{ route('cms.changeState', [$category->key, $entity->key]) }}" class="change-entity-state"
                           data-state="{{ $stateId }}"
                           data-instance="{{ $instance->id }}"
                           data-label="{{ $entity->state->values->$stateId->label }}"
                           data-color="{{ $entity->state->values->$stateId->color }}">
                            {{ $entity->state->values->$stateId->label }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>

    </div>



@endif