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

	public function boot()
	{
		// TODO remodeler structure répertoires (ajouter /ressources/views, /ressources/lang, ...)

		$this->loadViewsFrom(__DIR__ . '/../../views', 'sharp');
		$this->loadTranslationsFrom(__DIR__ . '/../../lang', 'sharp');

		// Publish config
		$this->publishes([
			__DIR__.'/../../config/sharp.php' => config_path('sharp.php')
		], 'config');

		// Publish assets
		$this->publishes([
			__DIR__.'/../../../public/css/sharp.min.css' => public_path('sharp/sharp.min.css'),

			__DIR__.'/../../../public/js/sharp.ui.min.js' => public_path('sharp/sharp.ui.min.js'),
			__DIR__.'/../../../public/js/sharp.form.min.js' => public_path('sharp/sharp.form.min.js'),
			__DIR__.'/../../../public/js/sharp.advancedsearch.min.js' => public_path('sharp/sharp.advancedsearch.min.js'),

			__DIR__.'/../../../public/bower_components/jquery/dist/jquery.min.js' => public_path('sharp/vendor/jquery.min.js'),
			__DIR__.'/../../../public/bower_components/bootstrap/dist/js/bootstrap.min.js' => public_path('sharp/vendor/bootstrap.min.js'),
			__DIR__.'/../../../public/js/vendor/jquery-ui-1.10.4.custom.min.js' => public_path('sharp/vendor/jquery-ui-1.10.4.custom.min.js'),

			__DIR__.'/../../../public/bower_components/fontawesome/fonts/fontawesome-webfont.eot' => public_path('sharp/fonts/fontawesome-webfont.eot'),
			__DIR__.'/../../../public/bower_components/fontawesome/fonts/fontawesome-webfont.svg' => public_path('sharp/fonts/fontawesome-webfont.svg'),
			__DIR__.'/../../../public/bower_components/fontawesome/fonts/fontawesome-webfont.ttf' => public_path('sharp/fonts/fontawesome-webfont.ttf'),
			__DIR__.'/../../../public/bower_components/fontawesome/fonts/fontawesome-webfont.woff' => public_path('sharp/fonts/fontawesome-webfont.woff'),

			__DIR__.'/../../../public/bower_components/bootstrap/fonts/glyphicons-halflings-regular.eot' => public_path('sharp/fonts/glyphicons-halflings-regular.eot'),
			__DIR__.'/../../../public/bower_components/bootstrap/fonts/glyphicons-halflings-regular.svg' => public_path('sharp/fonts/glyphicons-halflings-regular.svg'),
			__DIR__.'/../../../public/bower_components/bootstrap/fonts/glyphicons-halflings-regular.ttf' => public_path('sharp/fonts/glyphicons-halflings-regular.ttf'),
			__DIR__.'/../../../public/bower_components/bootstrap/fonts/glyphicons-halflings-regular.woff' => public_path('sharp/fonts/glyphicons-halflings-regular.woff'),

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

        // Register the SharpAdvancedSearchField Facade used in cms views
        $this->app->bind("sharpAdvancedSearchField", 'Dvlpp\Sharp\AdvancedSearch\SharpAdvancedSearchField');

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
