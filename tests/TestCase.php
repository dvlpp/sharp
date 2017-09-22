<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        $app->register(\Dvlpp\Sharp\SharpServiceProvider::class);

        return $app;
    }

    /**
     * Alias for seeInDatabase
     * 
     * @param  string $table
     * @param  array  $constraints]
     * @return static
     */
    protected function seeInDatabase($table, array $constraints)
    {
        return $this->assertDatabaseHas($table, $constraints);
    }

    /**
     * Alias for dontSeeInDatabase
     * 
     * @param  string $table
     * @param  array  $constraints]
     * @return static
     */
    protected function dontSeeInDatabase($table, array $constraints)
    {
        return $this->assertDatabaseMissing($table, $constraints);
    }
}
