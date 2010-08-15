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
	 * @var array
	 */
	public $config = array();
	
	/**
	 * Default settings
	 * @var array
	 */
	public $_defaults = array();
	
	/**
	 * @var Scaffold_Helper
	 */
	public $helper;
	
	/**
	 * @param $config array
	 * @access public
	 * @return void
	 */
	public function __construct($config = array())
	{
		$this->config = array_merge($this->_defaults,$config);
	}
	
	/**
	 * Adds a helper object
	 * @param Scaffold_Helper
	 * @access public
	 * @return void
	 */
	public function attach_helper(Scaffold_Helper $helper)
	{
		$this->helper = $helper;
	}
}