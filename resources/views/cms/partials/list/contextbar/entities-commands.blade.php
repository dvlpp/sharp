@if(sizeof($entity->commands->data) && sizeof($entity->commands->list->data) && sharp_granted('entity', 'update', $entityKey))

    <div class="dropdown navbar-right normal-mode">

        <a class="btn navbar-btn" data-toggle="dropdown" data-target="#"><i class="fa fa-caret-down"></i></a>

        <ul class="dropdown-menu pull-right">
            @foreach($entity->commands->list as $command)
                <li>
                    <a href="{{ route('cms.listCommand', array_merge([$category->key, $entityKey, $command], Input::all())) }}" {{ $entity->commands->list->$command->type=="view" ? 'target="_blank"' : ''}}>
                        {{ $entity->commands->list->$command->text }}
                    </a>
                </li>
            @endforeach
        </ul>

    </div>

@endif