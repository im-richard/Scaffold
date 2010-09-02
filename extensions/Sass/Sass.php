<?php
/**
 * Scaffold_Extension_Sass
 *
 * Parses the CSS through the Sass rubygem
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_Sass extends Scaffold_Extension
{
	/**
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 * @var array
	 */
	public $_defaults = array(
		
		'params' => array(

			// Use the CSS-superset SCSS syntax.
			'scss' => true,
			
			// Output style. Can be nested (default), compact, compressed, or expanded.
			'style' => 'compressed',
			
			// Don't cache to sassc files.
			'no-cache' => true,
			
			// The path to put cached Sass files. Defaults to .sass-cache.
			'cache-location' => false,
			
			// Add a sass import path.
			'load-path' => false,
			
			// Emit extra information in the generated CSS that can be used by the FireSass Firebug plugin.
			'debug-info' => false,
			
			// Emit comments in the generated CSS indicating the corresponding sass line.
			'line-numbers' => false	
		),
		'command' => 'sass'
	);

	/**
	 * Parse the string through the sass command line tool
	 * @author Anthony Short
	 * @param $source
	 * @return string
	 */
	public function post_process($source,$scaffold)
	{		
		// The path to the cache file we'll use to temporary store the files
		$path = $scaffold->cache->set('sass/'.$source->id,$source->contents,null,false,false);
		
		// Temporary file to store the sass output
		$temp = $scaffold->cache->find('/sass/'.$source->id.'.sass');

		// Sass will output the final file to the cache
		$cmd = $this->config['command'].' '.$this->_build_sass_params($this->config['params']) . ' ' . escapeshellcmd($path) . ' '.$temp.' 2>&1';
		exec($cmd,$output,$return);
		
		// There's an error
		if($output !== array())
		{
			throw new Scaffold_Extension_Exception('Sass Error',implode("\n",$output));
		}
		
		$sass = file_get_contents($temp);
		
		// Set the contents to the source! We're all done!
		$source->set($sass);		
	}
	
	/**
	 * Builds the params for the Sass cmd using an array
	 * - true = sets the key. eg 'sass' => true will become --sass
	 * - false = It's ignored
	 * - Any value = Sets the key and value. eg. 'style' => 'nested' will become '--style nested'
	 * @access private
	 * @param $params
	 * @return string
	 */
	private function _build_sass_params($params)
	{
		$return = '';
	
		foreach($params as $key => $value)
		{
			if($value === false)
			{
				continue;
			}
			elseif($value === true)
			{
				$return .= '--'.$key.' ';
			}
			else
			{
				$return .= '--'.$key.' '.$value.' ';
			}
		}
		
		return $return;
	}
}