@if(count($form->tabs()) > 1)

    <ul class="nav nav-pills entity-tabs" role="tablist">
        @foreach($form->tabs() as $k => $tab)
            <li class="{{ $k==0?'active':'' }}">
                <a href="#tab{{ $k }}">{{ $tab->label() }}</a>
            </li>
        @endforeach
    </ul>

@endif


@foreach($form->tabs() as $tab)

    <div class="tab-pane {{ $k==0?'active':'' }}" id="tab{{ $k }}">

        @if($form->customView())
            @include($form->customView(), ["form" => $form])

        @else
            @foreach($form->columns() as $column)

                @include("sharp::cms.partials.formColumn", ["column" => $column])

            @endforeach

        @endif

    </div>

@endforeach