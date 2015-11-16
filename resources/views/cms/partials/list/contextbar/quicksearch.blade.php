@if($entity->searchable())

    {!! Form::open(["class"=>"navbar-form navbar-right normal-mode search-field", "method"=>"get"]) !!}

        <div class="input-group">
            {!! Form::text("search", Input::get("search"), ["class"=>"form-control", "placeholder" => trans('sharp::ui.list_searchPlaceholder')]) !!}
            <span class="input-group-btn">
                <button class="btn" type="submit"><i class="fa fa-search"></i></button>
            </span>
        </div>

        @foreach(Input::only(['sort','dir','sub']) as $qs => $qsVal)
            {!! Form::hidden($qs, $qsVal) !!}
        @endforeach

    {!! Form::close() !!}

@endif