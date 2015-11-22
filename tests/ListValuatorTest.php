<?php

use Dvlpp\Sharp\Config\FormFields\ListField\SharpListFormFieldConfig;
use Dvlpp\Sharp\Config\FormFields\ListField\SharpListItemFormTemplateConfig;
use Dvlpp\Sharp\Config\FormFields\SharpTextareaFormFieldConfig;
use Dvlpp\Sharp\Repositories\AutoUpdater\SharpEloquentAutoUpdaterService;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\ListValuator;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ListValuatorTest extends TestCase
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
    public function existing_item_is_updated()
    {
        $post = factory(TestListPostModel::class)->create();
        $comment = factory(TestListCommentModel::class)->create([
            "post_id" => $post->id
        ]);

        $sharpRepo = Mockery::mock(SharpCmsRepository::class);
        $listConfig = SharpListFormFieldConfig::create("comments")
            ->addItemFormField(
                SharpTextareaFormFieldConfig::create("body")
            );
        $autoUpdater = new SharpEloquentAutoUpdaterService();

        (new ListValuator($post, "comments", [
            $comment->id => [
                "id" => $comment->id,
                "body" => "some test body"
            ]
        ], $listConfig, $sharpRepo, $autoUpdater))
            ->valuate();

        $this->seeInDatabase('comments', ['post_id' => $post->id, 'body' => "some test body"]);
    }

    /** @test */
    public function new_item_is_added()
    {
        $post = factory(TestListPostModel::class)->create();

        $sharpRepo = Mockery::mock(SharpCmsRepository::class);
        $listConfig = SharpListFormFieldConfig::create("comments")
            ->setAddable(true)
            ->addItemFormField(
                SharpTextareaFormFieldConfig::create("body")
            );
        $autoUpdater = new SharpEloquentAutoUpdaterService();

        (new ListValuator($post, "comments", [
            "N_1" => [
                "id" => "N_1",
                "body" => "some test body"
            ]
        ], $listConfig, $sharpRepo, $autoUpdater))
            ->valuate();

        $this->seeInDatabase('comments', ['post_id' => $post->id, 'body' => "some test body"]);
    }

    /** @test */
    public function missing_item_is_removed()
    {
        $post = factory(TestListPostModel::class)->create();
        $comment = factory(TestListCommentModel::class)->create([
            "post_id" => $post->id
        ]);

        $sharpRepo = Mockery::mock(SharpCmsRepository::class);
        $listConfig = SharpListFormFieldConfig::create("comments")
            ->setRemovable(true);
        $autoUpdater = new SharpEloquentAutoUpdaterService();

        (new ListValuator($post, "comments", [], $listConfig, $sharpRepo, $autoUpdater))
            ->valuate();

        $this->dontSeeInDatabase('comments', ['post_id' => $post->id]);
    }

    /** @test */
    public function list_items_order_is_managed()
    {
        $post = factory(TestListPostModel::class)->create();
        $commentOne = factory(TestListCommentModel::class)->create([
            "post_id" => $post->id,
            "order" => 1
        ]);
        $commentTwo = factory(TestListCommentModel::class)->create([
            "post_id" => $post->id,
            "order" => 2
        ]);

        $sharpRepo = Mockery::mock(SharpCmsRepository::class);
        $listConfig = SharpListFormFieldConfig::create("comments")
            ->setSortable(true)
            ->setOrderAttribute("order");
        $autoUpdater = new SharpEloquentAutoUpdaterService();

        (new ListValuator($post, "comments", [
            $commentTwo->id => ["id" => $commentTwo->id],
            $commentOne->id => ["id" => $commentOne->id],
        ], $listConfig, $sharpRepo, $autoUpdater))
            ->valuate();

        $this->seeInDatabase('comments', ['post_id'=>$post->id, 'id'=>$commentOne->id, 'order'=>2]);
        $this->seeInDatabase('comments', ['post_id'=>$post->id, 'id'=>$commentTwo->id, 'order'=>1]);
    }

    private function migrateDatabase()
    {
        \Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        \Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("post_id") ->unsigned();
            $table->text("body");
            $table->integer("order") ->unsigned()->nullable();
            $table->timestamps();
        });
    }

    private function initModelFactories()
    {
        $modelFactory = app(Factory::class);

        $modelFactory->define(TestListPostModel::class, function (Faker\Generator $faker) {
            return [];
        });

        $modelFactory->define(TestListCommentModel::class, function (Faker\Generator $faker) {
            return [
                "body" => $faker->sentence,
                "order" => $faker->randomNumber()
            ];
        });
    }
}

class TestListPostModel extends Model {
    protected $table = "posts";

    public function comments()
    {
        return $this->hasMany(TestListCommentModel::class, "post_id");
    }
}

class TestListCommentModel extends Model {
    protected $table = "comments";

    public function post()
    {
        return $this->belongsTo(TestListPostModel::class, "post_id");
    }
}