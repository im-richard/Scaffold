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
abstract class Scaffold_Source
{
	/**
	 * A unique identifier
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $last_modified;
	
	/**
	 * @var string
	 */
	public $contents;
	
	/**
	 * @var array
	 */
	public $options = array();
	
	/**
	 * @var string
	 */
	public $type;
	
	/**
	 * @var int
	 */
	public $expires;
	
	/**
	 * Get the current contents of the source
	 * @access public
	 * @return string
	 */
	abstract public function get();
	
	/**
	 * Set the current contents of the source
	 * @access public
	 * @return string
	 */
	abstract public function set($value);
	
	/**
	 * Return the unique id for this source
	 *
	 * @access public
	 * @return string
	 */
	abstract public function id();
	
	/**
	 * Get the last-modified time for this source
	 *
	 * @access public
	 * @return string
	 */
	abstract public function last_modified();
	
	/**
	 * Finds a file relative to the source file from a URL
	 * @access public
	 * @param $url
	 * @return mixed
	 */
	abstract public function find($url);
}