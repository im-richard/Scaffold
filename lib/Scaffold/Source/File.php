<?php

/**
 * Scaffold_Source_File
 *
 * A source file for Scaffold that is file-based.
 * 
 * @package 		Scaffold
 * @subpackage		Source
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
 
class Scaffold_Source_File implements Scaffold_Source_Interface
{
	/**
	 * A unique identifier for this source
	 *
	 * @var mixed
	 */
	public $id;

	/**
	 * When the file was last modified
	 *
	 * @var string
	 */
	public $last_modified = 0;
	
	/**
	 * Options specific to this file
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * The source contents.
	 *
	 * @var string
	 */
	public $contents;

	/**
	 * The original content before modification
	 *
	 * @var string
	 */
	public $original;

	/**
	 * Create the file object
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function __construct($content,$options = array())
	{
		// Options for this file
		$this->options = $options;

		// Store the contents of the file
		$this->contents = file_get_contents($content);

		// When it was modified
		$this->last_modified = filemtime($content);
	
		// Unique id
		$this->id = $content;

		// Store the path
		$this->original = $content;
		
		// Override the last modified time manually
		if(isset($options['last_modified']))
		{
			$this->last_modified = $options['last_modified'];
		}
		
		// Override the identifier manually
		if(isset($options['id']))
		{
			$this->id = $options['id'];
		}
	}
}