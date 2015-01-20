<?php namespace Dvlpp\Sharp;

use Illuminate\Foundation\AliasLoader;
use Orchestra\Support\Providers\ServiceProvider;
use ReflectionClass;

class SharpServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	public function boot()
	{
		$this->loadViewsFrom("sharp", $this->guessPackagePath() . '/views');
		$this->loadTranslationsFrom("sharp", $this->guessPackagePath() . '/lang');
		$this->addConfigComponent('dvlpp/sharp', 'dvlpp/sjarp', realpath(__DIR__.'/../config'));

//		$this->setConfig(["cms", "site"]);

		dd($this->app['config']);

		// Include Sharp's routes.php file
		include __DIR__.'/../../routes.php';
	}

	protected function guessPackagePath()
	{
		$path = (new ReflectionClass($this))->getFileName();

		return realpath(dirname($path).'/../../');
	}

//	protected function setConfig($configs)
//	{
//		if( ! is_array($configs)) $configs = [$configs];
//
//		foreach($configs as $configFile)
//		{
//			$path = $this->guessPackagePath() . "/config/$configFile.php";
//			$config = require $path;
//
//			foreach ($config as $key => $value)
//			{
//				$this->app['config']->set("sharp::$configFile.$key", $value);
//			}
//		}
//	}

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
