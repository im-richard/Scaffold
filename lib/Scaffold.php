<?php
/**
 * Scaffold
 *
 * - Parses files
 * - Parses directories of CSS files
 * - Parses pre-defined groups
 *
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold
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
	
	// =========================================
	// = Private Variables =
	// =========================================
	
	/**
	 * System cache
	 *
	 * @access private
	 * @var Scaffold_Cache
	 */
	private $_cache;
	
	/**
	 * Responds to the browser
	 *
	 * @access private
	 * @var Scaffold_Reponse
	 */
	private $_response;
	
	/**
	 * Loads in files, directories and sources
	 *
	 * @access private
	 * @var Scaffold_Load
	 */
	 private $_loader;
	
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
	public function __construct(Scaffold_Cache $cache, Scaffold_Response $response, Scaffold_Load $loader, $production = false)
	{		
		// The system cache
		$this->_cache = $cache;
		
		// This handles the output of CSS
		$this->_response = $response;
		
		// Paths to load files from
		$this->_loader = $loader;
		
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
		if($this->_cache->expired($source->id(),$source->last_modified()))
		{
			$result = $this->_parse($source->contents);
			$this->notify('before_cache',$result,$source);
			$this->_cache->set($source->id(),$source->contents());
		}
		else
		{
			$result = $this->_cache->get($source->id());
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
		$this->_response->render($source->contents,$source->last_modified,$this->production);
	}
	
	// ============================
	// = Private Methods =
	// ============================
	
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
	private function _parse($css)
	{
		$css = $this->notify('initialize',$css);
		$css = $this->notify('process',$css);
		$css = $this->notify('format',$css);
		return $css;
	}
}