<?php
/**
 * Scaffold_Source_Interface
 *
 * The interface for all source types
 * 
 * @package 		Scaffold
 * @subpackage		Source
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
interface Scaffold_Source_Interface
{	
	/**
	 * Return the original contents of the source
	 * @access public
	 * @return string
	 */
	public function original();
	
	/**
	 * Get the current contents of the source
	 * @access public
	 * @return string
	 */
	public function get();
	
	/**
	 * Return the unique id for this source
	 *
	 * @access public
	 * @return string
	 */
	public function id();
	
	/**
	 * Get the last-modified time for this source
	 *
	 * @access public
	 * @return string
	 */
	public function last_modified();
}