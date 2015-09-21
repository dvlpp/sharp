@if(sizeof($commands = get_abilited_entities_list_commands($category, $entity)))

    <div class="dropdown navbar-right normal-mode">

        <a class="btn navbar-btn" data-toggle="dropdown" data-target="#">
            <i class="fa fa-ellipsis-h"></i>
        </a>

        <ul class="dropdown-menu pull-right">
            @foreach($commands as $commandKey => $command)
                <li>
                    <a class="command" data-command="{{ $commandKey }}"
                            href="{{ route('cms.listCommand', array_merge([$category->key, $entity->key, $commandKey], Input::all())) }}"
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