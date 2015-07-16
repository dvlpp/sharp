#Repository and data management

So, here we are: we just finished to [write our giraffe config file](config.md), and we want to see all giraffes in a pretty list. Well, there's some classes to create, and the first one, the main one is the *repository*. Let's get started.

We defined, in the config file, that the repository should be `\Quincy\Sharp\Giraffe\Repository`. Let's create this class, then make it implement the `Dvlpp\Sharp\Repositories\SharpCmsRepository` interface, and create the mandatory methods. Here's what we get:

```
<?php namespace Quincy\Sharp\Giraffe;

use Dvlpp\Sharp\ListView\SharpEntitiesListParams;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;

class Repository implements SharpCmsRepository {

    /**
     * Find an instance with the given id.
     *
     * @param $id
     * @return mixed
     */
    function find($id)
    {
    }

    /**
     * List all instances, with optional sorting and search.
     *
     * @param \Dvlpp\Sharp\ListView\SharpEntitiesListParams $params
     * @return mixed
     */
    function listAll(SharpEntitiesListParams $params)
    {
    }

    /**
     * Paginate instances.
     *
     * @param $count
     * @param \Dvlpp\Sharp\ListView\SharpEntitiesListParams $params
     * @return mixed
     */
    function paginate($count, SharpEntitiesListParams $params)
    {
    }

    /**
     * Create a new instance for initial population of create form.
     *
     * @return mixed
     */
    function newInstance()
    {
    }

    /**
     * Persists an instance.
     *
     * @param array $data
     * @return mixed
     */
    function create(Array $data)
    {
    }

    /**
     * Update an instance.
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    function update($id, Array $data)
    {
    }

    /**
     * Delete an instance.
     *
     * @param $id
     * @return mixed
     */
    function delete($id)
    {
    }
}
```
A quite simple repository, with methods to:

- grab lists (`listAll()`, `paginate()`), 
- get instances (`find()` and `newInstance()`), 
- and some to perform updates (`update()`, `create()`, `delete()`).

## The model

What will our repository is going to manipulate? We need to create a model. Now this isn't the purpose of this documentation, or even of Sharp, model classes can be anything. BUT (this is a big but), it's going to be way easier if we use the Laravel standard, Eloquent. So, for this example, here is out Giraffe model:

```
<?php namespace Quincy\Sharp\Giraffe;

class Giraffe extends \Eloquent  {

    function photos()
    {
        return $this->morphMany('\Quincy\Sharp\Photo\Photo', 'animal');
    }

    public function zookeeper()
    {
        return $this->belongsTo('\Quincy\Sharp\Zookeeper\Zookeeper');
    }
} 
```

We have two relations:
- one morphMany, which is a [oneToMany polymorphic relation](http://laravel.com/docs/eloquent#polymorphic-relations) useful in file uploads cases (to get a photos table with all files linked to many different entities: giraffes, lions, even maybe a zoo department, ...)
- and one belongsTo that refers to a zookeeper responsible of this giraffe.

Ok, let's move on.

## Writing the repo for list view

*Note: for the purpose of this documentation, I choose to keep things simple. Of course, in real life, you can create interfaces for your repo and implementations, and auto resolve dependencies in constructors, given that Sharp will  always try to instantiate classes through Laravel IoC container.*

Let's fill the firsts method:

```
function find($id)
{
	return Giraffe::findOrFail($id);
}

function listAll(SharpEntitiesListParams $params)
{
	return Giraffe::all();
}
    
function paginate($count, SharpEntitiesListParams $params)
{
	throw new BadMethodCallException("Not implemented");
}

function newInstance()
{
	return new Giraffe();
}
```

I choose to throw an Exception on paginate since this method shouldn't be called (in our config, we valued "paginate" to false as a first step).

## Test it out

At this step, we can hit `domain.com/admin/cms/africa/giraffe` and see:

![](img/listview-giraffe-empty.png)

Clicking "Create new":

![](img/formview-giraffe-empty.png)

