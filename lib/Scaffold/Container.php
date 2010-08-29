<?php

/**
 * Scaffold_Container
 *
 * Dependency injection container for Scaffold
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Container
{
	/**
	 * The path to the system files
	 *
	 * @var string
	 */
	public $system;
	
	/**
	 * The various options for different parts of the system
	 *
	 * @var array
	 */
	public $options;
	
	/**
	 * Default configuration
	 *
	 * @var array
	 */
	private $_defaults = array
	(
		'production' 			=> false,
		'max_age' 				=> 3600,
		'load_paths' 			=> array(),
		'output_compression' 	=> false,
		'extensions'			=> array()
	);
	
	/**
	 * Constructor
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function __construct($system,$options = array())
	{
		$this->system = $system;
		$this->options = array_merge($this->_defaults,$options);
	}
	
	/**
	 * Loads the extension files
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function load_extensions($path,$scaffold)
	{	
		# Scaffold_Helper object
		$helper = $this->getHelper();
		
		# The glob path
		$path = realpath($path) . DIRECTORY_SEPARATOR . '*';
	
		# Load each of the extensions
		foreach(glob($path) as $ext)
		{			
			$ext .= DIRECTORY_SEPARATOR;
		
			$config 	= array();
			$name 		= basename($ext);
			$class 		= 'Scaffold_Extension_' . $name;
			$file 		= $ext.$name.'.php';
			
			# This extension isn't enabled
			if(!in_array($name, $this->options['extensions']))
				continue;
			
			# Get the config for the extension if available
			if(isset($this->options[$name]))
				$config = $this->options[$name];
			
			# Load the controller
			if(file_exists($file))
			{
				require_once realpath($ext.$name.'.php');
				$object = new $class($config);
				$object->attach_helper($helper);
				$scaffold->attach($name,$object);
			}
		}

		return $scaffold;
	}

	/**
	 * Generates Scaffold_Engine objects
	 * @return Scaffold
	 */
	public function build() 
	{
		$cache 		= $this->getCache();
		$response 	= $this->getResponse();
		$helper 	= $this->getHelper();

		# The main object
		$scaffold = new Scaffold($cache,$response,$this->options['production']);
		
		# Load extensions
		$scaffold = $this->load_extensions($this->system.'/extensions/',$scaffold);
		
		# Attach the helper object
		$scaffold->attach_helper($helper);
		
		return $scaffold;
	}
	
	/**
	 * Gets the helper object
	 * @access public
	 * @return Scaffold_Helper
	 */
	public function getHelper()
	{
		if(isset($this->_helper))
			return $this->_helper;
			
		$helper = new Scaffold_Helper();
		$helper->add('string',new Scaffold_Helper_String());
		$helper->add('array',new Scaffold_Helper_Array());
		$helper->add('css',new Scaffold_Helper_CSS());
		$helper->add('load',new Scaffold_Helper_Loader($this->options['load_paths']));
		$this->_helper = $helper;
		
		return $this->_helper; 
	}
	
	/**
	 * Gets the response object
	 *
	 * @access public
	 * @return Scaffold_Response
	 */
	public function getResponse()
	{
		if(isset($this->_response))
			return $this->_response;
		
		// Dependencies
		$response_encoder = $this->getResponseEncoder();
		$response_cache = $this->getResponseCache();

		return $this->_response = new Scaffold_Response($response_encoder,$response_cache,$this->options);
	}
	
	/**
	 * Gets the response encoder object
	 * @access public
	 * @return Scaffold_Response_Encoder
	 */
	public function getResponseEncoder()
	{
		if(isset($this->_response_encoder))
			return $this->_response_encoder;
		
		return $this->_response_encoder = new Scaffold_Response_Encoder($this->options['output_compression']);
	}
	
	/**
	 * Gets the response cache object
	 *
	 * @access public
	 * @return Scaffold_Response_Cache
	 */
	public function getResponseCache()
	{
		if(isset($this->_response_cache))
			return $this->_response_cache;
		
		return $this->_response_cache = new Scaffold_Response_Cache();
	}
	
	/**
	 * Gets the system cache object
	 *
	 * @access public
	 * @return Scaffold_Cache_File
	 */
	public function getCache()
	{
		if(isset($this->_cache))
			return $this->_cache;

		return $this->_cache = new Scaffold_Cache_File($this->system.'/cache/',$this->options['max_age']);
	}
}