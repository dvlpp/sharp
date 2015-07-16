@if($subList)
    <div class="dropdown pull-right normal-mode">
        <a class="btn navbar-btn btn-sublist" data-toggle="dropdown" data-target="#">
            {{ $subLists[$subList] }} <span class="caret"></span></a>
        <ul class="dropdown-menu pull-right">
            @foreach($subLists as $idsl => $sl)
                <li><a href="{{ URL::route('cms.list', ["category"=>$category->key, "entity"=>$entityKey, "sub"=>$idsl]) }}">{{ $sl }}</a></li>
            @endforeach
        </ul>
    </div>
@endif