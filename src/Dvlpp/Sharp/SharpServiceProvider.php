<?php

namespace Dvlpp\Sharp;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class SharpServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	public function boot()
	{
		$this->loadViewsFrom(__DIR__ . '/../../../resources/views', 'sharp');
		$this->loadTranslationsFrom(__DIR__ . '/../../../resources/lang', 'sharp');

		// Publish config
		$this->publishes([
			__DIR__.'/../../../resources/config/sharp.php' => config_path('sharp.php')
		], 'config');

		// Publish assets
		$this->publishes([
			__DIR__.'/../../../resources/assets/sharp.min.css' => public_path('sharp/sharp.min.css'),

			__DIR__.'/../../../resources/assets/sharp.ui.min.js' => public_path('sharp/sharp.ui.min.js'),
			__DIR__.'/../../../resources/assets/sharp.form.min.js' => public_path('sharp/sharp.form.min.js'),

			__DIR__.'/../../../resources/assets/fonts/fontawesome-webfont.eot' => public_path('sharp/fonts/fontawesome-webfont.eot'),
			__DIR__.'/../../../resources/assets/fonts/fontawesome-webfont.svg' => public_path('sharp/fonts/fontawesome-webfont.svg'),
			__DIR__.'/../../../resources/assets/fonts/fontawesome-webfont.ttf' => public_path('sharp/fonts/fontawesome-webfont.ttf'),
			__DIR__.'/../../../resources/assets/fonts/fontawesome-webfont.woff' => public_path('sharp/fonts/fontawesome-webfont.woff'),
			__DIR__.'/../../../resources/assets/fonts/fontawesome-webfont.woff2' => public_path('sharp/fonts/fontawesome-webfont.woff2'),

		], 'assets');

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
        // Register the SharpCmsField Facade used in cms views
		$this->app->bind("sharpCmsField", 'Dvlpp\Sharp\Form\SharpCmsField');

        // Register the Illuminate/Html dependency (no more included in Laravel 5)
        $this->app->register('Collective\Html\HtmlServiceProvider');
        $loader = AliasLoader::getInstance();
        $loader->alias('Form', 'Collective\Html\FormFacade');
        $loader->alias('HTML', 'Collective\Html\HtmlFacade');
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
