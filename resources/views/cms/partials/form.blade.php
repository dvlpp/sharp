@foreach($template->fields() as $fieldsetName => $fieldTemplate)

    @if(is_string($fieldsetName))
        {{--Fieldset--}}
        <div class="panel fieldset">
            <div class="panel-heading">
                <label class="control-label">{{ $fieldsetName }}</label>
            </div>
            <div class="panel-body">
                @foreach((array)$fieldTemplate as $row)
                    @include("sharp::cms.partials.formFieldsRow", [
                        "entity" => $entity,
                        "fields" => $row
                    ])
                @endforeach
            </div>
        </div>

    @else

        @include("sharp::cms.partials.formFieldsRow", [
            "entity" => $entity,
            "fields" => $fieldTemplate
        ])

    @endif

@endforeach
