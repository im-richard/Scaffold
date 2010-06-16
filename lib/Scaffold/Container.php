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
	 * Extensions
	 *
	 * @var array
	 */
	public $extensions = array();
	
	/**
	 * The various options for different parts of the system
	 *
	 * @var array
	 */
	public $options;
	
	/**
	 * @var Scaffold_Response
	 */
	public $_response;
	
	/**
	 * @var Scaffold_Response_Compressor
	 */
	public $_response_compressor;
	
	/**
	 * @var Scaffold_Response_Cache
	 */
	public $_response_cache;
	
	/**
	 * @var Scaffold_Cache_File
	 */
	public $_cache;
	
	/**
	 * Constructor
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function __construct($system,$options = array())
	{
		$this->system = $system;
		$this->options = $options;
		
		/**
		 * If we're just in development mode, we'll want to always recache
		 * the file. Setting the cache_lifetime to 0 will force Scaffold
		 * to make sure it reparsed the file each time.
		 */
		if($this->options['production'] === false)
		{
			$this->options['cache_lifetime'] = 0;
		}
		
		# Load each of the extensions
		foreach(glob($system.'/extensions/*/') as $ext)
		{
			$ext .= DIRECTORY_SEPARATOR;
		
			$config = array();
			$name = basename($ext);
			$file = $name.'.php';
			
			if(isset($this->options[$name]))
			{
				$config = $this->options[$name];
			}

			# Load the controller
			if(file_exists($ext.$file))
			{
				require_once($ext.$file);
				$class = "Scaffold_Extension_".$name;
				$this->extensions[$name] = new $class($config,$ext);
			}
		}
	}

	/**
	 * Generates Scaffold_Engine objects
	 *
	 * @return Scaffold
	 */
	public function build() 
	{	
		$cache = $this->getCache();
		$response = $this->getResponse();
		$loader = $this->getLoader();
		$scaffold = new Scaffold($cache,$response,$loader,$this->options['production']);
		
		foreach($this->extensions as $name => $ext)
		{
			$scaffold->attach($name,$ext);
		}
		
		return $scaffold;
	}
	
	/**
	 * Create a new unrestricted source type
	 * @param $type
	 * @param $content
	 * @return Scaffold_Source
	 */
	public function source($type,$content)
	{
		$class = 'Scaffold_Source_'.ucfirst($type);
		$driver = new $class($content,$this->options);		
		return new Scaffold_Source($driver,$this->options);
	}
	
	/**
	 * Gets the loader object
	 *
	 * @access public
	 * @return Scaffold_Loader
	 */
	public function getLoader()
	{
		if(isset($this->_loader))
			return $this->_loader;
		
		return $this->_loader = new Scaffold_Loader($this->options['load_paths']);
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
			
		return $this->_response = new Scaffold_Response($response_encoder,$response_cache,array('content_type' => 'text/css'));
	}
	
	/**
	 * Gets the response encoder object
	 *
	 * @access public
	 * @return Scaffold_Response_Compressor
	 */
	public function getResponseEncoder()
	{
		if(isset($this->_response_encoder))
			return $this->_response_encoder;
		
		return $this->_response_encoder = new Scaffold_Response_Compressor($this->options['output_compression']);
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
		
		return $this->_response_cache = new Scaffold_Response_Cache($this->options['cache_lifetime']);
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

		return $this->_cache = new Scaffold_Cache_File($this->system.'/cache/',$this->options['cache_lifetime']);
	}
}