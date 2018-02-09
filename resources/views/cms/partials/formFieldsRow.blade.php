<div class="row">

    @foreach((array)$fields as $field)

        <?php
            if(strpos($field, ":")) {
                list($field, $size) = explode(":", $field);
            } else {
                $size = 12/sizeof((array)$fields);
            }
        ?>

        <div class="col-sm-{{ $size }}">

            @include("sharp::cms.partials.formField", [
                "field" => $entity->findField($field)
            ])

        </div>

    @endforeach

</div>