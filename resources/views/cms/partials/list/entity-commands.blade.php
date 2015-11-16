@if(sizeof($commands = get_abilited_entities_entity_commands($entity, $instance->id)))

    @foreach($commands as $commandKey => $command)
        <li>
            <a class="command" data-command="{{ $commandKey }}"
                    href="{{ route('cms.entityCommand', [$category->key(), $entity->key(), $commandKey, $instance->id]) }}"
                    {!! $command->hasConfirmation() ? 'data-confirm="'.e($command->confirmMessage()).'"' : '' !!}>
                @if($command->iconName())
                    <i class="fa fa-{{ $command->iconName() }}"></i>
                @endif
                {{ $command->label() }}
            </a>
        </li>
    @endforeach

@endif