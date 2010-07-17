<?php
/**
 * Scaffold_Extension_YUICompressor
 *
 * Parses the CSS through the YUI Compressor
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_YUICompressor extends Scaffold_Extension
{
	/**
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 * @var array
	 */
	public $_defaults = array(
		'command' => 'java',
		'jar' => false,
		'params' => array(
			'type' => 'css'
		)
	);

	/**
	 * Parse the string through the sass command line tool
	 * @author Anthony Short
	 * @param $source
	 * @return string
	 */
	public function post_format($source,$scaffold)
	{		
		// The path to the cache file we'll use to temporary store the files
		$path = $scaffold->cache->set('yuicompressor/'.$source->id,$source->contents,null,false,false);
		
		// Path to jar file
		if($this->config['jar'] === false)
		{
			$jar = dirname(__FILE__) . '/yuicompressor-2.4.2.jar';
		}
		else
		{
			$jar = $this->config['jar'];
		}
		
		// Sass will output the final file to the cache
		$cmd = $this->config['command'].' -jar '.$jar.' '.$this->_build_params($this->config['params']) . ' ' . escapeshellcmd($path) . ' 2>&1';
		exec($cmd,$output,$return);
		
		// There's an error
		if($return == 1)
		{
			throw new Scaffold_Extension_Exception('YUI Compressor Error',$output[0]);
		}
		
		// Set the contents to the source! We're all done!
		$source->set($output[0]);		
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
	private function _build_params($params)
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