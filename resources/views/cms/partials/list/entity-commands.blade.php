@if(sizeof($commands = get_abilited_entities_entity_commands($category, $entity, $instance->id)))

    <div class="btn dropdown">

        <a data-toggle="dropdown" data-target="#">
            <i class="fa fa-ellipsis-h"></i>
        </a>

        <ul class="dropdown-menu pull-right">
            @foreach($commands as $commandKey => $command)
                <li>
                    <a class="command" href="{{ route('cms.entityCommand', [$category->key, $entity->key, $commandKey, $instance->id]) }}"
                            {!! $command->confirm ? 'data-confirm="'.e($command->confirm).'"' : '' !!}>
                        @if($command->icon)
                            <i class="fa fa-{{ $command->icon }}"></i>
                        @endif
                        {{ $command->text }}
                    </a>
                </li>
            @endforeach
        </ul>

    </div>

@endif