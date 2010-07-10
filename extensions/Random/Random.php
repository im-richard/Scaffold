<?php
/**
 * Scaffold_Extension_Random
 *
 * Uses the Function extension to create a new CSS function called rand(),
 * this lets you get a random number between two numbers.
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_Random extends Scaffold_Extension
{
	/**
	 * Registers a custom CSS function.
	 * @access public
	 * @param $functions
	 * @return array
	 */
	public function register_function($functions)
	{
		$functions->register('rand',array($this,'parse'));
	}
	
	/**
	 * Parses rand() functions within the CSS
	 * @access public
	 * @param $from
	 * @param $to
	 * @return string
	 */
	public function parse($from,$to)
	{
		return rand($from,$to);
	}
}