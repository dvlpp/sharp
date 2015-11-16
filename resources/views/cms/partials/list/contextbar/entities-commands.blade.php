@if(sizeof($commands = get_abilited_entities_list_commands($entity)))

    <div class="dropdown navbar-right normal-mode">

        <a class="btn navbar-btn" data-toggle="dropdown" data-target="#">
            <i class="fa fa-asterisk"></i>
        </a>

        <ul class="dropdown-menu pull-right">
            @foreach($commands as $commandKey => $command)
                <li>
                    <a class="command" data-command="{{ $commandKey }}"
                            href="{{ route('cms.listCommand', array_merge([$category->key(), $entity->key(), $commandKey], Input::all())) }}"
                            {!! $command->hasConfirmation() ? 'data-confirm="'.e($command->confirmMessage()).'"' : '' !!}>
                        @if($command->iconName())
                            <i class="fa fa-{{ $command->iconName() }}"></i>
                        @endif
                        {{ $command->label() }}
                    </a>
                </li>
            @endforeach
        </ul>

    </div>

@endif