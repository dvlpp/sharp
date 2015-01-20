<?php namespace Dvlpp\Sharp;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class SharpServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
        // Include Sharp's routes.php file
        include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Set config
		$this->app['config']->set([
			'dvlpp' => [
				'sharp' => [
					'cms' => include_once(__DIR__ . '/../../config/cms.php'),
					'site' => include_once(__DIR__ . '/../../config/site.php')
				]
			]
		]);

        // Register the SharpCmsField Facade used in cms views
		$this->app->bind("sharpCmsField", 'Dvlpp\Sharp\Form\SharpCmsField');

        // Register the SharpAdvancedSearchField Facade used in cms views
        $this->app->bind("sharpAdvancedSearchField", 'Dvlpp\Sharp\AdvancedSearch\SharpAdvancedSearchField');

        // Register the intervention/image dependency
//        $this->app->register('Intervention\Image\ImageServiceProvider');

        // Register the Illuminate/Html dependency (no more included in Laravel 5)
        $this->app->register('Illuminate\Html\HtmlServiceProvider');
        $loader = AliasLoader::getInstance();
        $loader->alias('Form', 'Illuminate\Html\FormFacade');
        $loader->alias('HTML', 'Illuminate\Html\HtmlFacade');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
