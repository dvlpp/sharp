<?php

use Dvlpp\Sharp\Config\FormFields\SharpPivotFormFieldConfig;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\PivotValuator;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PivotValuatorTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('database.default','sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        $this->migrateDatabase();
        $this->initModelFactories();
    }

    /** @test */
    public function existing_pivot_values_are_added()
    {
        $post = factory(TestPivotPostModel::class)->create();
        $tagOne = factory(TestPivotTagModel::class)->create();
        $tagTwo = factory(TestPivotTagModel::class)->create();

        $sharpRepo = Mockery::mock(SharpCmsRepository::class);
        $pivotConfig = SharpPivotFormFieldConfig::create("tags", null);

        (new PivotValuator($post, "tags", [$tagOne->id, $tagTwo->id], $pivotConfig, $sharpRepo))
            ->valuate();

        $this->seeInDatabase('post_tag', ['post_id' => $post->id, 'tag_id' => $tagOne->id]);
        $this->seeInDatabase('post_tag', ['post_id' => $post->id, 'tag_id' => $tagTwo->id]);
    }

    /** @test */
    public function new_pivot_value_is_created_if_addable()
    {
        $post = factory(TestPivotPostModel::class)->create();

        $sharpRepo = Mockery::mock(SharpCmsRepository::class);
        $pivotConfig = SharpPivotFormFieldConfig::create("tags", null)
            ->setAddable(true)
            ->setCreateAttribute("name");

        (new PivotValuator($post, "tags", ["#new"], $pivotConfig, $sharpRepo))
            ->valuate();

        $this->seeInDatabase('tags', ['name' => "new"]);
        $this->seeInDatabase('post_tag', ['post_id' => $post->id, 'tag_id' => 1]);
    }

    /** @test */
    public function missing_pivot_value_is_removed_if_addable()
    {
        $post = factory(TestPivotPostModel::class)->create();
        $tag = factory(TestPivotTagModel::class)->create();

        $post->tags()->sync([$tag->id]);

        $sharpRepo = Mockery::mock(SharpCmsRepository::class);
        $pivotConfig = SharpPivotFormFieldConfig::create("tags", null);

        (new PivotValuator($post, "tags", [], $pivotConfig, $sharpRepo))
            ->valuate();

        $this->dontSeeInDatabase('post_tag', ['post_id' => $post->id]);
    }

    /** @test */
    public function pivot_values_order_is_managed()
    {
        $post = factory(TestPivotPostModel::class)->create();
        $tagOne = factory(TestPivotTagModel::class)->create();
        $tagTwo = factory(TestPivotTagModel::class)->create();

        $post->tags()->sync([$tagOne->id, $tagTwo->id]);

        $sharpRepo = Mockery::mock(SharpCmsRepository::class);
        $pivotConfig = SharpPivotFormFieldConfig::create("tags", null)
            ->setSortable(true)
            ->setOrderAttribute("order");

        (new PivotValuator($post, "tags", [$tagTwo->id, $tagOne->id], $pivotConfig, $sharpRepo))
            ->valuate();

        $this->seeInDatabase('post_tag', ['post_id'=>$post->id, 'tag_id'=>$tagOne->id, 'order'=>2]);
        $this->seeInDatabase('post_tag', ['post_id'=>$post->id, 'tag_id'=>$tagTwo->id, 'order'=>1]);
    }

    private function migrateDatabase()
    {
        \Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        \Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string("name");
            $table->timestamps();
        });

        \Schema::create('post_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("post_id") ->unsigned();
            $table->integer("tag_id") ->unsigned();
            $table->integer("order") ->unsigned()->nullable();
        });
    }

    private function initModelFactories()
    {
        $modelFactory = app(Factory::class);

        $modelFactory->define(TestPivotPostModel::class, function (Faker\Generator $faker) {
            return [];
        });

        $modelFactory->define(TestPivotTagModel::class, function (Faker\Generator $faker) {
            return [
                "name" => $faker->word
            ];
        });
    }
}

class TestPivotPostModel extends Model {
    protected $table = "posts";

    public function tags()
    {
        return $this->belongsToMany(TestPivotTagModel::class, "post_tag", "post_id", "tag_id")
            ->withPivot("order");
    }
}

class TestPivotTagModel extends Model {
    protected $table = "tags";
    protected $fillable = ["name"];
}