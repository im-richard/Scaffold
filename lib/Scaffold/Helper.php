<?php
/**
 * Scaffold_Helper
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Helper
{
	/**
	 * @var array
	 */
	private $_helpers = array();
	
	/**
	 * @return void
	 */
	public function __construct(){}

	/**
	 * Adds a helper object
	 * @access public
	 * @param $name
	 * @param $object
	 * @return void
	 */
	public function add($name,$object)
	{
		$this->_helpers[$name] = $object;
	}
	
	/**
	 * Removes a helper object
	 * @access public
	 * @param $name
	 * @return void
	 */
	public function remove($name)
	{
		if(isset($this->_helpers[$name]))
			$this->_helpers[$name] = null;
	}
	
	/**
	 * Magic method for using helper objects
	 * @access public
	 * @param $name
	 * @return object
	 */
	public function __get($name)
	{
		if(array_key_exists($name,$this->_helpers))
		{
			return $this->_helpers[$name];
		}
		else
		{
			throw new Exception('Helper doesn\'t exist - ' . $name);
		}
	}
}