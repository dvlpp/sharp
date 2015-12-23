<?php

use Dvlpp\Sharp\Config\FormFields\SharpDateFormFieldConfig;
use Dvlpp\Sharp\Config\FormFields\SharpTextareaFormFieldConfig;
use Dvlpp\Sharp\Config\FormFields\SharpTextFormFieldConfig;
use Dvlpp\Sharp\Config\SharpEntityConfig;
use Dvlpp\Sharp\Repositories\AutoUpdater\SharpEloquentAutoUpdaterService;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AutoUpdaterTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('database.default','sqlite');
        $this->app['config']->set('database.connections.sqlite.database', ':memory:');

        $this->migrateDatabase();
        $this->initModelFactories();

        $this->app->bind("sharp.test.post", SharpCategoryConfigTestAutoUpdaterPost::class);
    }

    /** @test */
    public function it_updates_a_full_model()
    {
        $post = factory(TestAutoUpdaterPostModel::class)->create([]);
        $now = date("Y-m-d H:i:s");

        $data = [
            "title" => "Post title",
            "body" => "Post body",
            "date" => $now
        ];

        $cmsRepository = Mockery::mock(TestAutoUpdaterRepositoryUpdate::class);

        $autoUpdater = new SharpEloquentAutoUpdaterService();
        $autoUpdater->updateEntity($cmsRepository, "test", "post", $post, $data);

        $this->seeInDatabase('posts', [
            'id'=>$post->id,
            'title'=>"Post title",
            'body'=>"Post body",
            "date"=>$now
        ]);
    }

    private function migrateDatabase()
    {
        \Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string("title");
            $table->text("body");
            $table->dateTime("date");
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

        $modelFactory->define(TestAutoUpdaterPostModel::class, function (Faker\Generator $faker) {
            return [
                "title" => $faker->sentence,
                "body" => $faker->paragraph,
                "date" => $faker->date()
            ];
        });

        $modelFactory->define(TestAutoUpdaterCommentModel::class, function (Faker\Generator $faker) {
            return [
                "body" => $faker->sentence,
                "order" => $faker->randomNumber()
            ];
        });
    }
}

class TestAutoUpdaterPostModel extends Model {
    protected $table = "posts";

    public function comments()
    {
        return $this->hasMany(TestAutoUpdaterCommentModel::class, "post_id");
    }
}

class TestAutoUpdaterCommentModel extends Model {
    protected $table = "comments";

    public function post()
    {
        return $this->belongsTo(TestAutoUpdaterPostModel::class, "post_id");
    }
}

abstract class TestAutoUpdaterRepositoryUpdate implements SharpCmsRepository {

    use \Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterTrait;

    function update($id, Array $data)
    {
        $post = TestAutoUpdaterPostModel::find($id);
        $this->updateEntity("test", "post", $post, $data);
    }

}

class SharpCategoryConfigTestAutoUpdaterPost extends SharpEntityConfig
{
    public function formFieldsConfig()
    {
        return [
            SharpTextFormFieldConfig::create("title"),
            SharpTextareaFormFieldConfig::create("body"),
            SharpDateFormFieldConfig::create("date")
        ];
    }
    function buildListTemplate() {}
    function buildFormFields() {}
}