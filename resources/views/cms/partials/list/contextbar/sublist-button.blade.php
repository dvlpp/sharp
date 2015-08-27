@if($subList)
    <div class="dropdown pull-right normal-mode">

        <a class="btn navbar-btn btn-sublist" data-toggle="dropdown" data-target="#">
            {{ $subLists[$subList] }} <span class="caret"></span>
        </a>

        <ul class="dropdown-menu pull-right">

            @foreach($subLists as $sublistId => $sublistName)
                <li>
                    <a href="{{ route('cms.list', ["category"=>$category->key, "entity"=>$entityKey, "sub"=>$sublistId]) }}">
                        {{ $sublistName }}
                    </a>
                </li>
            @endforeach

        </ul>

    </div>
@endif