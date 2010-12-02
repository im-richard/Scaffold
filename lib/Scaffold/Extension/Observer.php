<?php

/**
 * Scaffold_Observer
 *
 * Observer class to implement the observer method
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
abstract class Scaffold_Extension_Observer
{
	/**
	 * Observable Parent
	 * @access private
	 * @var Scaffold_Observable
	 */
	private $observable = array();

	/**
	 * Attaches the observer to the observable class
	 *
	 * @param $observer
	 * @access public
	 * @return void
	 */
	public function __construct(Scaffold_Extension_Observable $observable)
	{
		$this->observable[] = $observable;
		$observable->attach($this);
	}

	/**
	 * @param $hook
	 * @param $params array
	 * @access public
	 * @return void
	 */
	public function update($hook,$params = array())
	{
		if(method_exists($this,$hook))
		{
			call_user_func_array(array($this,$hook),$params);
		}
	}
}