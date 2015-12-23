<?php

use Dvlpp\Sharp\Config\FormFields\SharpFileFormFieldConfig;
use Dvlpp\Sharp\Repositories\AutoUpdater\Valuators\FileValuator;
use Dvlpp\Sharp\Repositories\SharpCmsRepository;
use Dvlpp\Sharp\Repositories\SharpEloquentRepositoryUpdaterWithUploads;
use Illuminate\Contracts\Filesystem\Factory;

class FileValuatorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        config([
            // Sharp needed configs for files
            "sharp.upload_tmp_base_path" => "work/tmp",
            "sharp.upload_storage_base_path" => "work/storage",
            "sharp.upload_storage_disk" => "local",
            "sharp.file_queue_name" => "test_queue",

            // Change FileSystem root
            "filesystems.disks.local.root" => __DIR__ . "/../fixtures",
            "app.key" => "aabbccffaabbccffaabbccffaabbccff"
        ]);

        // Make public path leading to a test working directory
        $this->app->bind('path.public', function() {
            return __DIR__ . "/../fixtures/work/public";
        });

        // Cleanup working directories from test files
        app(Factory::class)->disk("local")->deleteDirectory("work");
        app(Factory::class)->disk("local")->makeDirectory("work/tmp");
        app(Factory::class)->disk("local")->makeDirectory("work/storage");
        app(Factory::class)->disk("local")->makeDirectory("work/public");
    }

    /** @test */
    public function new_file_is_uploaded_and_updated()
    {
        $instance = new FileValuatorTestEntity;
        $sharpRepo = Mockery::mock(FileValuatorTestEntityWithUploadsRepository::class);
        touch(__DIR__ . "/../fixtures/work/tmp/file.txt");

        $sharpRepo->shouldReceive('getStorageDirPath')->andReturn("/");

        $sharpRepo->shouldReceive('updateFileUpload')->once()->withArgs([
            $instance, "file", [
                "path" => "work/storage/file.txt",
                "mime" => "inode/x-empty",
                "size" => 0
            ]
        ]);

        (new FileValuator($instance, "file", "file.txt", $this->fileConfig(), $sharpRepo))
            ->valuate();

        $this->assertFileExists(__DIR__ . "/../fixtures/work/storage/file.txt");
    }

    /** @test */
    public function thumbnails_are_generated()
    {
        $instance = new FileValuatorTestEntity();
        $sharpRepo = Mockery::mock(FileValuatorTestEntityWithUploadsRepository::class);
        copy(__DIR__ . '/../fixtures/image.jpg', __DIR__ . "/../fixtures/work/tmp/image.jpg");

        $sharpRepo->shouldReceive('getStorageDirPath')->andReturn("/");

        $sharpRepo->shouldReceive('updateFileUpload')->once()->withArgs([
            $instance, "image", [
                "path" => "work/storage/image.jpg",
                "mime" => "image/jpeg",
                "size" => filesize(__DIR__ . '/../fixtures/image.jpg')
            ]
        ]);

        (new FileValuator($instance, "image", "image.jpg", $this->imageConfig(), $sharpRepo))
            ->valuate();

        $this->assertFileExists(__DIR__ . "/../fixtures/work/public/50-50/image.jpg");
        $this->assertFileExists(__DIR__ . "/../fixtures/work/public/100-100/image.jpg");
    }

    /** @test */
    public function file_is_deleted()
    {
        $instance = new FileValuatorTestEntity();
        $instance->file = "somefile";
        $sharpRepo = Mockery::mock(FileValuatorTestEntityWithUploadsRepository::class);

        $sharpRepo->shouldReceive('getStorageDirPath')->andReturn("/");

        $sharpRepo->shouldReceive('deleteFileUpload')->once()->withArgs([
            $instance, "file"
        ]);

        (new FileValuator($instance, "file", null, $this->fileConfig(), $sharpRepo))
            ->valuate();
    }

    private function fileConfig()
    {
        return SharpFileFormFieldConfig::create("file");
    }

    private function imageConfig()
    {
        return SharpFileFormFieldConfig::create("image")
            ->setThumbnail("50x50")
            ->addGeneratedThumbnail("100x100");
    }
}

abstract class FileValuatorTestEntityWithUploadsRepository implements
    SharpCmsRepository,
    SharpEloquentRepositoryUpdaterWithUploads {}

class FileValuatorTestEntity extends \Illuminate\Database\Eloquent\Model {}