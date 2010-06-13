<?php

/**
 * Scaffold_Source_String
 *
 * A single CSS file or string. 
 * 
 * @author your name
 */
class Scaffold_Source_String
{
	/**
	 * Type of source
	 *
	 * @var string
	 */
	public $type;

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
	public $last_modified = null;
	
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
		
		// The type of source
		$this->type = $type;

		$this->contents = $this->original = $content;

		// When it was modified
		$this->last_modified = time();
	
		// Unique id
		$this->id = md5($string);
		
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