<?php
/**
 * Scaffold_Extension_Minify
 *
 * Compresses the string using the Minify library
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_Minify extends Scaffold_Extension
{
	/**
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 * @var array
	 */
	public $_defaults = array();
	
	/**
	 * Loads the minify library
	 * @access public
	 * @param $source
	 * @return void
	 */
	public function initialize($source)
	{
		require dirname(__FILE__) . '/Compressor.php';
	}
	
	/**
	 * Formats the CSS
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function post_format($source)
	{
		$source->set( Minify_CSS_Compressor::process($source->get()) );
	}
}