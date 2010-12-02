<?php

require dirname(__FILE__) . '/Compressor.php';

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
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function post_format($source,$scaffold)
	{
		$source->contents = Minify_CSS_Compressor::process($source->contents);
	}
}