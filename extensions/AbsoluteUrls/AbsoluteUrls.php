<?php
/**
 * Absolute_Urls
 *
 * Rewrites all URL's in the CSS to absolute paths.
 * 
 * @package 		Scaffold
 * @subpackage		Engine
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_AbsoluteUrls extends Scaffold_Extension
{
	/**
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 * @var array
	 */
	public $_defaults = array(

		# If true, an exception will be thrown on missing files
		'require_files' => false
	);

	/**
	 * Takes a CSS string, rewrites all URL's using Scaffold's built-in find_file method
	 * @author Anthony Short
	 * @param $css
	 * @return $css string
	 */
	public function post_format($source)
	{
		# We can only process files
		if($source->type != 'file') return;

		// HOOK //
		$this->scaffold->notify('absoluteurls_before');
		
		# Full path the the source file
		$path = $source->path;
		
		# The CSS
		$css = $source->get();
	
		# The absolute url to the directory of the current CSS file
		$path = str_replace($_SERVER['DOCUMENT_ROOT'], '/', $path);
		$path = dirname($path);
	
		# @imports - Thanks to the guys from Minify for the regex :)
		if(
			preg_match_all(
			    '/
			        @import\\s+
			        (?:url\\(\\s*)?      # maybe url(
			        [\'"]?               # maybe quote
			        (.*?)                # 1 = URI
			        [\'"]?               # maybe end quote
			        (?:\\s*\\))?         # maybe )
			        ([a-zA-Z,\\s]*)?     # 2 = media list
			        ;                    # end token
			    /x'
			    ,$css
			    ,$found
			)
		)
		{
			foreach($found[1] as $key => $value)
			{			
				# Should we skip it
				if($this->skip($value))
					continue;
				
				$media = ($found[2][$key] == "") ? '' : ' ' . preg_replace('/\s+/', '', $found[2][$key]);
				
				# Absolute path				
				$absolute = $this->up_directory($path, substr_count($value, '..'.DIRECTORY_SEPARATOR , 0)) . str_replace('..'.DIRECTORY_SEPARATOR,'',$value);
				
				# Try to find the file and throw an error if we want to 			
				$this->scaffold->loader->find_file($absolute,$this->config['require_files']);
					
				# Rewrite it
				$css = str_replace($found[0][$key], '@import \''.$absolute.'\'' . $media . ';', $css);
			}
		}
		
		# Convert all url()'s to absolute paths if required
		if( preg_match_all('/url\\(\\s*([^\\)\\s]+)\\s*\\)/', $css, $found) )
		{
			foreach($found[1] as $key => $value)
			{
				$url = $this->unquote($value);
	
				# Absolute Path
				if($this->skip($url))
					continue;
				
				# Absolute path				
				$absolute = $this->up_directory($path, substr_count($url, '..'.DIRECTORY_SEPARATOR, 0)) . str_replace('..'.DIRECTORY_SEPARATOR,'',$url);

				# Try to find the file and throw an error if we want to 			
				$this->scaffold->loader->find_file($absolute,$this->config['require_files']);

				# Rewrite it
				$css = str_replace($found[0][$key], 'url('.$absolute.')', $css);
			}
		}
		
		# Update the source
		$source->set($css);
		
		// HOOK //
		$this->scaffold->notify('absoluteurls_after');
	}
	
	/**
	 * Skip a path for rewriting
	 *
	 * @author Anthony Short
	 * @param $url
	 * @return boolean
	 */
	private static function skip($url)
	{
		return (
			$url[0] == DIRECTORY_SEPARATOR || 
			$url[0] == "\\" ||
		    substr($url, 0, 7) == "http://" ||
			substr($url, 0, 5) == "data:"
		);
	}
	
	/**
	 * Takes a path, and goes back x number of directories.
	 *
	 * @author Anthony Short
	 * @param $path The path
	 * @param $n The number of directories to go back
	 * @return string
	 */
	public static function up_directory($path,$n)
	{
		$exploded = explode(DIRECTORY_SEPARATOR,$path);
		$exploded = array_slice($exploded, 0, (count($exploded) - $n) );
		return implode(DIRECTORY_SEPARATOR,$exploded);
	}
	
	/**
	 * Removes surrounding quotes from a string
	 * @access public
	 * @param $string
	 * @return string
	 */
	public function unquote($string)
	{
		return trim($string,"\"' ");
	}
}