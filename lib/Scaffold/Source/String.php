<?php
/**
 * Scaffold_Source_File
 *
 * A source file for Scaffold that is just a string.
 * 
 * @package 		Scaffold
 * @subpackage		Source
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Source_String extends Scaffold_Source
{
	/**
	 * The type of source
	 * @var string
	 */
	public $type = 'string';

	/**
	 * Constructor
	 */
	public function __construct($content,$options = array())
	{
		$this->options = $options;
		$this->contents = $content;		
		$this->last_modified = (isset($options['last_modified'])) ? $options['last_modified'] : time();
		$this->id = (isset($options['id'])) ? $options['id'] : md5($content);
	}

	/**
	 * Get the current contents of the source
	 * @access public
	 * @return string
	 */
	public function get()
	{
		return $this->contents;
	}
	
	/**
	 * Set the current contents of the source
	 * @access public
	 * @return string
	 */
	public function set($value)
	{
		return $this->contents = $value;
	}
	
	/**
	 * Return the unique id for this source
	 *
	 * @access public
	 * @return string
	 */
	public function id()
	{
		return $this->id;
	}
	
	/**
	 * Get the last-modified time for this source
	 *
	 * @access public
	 * @return string
	 */
	public function last_modified()
	{
		return $this->last_modified;
	}
	
	/**
	 * Finds a file relative to the source file from a URL
	 * @access public
	 * @param $url
	 * @return boolean
	 */
	public function find($url)
	{
		return false;
	}
}