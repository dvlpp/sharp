@if(check_ability('delete', $category->key(), $entity->key(), $instance->id))
    <li>
        {!! Form::open(["route"=>["sharp.cms.destroy", $category->key(), $entity->key(), $instance->id], "method"=>"DELETE", "id"=>"sharpdelete".$instance->id]) !!}
        {!! Form::close() !!}
        <a href="#" class="sharp-delete" data-form="{{ "sharpdelete".$instance->id }}" data-confirmdelete="{{ trans('sharp::ui.form_deleteConfirmMsg') }}">
            <i class="fa fa-trash"></i>
            {{ trans("sharp::ui.list_entityDeleteAction") }}
        </a>
    </li>
@endif