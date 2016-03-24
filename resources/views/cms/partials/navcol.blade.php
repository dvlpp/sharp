<ul class="nav nav-pills nav-stacked">

    @foreach($category->entities() as $entityKey)

        @if(check_ability('list', $category->key(), $entityKey))

            <li class="{{ $entity->key() == $entityKey ? 'active' : '' }}">
                <a href="{{ route('sharp.cms.list', [$category->key(), $entityKey]) }}">
                    <i class="fa fa-{{ sharp_entity($category->key(), $entityKey)->icon() ?: 'file-o' }}"></i>
                    {{ sharp_entity($category->key(), $entityKey)->plural() }}
                </a>
            </li>

        @endif

    @endforeach

</ul>