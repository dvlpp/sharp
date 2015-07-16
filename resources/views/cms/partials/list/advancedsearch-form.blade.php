@if($entity->advanced_search->data)

    <div id="advsearch_panel" class="panel-collapse collapse {{ Input::has("adv") ? 'in' : '' }}">
        <form id="advsearch" method="get" class="">

            @foreach($entity->advanced_search->rows as $advsrow)

                @include("sharp::cms.partials.advancedsearch.row", ["row" => $entity->advanced_search->rows->$advsrow])

            @endforeach

            @foreach(Input::only(['sort','dir','sub']) as $qs => $qsVal)
                {!! Form::hidden($qs, $qsVal) !!}
            @endforeach

            {!! Form::hidden('adv', true) !!}

            <div class="clearfix">
                <div class="col-sm-2 col-sm-offset-3">
                    <button type="submit" class="btn btn-block"><i class='fa fa-search'></i> {{ trans('sharp::ui.list_advancedSearchBtn') }}</button>
                </div>
            </div>

        </form>
    </div>

    <a class="advancedsearch-toggle" data-toggle="collapse" href="#advsearch_panel">
        Show / hide advanced search
    </a>

@endif