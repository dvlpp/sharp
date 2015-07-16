#Sharp Eloquent auto-updater

If you read the [Entity forms](docs/entity_form.md) chapter, you probably noticed that the update or create code to write in the entity repository can be tricky, or at least a bit long, especially when posting big entities with lists and tags and stuff.

The good news is that if you are using Eloquent for your models, you can probably let Sharp do almost everything. Let's see how.

1. [The auto-updater Trait magic](#magic)
2. [The file upload case](#file)
3. [List special config](#list)
3. [Pivot tags special config](#tags)
3. [Wait, this particular attribute is specific](#specific)



##<a name="magic"></a>  1. The auto-updater Trait magic

Here's how the giraffe repository is managing `update()` and `create()` methods:

```
function create(Array $data)
{
	$this->update(null, $data);
}
    
function update($id, Array $data)
{
	$giraffe = $id ? $this->find($id) : $this->newInstance();
	
	$this->updateEntity("africa", "giraffe", $giraffe, $data);
}
```

Pretty cool... So what is this `updateEntity()` method? Well, as you guessed, it came with a Trait usage:

`use Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterTrait;`

This Trait brings this `updateEntity()` method, which takes 4 mandatory arguments:

- the entity category (from the config file)
- the entity name
- the instance, either a new one (creation case) or an existing one (update)
- and, of course, the posted data.

First two arguments are useful to review the configuration and find out the type of each posted input, among other things. The 3rd and 4th are more obvious.

And... that's mostly it: this function does all the boring job. But it will sometimes need some help, as explained below.


##<a name="file"></a>  2. The file upload case

File uploads needs a little extra work: if you use the SharpEloquentRepositoryUpdaterTrait on a repository that manage an entity which contains file uploads, you need to implement a special interface: `SharpEloquentRepositoryUpdaterWithUploads`. This interface will require 3 methods:

```
function getFileUploadPath($instance, $attr)
{
	return $instance instanceof Photo
		? public_path("files/photos")
		: public_path("files/giraffes");
}
    
function updateFileUpload($instance, $attr, $file)
{
	$instance->$attr = $file;
}

function deleteFileUpload($instance, $attr)
{
	$instance->$attr = null;
}
```

The first method, `getFileUploadPath()`, must simply return the folder where to put a newly uploaded file. The current instance as well as attribute name are given.

In our example, I handle a quite complex case: if you remember, a giraffe can have a `portrait`, which in the database is a simple text field containing the file name, and a list of `photos`, which are designed to be reused: they have their own table and model, `Photo`. Now portraits pictures are stored in a `public/files/giraffes` folder, and photos in the `public/files/photos`. OK, let's move on:

The second method, `updateFileUpload()`, must do the real job, storing the photo. As you can see, it can be very easy: for both cases, given that the `$instance` parameter is either the `Giraffe` or the `Photo` (as a list item), we just have to set the attribute (which is `portrait` in the first case, and `file` in the second, but we don't care, we just reference the `$attr`parameter).  
Of course, it could be more complex in a real case, and that's why the sharp auto-updater won't manage the file storage: there's a hundred ways to do it.

Finally, the last method, `deleteFileUpload()`, is called when a file is deleted. It's basically the same process than the previous one.

Of course, it's easy to manage this in a project Trait, and code it only once, based on your file upload storage implementation choices.


##<a name="list"></a>  3. List special config

Lists offers two optional config parameters:

- `item_id_attribute` must be added if your items id field is not `id`;
- `order_attribute` is useful for sortable lists: if you write there the name of an integer order field, Sharp auto-updater will do the rest.

So, as an example:

```
"photos" => [
	"label" => "Photos",
	"type" => "list",
	"sortable" => true,
	"addable" => true,
	"removable" => true,
	"add_button_text" => "Add a photo",
	"item_id_attribute" => "id",
	"order_attribute" => "order",
	"item" => [ ... ]
]
```


##<a name="tags"></a> 4. Pivot tags special config

Very similarly to lists, pivot tags have also optional config parameters dedicated to the auto-updater:

- `order_attribute`, like list
- `create_attribute` is more specific: for pivot tags fields which authorize on-the-fly creation (with `addable` parameter), this must be filled with the model attribute name which will be updated with the string tag.


##<a name="specific"></a> 5. Wait, this particular attribute is specific

Of course, you will sometimes need extra code for some attributes, or you will maybe want to override the default behavior. Well, you want hooks, and there's one per attribute.

The rule is simple: if you have in your repo a method called `update[AttributeName]Attribute($instance, $value)`, the auto-updater will call it before doing anything if this particular attribute was sent. For instance, for our giraffe name, the method name would be `updateNameAttribute()`. Passed parameters are the instance, and the posted value. So:

```
function updateNameAttribute($instance, $value)
{
	// Do anything with the value
	return false;}
```

If the method returns false, the auto updater will skip to the next posted value. If true, the auto update process will continue just as planned.

For list items, the method name pattern is: `update[ListKey]List[AttributeName]Attribute`. For instance: `updatePhotosListLegendAttribute`. The instance parameter is the item object.

And in a [single relation case](entity_form.md#singlerelation), the `~` is replaced with a `_`.

To be exhaustive, there's one more: before create a new list item, Sharp will try to call a `create[ListKey]ListItem($instance)` method. The goal is different here: the method must return a new list item object, if its creation is specific. In our case, it's very useful (but in a rare case):

```
function createPhotosListItem($instance)
{
	return new Photo([
		"animal_id" => $instance->id,
		"animal_type" => 'Quincy\Sharp\Giraffe\Giraffe'
	]);}
```

In fact, if we let Sharp doing this alone, in this case, only `animal_id` would be valued, because there's no way it could guess the `animal_type` specific thing (for [polymorphic relation](http://laravel.com/docs/eloquent#polymorphic-relations)).

