<?php
/**
 * Laravel 4 - Persistant Newsmodulesettings
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-Newsmodulesettings
 */

namespace jaapgoorhuis\LaravelNewsmodulesettings;

use Illuminate\Foundation\Application;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	/**
	 * This provider is deferred and should be lazy loaded.
	 *
	 * @var boolean
	 */
	protected $defer = true;

	/**
	 * Register IoC bindings.
	 */
	public function register()
	{
		$method = version_compare(Application::VERSION, '5.2', '>=') ? 'singleton' : 'bindShared';

		// Bind the manager as a singleton on the container.
		$this->app->$method('jaapgoorhuis\LaravelNewsmodulesettings\NewsmodulesettingsManager', function($app) {
			// When the class has been resolved once, make sure that settings
			// are saved when the application shuts down.
			if (version_compare(Application::VERSION, '5.0', '<')) {
				$app->shutdown(function($app) {
					$app->make('jaapgoorhuis\LaravelNewsmodulesettings\NewsmodulesettingStore')->save();
				});
			}

			/**
			 * Construct the actual manager.
			 */
			return new NewsmodulesettingsManager($app);
		});

		// Provide a shortcut to the SettingStore for injecting into classes.
		$this->app->bind('jaapgoorhuis\LaravelNewsmodulesettings\NewsmodulesettingStore', function($app) {
			return $app->make('jaapgoorhuis\LaravelNewsmodulesettings\NewsmodulesettingsManager')->driver();
		});

		if (version_compare(Application::VERSION, '5.0', '>=')) {
			$this->mergeConfigFrom(__DIR__ . '/config/config.php', 'settings');
		}
	}

	/**
	 * Boot the package.
	 */
	public function boot()
	{
		if (version_compare(Application::VERSION, '5.0', '>=')) {
			$this->publishes([
				__DIR__.'/config/config.php' => config_path('newsmodulesettings.php')
			], 'config');
			$this->publishes([
				__DIR__.'/migrations' => database_path('migrations')
			], 'migrations');
		} else {
			$this->app['config']->package(
				'jaapgoorhuis/l4-newsmodulesettings', __DIR__ . '/config', 'jaapgoorhuis/l4-newsmodulesettings'
			);
		}
	}

	/**
	 * Which IoC bindings the provider provides.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'jaapgoorhuis\LaravelNewsmodulesettings\NewsmodulesettingsManager',
			'jaapgoorhuis\LaravelNewsmodulesettings\NewsmodulesettingStore',
		);
	}
}
