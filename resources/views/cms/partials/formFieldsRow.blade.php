<?php
    if(strpos($key, ":")) {
        list($key, $size) = explode(":", $key);
    } else {
        $size = 12/sizeof($cols);
    }
?>

@include("sharp::cms.partials.formField", [
    "field" => $entity->form_fields->$key,
    "size" => $size
])