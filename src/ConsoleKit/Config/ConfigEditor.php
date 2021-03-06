<?php
/**
 * This file is part of the Console-Kit library.
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @copyright Alexander Obuhovich <aik.bold@gmail.com>
 * @link      https://github.com/console-helpers/console-kit
 */

namespace ConsoleHelpers\ConsoleKit\Config;


class ConfigEditor
{

	/**
	 * Filename, where config is stored.
	 *
	 * @var string
	 */
	protected $filename;

	/**
	 * Settings.
	 *
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Creates config instance.
	 *
	 * @param string $filename Filename.
	 * @param array  $defaults Defaults.
	 */
	public function __construct($filename, array $defaults = array())
	{
		$this->filename = $filename;
		$this->load($defaults);
	}

	/**
	 * Returns config value.
	 *
	 * @param string $name    Config setting name.
	 * @param mixed  $default Default value.
	 *
	 * @return mixed
	 */
	public function get($name, $default = null)
	{
		if ( substr($name, -1) == '.' ) {
			$ret = array();

			foreach ( $this->settings as $setting_name => $setting_value ) {
				if ( preg_match('/^' . preg_quote($name, '/') . '/', $setting_name) ) {
					$ret[$setting_name] = $setting_value;
				}
			}

			return $ret;
		}

		return array_key_exists($name, $this->settings) ? $this->settings[$name] : $default;
	}

	/**
	 * Returns all config settings.
	 *
	 * @return array
	 */
	public function getAll()
	{
		return $this->settings;
	}

	/**
	 * Sets config value.
	 *
	 * @param string $name  Config setting name.
	 * @param mixed  $value Config setting value.
	 *
	 * @return void
	 */
	public function set($name, $value)
	{
		if ( $value === null ) {
			unset($this->settings[$name]);
		}
		else {
			$this->settings[$name] = $value;
		}

		$this->store();
	}

	/**
	 * Loads config contents from disk.
	 *
	 * @param array $defaults Defaults.
	 *
	 * @return void
	 */
	protected function load(array $defaults)
	{
		if ( file_exists($this->filename) ) {
			$stored_settings = json_decode(file_get_contents($this->filename), true);
			$new_defaults = array_diff_key($defaults, $stored_settings);

			if ( $new_defaults ) {
				$this->settings = array_merge($stored_settings, $new_defaults);
				$this->store();
			}
			else {
				$this->settings = $stored_settings;
			}

			return;
		}

		$this->settings = $defaults;
		$this->store();
	}

	/**
	 * Stores config contents to the disk.
	 *
	 * @return void
	 */
	protected function store()
	{
		$options = defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0;
		file_put_contents($this->filename, json_encode($this->settings, $options));
	}

}
