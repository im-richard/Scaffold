<?php
/**
 * Scaffold
 *
 * Compiles and renders Scaffold_Source objects
 *
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold extends Scaffold_Extension_Observable
{
	// =========================================
	// = Public Variables =
	// =========================================

	/**
	 * Is Scaffold in production mode?
	 * @access public
	 * @var string
	 */
	public $production;
	
	/**
	 * Handles the caching of parsed sources
	 * @access public
	 * @var Scaffold_Cache
	 */
	public $cache;
	
	/**
	 * Responds to the browser
	 * @access public
	 * @var Scaffold_Reponse
	 */
	public $response;
	
	/**
	 * Loads in files,directories and sources
	 * @access public
	 * @var Scaffold_Loader
	 */
	 public $loader;
	
	// =========================================
	// = Protected Variables =
	// =========================================
	
	/**
	 * HTTP output type
	 * @var string
	 */
	protected $_output_type = 'text/css';

	// =========================================
	// = Constructors & Initialization Methods =
	// =========================================
	
	/**
	 * @access public
	 * @param $cache 		Scaffold_Cache
	 * @param $response 	Scaffold_Response
	 * @param $loader 		Scaffold_Loader
	 * @param $production 	boolean
	 * @return void
	 */
	public function __construct(Scaffold_Cache $cache, Scaffold_Response $response, Scaffold_Loader $loader, $production = false)
	{		
		// The system cache
		$this->cache = $cache;
		
		// This handles the output of CSS
		$this->response = $response;
		
		// Loads files and directories
		$this->loader = $loader;
		
		// Set production mode
		$this->production = $production;
	}
	
	// ============================
	// = Public Methods =
	// ============================
	
	/**
	 * Compiles the CSS using the engine and caches the result
	 * @access public
	 * @param $source Scaffold_Source
	 * @return array
	 */
	public function compile(Scaffold_Source $source)
	{
		$id 		= $source->id();
		$modified 	= $source->last_modified();
		$expired 	= $this->cache->expired($id,$modified);
		
		if($this->production === false OR $expired === true)
		{
			$this->cache->set($id,$this->parse($source));
		}

		$result = array();
		$result['string'] = $this->cache->get($id);
		$result['last_modified'] = $this->cache->modified($id);

		return $result;
	}
	
	/**
	 * Renders the contents of a file. In production mode, it will tell
	 * the rendering engine to use the browsers cache if it's available.
	 *
	 * In development mode, the content will always be resent so that you
	 * don't have to clear the cache every time you make a request.
	 *
	 * @access public
	 * @param $output 			string 		The contents to be output to the browser
	 * @param $last_modified 	int 		Time to compare against the browser cache
	 * @return void
	 */
	public function render($output,$last_modified)
	{
		$this->response->render($output,$last_modified,$this->production,$this->_output_type);
	}

	/**
	 * Parses a CSS string through each of the extensions. Calls 3 hooks
	 * 
	 * - Initialize: Used for loading libraries and preparing for processing
	 * - Process: Any form of processing the CSS.
	 * - Format: Used for formatting the CSS. Basically, no syntax manipulation.
	 *
	 * As well as these, the extensions themselves can create hooks, so that
	 * you can only run an extension during another extension.
	 *
	 * @param $source Scaffold_Source
	 * @return string
	 */
	public function parse($source)
	{
		$this->data = array('source'=>$source);
		$this->notify('initialize');
		$this->notify('process');
		$this->notify('format');
		return $this->data['source']->get();
	}
}