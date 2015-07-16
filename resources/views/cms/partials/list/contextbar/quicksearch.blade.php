@if($entity->list_template->searchable)

    <form role="search" class="navbar-form navbar-right normal-mode" id="search" method="get">

        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="{{ trans('sharp::ui.list_searchPlaceholder') }}" value="{{ Input::get('search') }}">
            <span class="input-group-btn">
                <button class="btn" type="submit"><i class="fa fa-search"></i></button>
            </span>
        </div>

        @foreach(Input::only(['sort','dir','sub']) as $qs => $qsVal)
            {!! Form::hidden($qs, $qsVal) !!}
        @endforeach

    </form>

@endif