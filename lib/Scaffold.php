<?php
/**
 * Scaffold
 * Handles for Scaffold_Source objects 
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
		$this->cache = $cache;
		$this->response = $response;
		$this->loader = $loader;
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
		# Try and load it from the cache
		$cached = $this->cache->get($source->id);
		
		# Can't load it from the cache, we're in dev mode, or the original file has changed
		if($cached === false OR $this->production === false OR $source->last_modified > $cached->last_modified)
		{
			// Run it through the extensions
			$source = $this->parse($source);
			
			// Save it to the cache
			$this->save($source);
		}
		else
		{
			// Update the source with the cache values
			$source->contents = $cached->contents;
			$source->last_modified = $cached->last_modified;
			$source->expires = $cached->expires;
		}
		
		return $source;
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
	public function render(Scaffold_Source $source)
	{
		$this->response->render(
			$source->get(),
			$source->last_modified,
			$this->production,
			$this->_output_type
		);
	}

	/**
	 * Parses a CSS string through each of the extensions.
	 * 
	 * - Initialize: Used for loading libraries and preparing for processing
	 * - Pre-format
	 * - Pre-process: Any formatting or processing that needs to occur so that the process hook will go smooth
	 * - Process: Any form of processing the CSS.
	 * - Post-process: Cleaning up anything left behind from the process hook to make it valid CSS again.
	 * - Post-Format: Used for formatting the CSS. Should be valid CSS by this point.
	 *
	 * As well as these, the extensions themselves can create hooks, so that
	 * you can only run an extension during another extension.
	 *
	 * @param $source Scaffold_Source
	 * @return Scaffold_Source
	 */
	public function parse(Scaffold_Source $source)
	{
		$params = array($source);
		$this->notify('initialize',$params);
		$this->notify('pre_format',$params);
		$this->notify('pre_process',$params);
		$this->notify('process',$params);
		$this->notify('post_process',$params);
		$this->notify('post_format',$params);
		return $source;
	}

	/**
	 * Saves the contents of the source object
	 * @access public
	 * @param $source Scaffold_Source
	 * @return void
	 */
	public function save(Scaffold_Source $source)
	{
		$this->cache->set(
			$source->id,
			$source->get(),
			$source->last_modified
		);
	}
}