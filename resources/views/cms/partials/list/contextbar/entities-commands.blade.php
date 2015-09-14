@if(sizeof($entity->commands->data) && sizeof($entity->commands->list->data))

    <div class="dropdown navbar-right normal-mode">

        <a class="btn navbar-btn" data-toggle="dropdown" data-target="#"><i class="fa fa-caret-down"></i></a>

        <ul class="dropdown-menu pull-right">
            @foreach($entity->commands->list as $command)

                @if(sharp_granted('entity', $entity->commands->list->$command->auth ?: "update", $entity->key))
                    <li>
                        <a href="{{ route('cms.listCommand', array_merge([$category->key, $entity->key, $command], Input::all())) }}"
                                {!! $entity->commands->list->$command->confirm ? 'data-confirm="'.e($entity->commands->list->$command->confirm).'"' : '' !!}
                                {!! $entity->commands->list->$command->type=="view" ? 'target="_blank"' : '' !!}>
                            {{ $entity->commands->list->$command->text }}
                        </a>
                    </li>
                @endif

            @endforeach
        </ul>

    </div>

@endif