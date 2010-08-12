<?php
/**
 * Scaffold_Observable
 *
 * Observable class for implementing the observer design pattern within Scaffold
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
abstract class Scaffold_Extension_Observable
{
	/**
	 * @access public
	 * @var array
	 */
	public $extensions = array();

	/**
	 * @param $extension
	 * @access public
	 * @return void
	 */
	public function attach($name,Scaffold_Extension_Observer $extension)
	{
		$this->extensions[$name] = $extension;
	}
	
	/**
	 * @param $extension
	 * @access public
	 * @return void
	 */
	public function detach(Scaffold_Extension_Observer $extension)
	{
		$new = array();
		
		foreach($this->extensions as $obj)
		{
			if($obj !== $extension)
			{
				$new[] = $obj;
			}
		}
		
		$this->extensions = $new;
	}

	/**
	 * Notifies observers
	 * @param $hook
	 * @param $params array
	 * @access public
	 * @return mixed
	 */
	public function notify($hook,$params = array())
	{
		foreach($this->extensions as $extension)
		{
			$extension->update($hook,$params);
		}
	}
}