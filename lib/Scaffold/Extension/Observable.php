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
	public $data;

	/**
	 * Array of extension objects
	 *
	 * @access public
	 * @var array
	 */
	public $extensions = array();

	/**
	 * Attaches a module as an observer
	 *
	 * @param $module
	 * @access public
	 * @return void
	 */
	public function attach($name,Scaffold_Extension_Observer $extension)
	{
		$this->extensions[$name] = $extension;
		$extension->scaffold = $this;
	}
	
	/**
	 * Detaches an observer
	 *
	 * @author your name
	 * @param $module
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
	 * Notifies observers of a hook
	 *
	 * @param $hook
	 * @access public
	 * @return Scaffold_Engine
	 */
	public function notify($hook,$data = array())
	{
		foreach($this->extensions as $extension)
		{
			$extension->update($hook,$data);
		}
	}
}