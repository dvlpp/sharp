@if(sizeof($commands = get_abilited_entities_entity_commands($category, $entity, $instance->id)))

    @foreach($commands as $commandKey => $command)
        <li>
            <a class="command" data-command="{{ $commandKey }}"
                    href="{{ route('cms.entityCommand', [$category->key, $entity->key, $commandKey, $instance->id]) }}"
                    {!! $command->confirm ? 'data-confirm="'.e($command->confirm).'"' : '' !!}>
                @if($command->icon)
                    <i class="fa fa-{{ $command->icon }}"></i>
                @endif
                {{ $command->text }}
            </a>
        </li>
    @endforeach

@endif