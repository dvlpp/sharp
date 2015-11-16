@if($entity->stateIndicator())

    <div class="btn dropdown">

        <a data-toggle="dropdown" data-target="#">
            <i class="fa fa-star entity-state"
               style="color:{{ $entity->stateIndicator()->findState(get_entity_attribute_value($instance, $entity->stateIndicator()->stateAttribute()))->hexColor }}"
               title="{{ $entity->stateIndicator()->findState(get_entity_attribute_value($instance, $entity->stateIndicator()->stateAttribute()))->label }}"></i>
        </a>

        <ul class="dropdown-menu pull-right">
            @foreach($entity->stateIndicator()->states() as $state)
                @if(check_ability("state-{$state->value}", $category->key(), $entity->key(), $instance->id))
                    <li class="item {{ get_entity_attribute_value($instance, $entity->stateIndicator()->stateAttribute())==$state->value ? "disabled" : "" }}">
                        <a href="{{ route('cms.changeState', [$category->key(), $entity->key()]) }}" class="change-entity-state"
                           data-state="{{ $state->value }}"
                           data-instance="{{ $instance->id }}"
                           data-label="{{ $state->label }}"
                           data-color="{{ $state->hexColor }}">
                            {{ $state->label }}
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>

    </div>



@endif