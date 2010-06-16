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
class Scaffold_Source_String extends Scaffold_Source_Base
{
	/**
	 * A unique identifier
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $last_modified = 0;

	/**
	 * @var string
	 */
	public $contents;

	/**
	 * The original content before modification
	 * @var string
	 */
	public $original;

	/**
	 * Constructor
	 */
	public function __construct($content,$options = array())
	{
		$this->options = $options;
		$this->contents = $this->original = $content;
		$this->last_modified = (isset($options['last_modified'])) ? $options['last_modified'] : time();
		$this->id = (isset($options['id'])) ? $options['id'] : md5($string);
	}
	
	/**
	 * Return the original contents of the source
	 * @access public
	 * @return string
	 */
	public function original()
	{
		return $this->original;
	}
	
	/**
	 * Get the current contents of the source
	 * @access public
	 * @return string
	 */
	public function contents()
	{
		return $this->contents;
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
}