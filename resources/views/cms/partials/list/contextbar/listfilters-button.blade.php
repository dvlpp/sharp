@foreach($listFilters["currents"] as $listFilterKey=>$listFilterInstanceId)

    <div class="dropdown pull-right normal-mode">

        <a class="btn navbar-btn btn-sublist" data-toggle="dropdown" data-target="#">
            {{ $listFilters["contents"][$listFilterKey][$listFilterInstanceId] }}
            <span class="caret"></span>
        </a>

        <ul class="dropdown-menu pull-right">

            @foreach($listFilters["contents"][$listFilterKey] as $listFilterId => $listFilterValue)
                <li>
                    <a href="{{ route('cms.list', ["category"=>$category->key, "entity"=>$entityKey, "sub"=>$listFilterKey.".".$listFilterId]) }}">
                        {{ $listFilterValue }}
                    </a>
                </li>
            @endforeach

        </ul>

    </div>

@endforeach
