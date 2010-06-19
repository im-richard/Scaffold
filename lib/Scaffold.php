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
	 *
	 * @access public
	 * @var string
	 */
	public $production;
	
	/**
	 * System cache
	 *
	 * @access public
	 * @var Scaffold_Cache
	 */
	public $cache;
	
	/**
	 * Responds to the browser
	 *
	 * @access public
	 * @var Scaffold_Reponse
	 */
	public $response;
	
	/**
	 * Loads in files, directories and sources
	 *
	 * @access public
	 * @var Scaffold_Loader
	 */
	 public $loader;
	
	// =========================================
	// = Protected Variables =
	// =========================================
	
	/**
	 * Output type
	 *
	 * @var string
	 */
	protected $_output_type = 'text/css';

	// =========================================
	// = Constructors & Initialization Methods =
	// =========================================
	
	/**
	 * Constructor
	 *
	 * @param $param
	 * @return return type
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
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function compile(Scaffold_Source $source)
	{
		$cache_expired = $this->cache->expired($source->id(),$source->last_modified());
		
		if($this->production === false OR $cache_expired === true)
		{
			$result = $this->parse($source);
			$this->cache->set($source->id(),$result->get());
		}
		else
		{
			$result = $this->cache->get($source->id());
		}

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
	 * @param $source Scaffold_Source
	 * @return void
	 */
	public function render(Scaffold_Source $source)
	{
		$this->response->render($source->get(),$source->last_modified(),$this->production,$this->_output_type);
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
	 * @param $css
	 * @return string
	 */
	public function parse($source)
	{
		$this->data = array('source'=>$source);
		$this->notify('initialize');
		$this->notify('process');
		$this->notify('format');
		return $this->data['source'];
	}
}