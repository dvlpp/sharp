<?php

namespace Dvlpp\Sharp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CopyFileInStorage implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Queueable;

    /**
     * @var
     */
    private $src;
    /**
     * @var
     */
    private $dest;
    /**
     * @var string
     */
    private $srcDisk;
    /**
     * @var string
     */
    private $destDisk;

    /**
     * Create a new job instance.
     *
     * @param $src
     * @param $dest
     * @param string $srcDisk
     * @param string $destDisk
     */
    public function __construct($src, $dest, $srcDisk='local', $destDisk='local')
    {
        $this->src = $src;
        $this->dest = $dest;
        $this->srcDisk = $srcDisk;
        $this->destDisk = $destDisk;
    }

    /**
     * Execute the job.
     *
     * @param Factory $fileSystemManager
     */
    public function handle(Factory $fileSystemManager)
    {
        $fileSystemManager->disk($this->destDisk)->put(
            $this->dest,
            $fileSystemManager->disk($this->srcDisk)->get($this->src)
        );
    }
}