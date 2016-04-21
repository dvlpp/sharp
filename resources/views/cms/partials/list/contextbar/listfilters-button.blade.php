@foreach($list->listFilterCurrents() as $listFilterKey=>$listFilterInstanceId)

    <div class="dropdown pull-right normal-mode">

        <a class="btn navbar-btn btn-sublist" data-toggle="dropdown" data-target="#">
            @if(is_null($listFilterInstanceId) || !isset($list->listFilterContents()[$listFilterKey][$listFilterInstanceId]))
                @if(sizeof($list->listFilterContents()[$listFilterKey]))
                    {{ array_first($list->listFilterContents()[$listFilterKey]) }}
                @else
                    -
                @endif

            @else
                {{ $list->listFilterContents()[$listFilterKey][$listFilterInstanceId] }}
            @endif

            <span class="caret"></span>
        </a>

        <ul class="dropdown-menu pull-right">

            @foreach($list->listFilterContents()[$listFilterKey] as $listFilterId => $listFilterValue)
                <li>
                    <a href="{{ route('sharp.cms.list', ["category"=>$category->key(), "entity"=>$entity->key(), "sub"=>$listFilterKey.".".$listFilterId]) }}">
                        {{ $listFilterValue }}
                    </a>
                </li>
            @endforeach

        </ul>

    </div>

@endforeach
