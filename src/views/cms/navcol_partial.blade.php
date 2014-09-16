<ul class="nav nav-pills nav-stacked">
    @foreach($category->entities as $eKey)
        @if(!$category->entities->$eKey->embedded && \Dvlpp\Sharp\Auth\SharpAccessManager::granted('entity', 'list', $eKey))
            <li class="{{ $entity->key == $eKey ? 'active' : '' }}">
                <a href="{{ route('cms.list', [$category->key, $eKey]) }}">
                    <i class="fa fa-{{ $category->entities->$eKey->icon ?: 'file-o' }}"></i>
                    {{ $category->entities->$eKey->plural }}
                </a>
            </li>
        @endif
    @endforeach
</ul>