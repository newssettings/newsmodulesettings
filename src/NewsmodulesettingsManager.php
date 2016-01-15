<?php
/**
 * Laravel 4 - Persistant Settings
 *
 * @author   Andreas Lutro <anlutro@gmail.com>
 * @license  http://opensource.org/licenses/MIT
 * @package  l4-Newsmodulesettings
 */

namespace jaapgoorhuis\LaravelNewsmodulesettings;

use Illuminate\Support\Manager;
use Illuminate\Foundation\Application;

class NewsmodulesettingsManager extends Manager
{
	public function getDefaultDriver()
	{
		return $this->getConfig('jaapgoorhuis/l4-newsmodulesettings::store');
	}

	public function createJsonDriver()
	{
		$path = $this->getConfig('jaapgoorhuis/l4-newsmodulesettings::path');

		return new JsonNewsmodulesettingStore($this->app['files'], $path);
	}

	public function createDatabaseDriver()
	{
		$connectionName = $this->getConfig('jaapgoorhuis/l4-newsmodulesettings::connection');
		$connection = $this->app['db']->connection($connectionName);
		$table = $this->getConfig('jaapgoorhuis/l4-newsmodulesettings::table');

		return new DatabaseNewsmodulesettingStore($connection, $table);
	}

	public function createMemoryDriver()
	{
		return new MemorySettingStore();
	}

	public function createArrayDriver()
	{
		return $this->createMemoryDriver();
	}

	protected function getConfig($key)
	{
		if (version_compare(Application::VERSION, '5.0', '>=')) {
			$key = str_replace('jaapgoorhuis/l4-newsmodulesettings::', 'newsmodulesettings.', $key);
		}

		return $this->app['config']->get($key);
	}
}
