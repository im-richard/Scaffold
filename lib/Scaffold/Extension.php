<?php

/**
 * Scaffold_Module
 *
 * The class for Scaffold modules. Modules hook into Scaffold_Engine at various stages.
 * 
 * @package			Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
abstract class Scaffold_Extension extends Scaffold_Extension_Observer
{
	/**
	 * The configuration
	 * @var array
	 */
	public $config = array();
	
	/**
	 * Path to the folder containing this extension
	 * @var string
	 */
	public $path;
	
	/**
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 *
	 * @var array
	 */
	public $_defaults = array();
	
	/**
	 * Sets the configuration
	 * @param $engine object Scaffold_Engine
	 * @param $config array Custom configration for this module
	 * @param $path string The path to the module folder to use
	 * @access public
	 * @return void
	 */
	public function __construct($config = array())
	{
		// Merge the default config with the custom config
		$this->config = array_merge($this->_defaults,$config);
	}
}