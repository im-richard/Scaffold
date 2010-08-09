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
	 * Takes a CSS string, rewrites all URL's using Scaffold's built-in find_file method
	 * @access Anthony Short
	 * @param $css
	 * @return void
	 */
	public function post_format($source,$scaffold)
	{
		# We can only process files
		if($source->path === false) return;

		$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'],'',dirname($source->path));
	
		# Process all @imports
		if($found = $this->find_imports($source->contents))
		{
			foreach($found[1] as $key => $value)
			{
				# A path we don't want to touch
				if($this->skip($value)) continue;

				# The media type if specified
				$media = ($found[2][$key] == "") ? '' : ' ' . preg_replace('/\s+/', '', $found[2][$key]);
				
				# Get the absolute path
				$absolute = $this->resolve_absolute_path($relative_path,$value);
				
				# Replace it
				$source->contents = str_replace($found[0][$key], '@import \''.$absolute.'\'' . $media . ';', $source->contents);
			}
		}
		
		# Process all url() paths
		if($found = $this->find_url_functions($source->contents))
		{
			foreach($found[1] as $key => $value)
			{
				$url = $this->unquote($value);
				
				# A path we don't want to touch
				if($this->skip($url)) continue;
				
				# Get the absolute path
				$absolute = $this->resolve_absolute_path($relative_path,$url);
				
				# Rewrite it
				$source->contents = str_replace($found[0][$key], 'url('.$absolute.')', $source->contents);
			}
		}
	}
	
	/**
	 * Resolve a url path
	 * @access private
	 * @param $relative_path Document root relative path to the original CSS file
	 * @param $path The CSS url path to resolve
	 */
	private function resolve_absolute_path($relative_path,$path)
	{
		# Relative path to the file
		$relative = $this->up_directory(
			$relative_path, 
			substr_count($path, '..'.DIRECTORY_SEPARATOR, 0)
		); 
		
		# Absolute path				
		return $relative.str_replace('..'.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,$path);
	}
	
	/**
	 * Skip a path for rewriting
	 * @access private
	 * @param $url
	 * @return boolean
	 */
	private function skip($url)
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
	 * @access public
	 * @param $path The path
	 * @param $n The number of directories to go back
	 * @return string
	 */
	public function up_directory($path,$n)
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
	
	/**
	 * Finds all @imports
	 * @access public
	 * @param $str
	 * @return array
	 */
	public function find_imports($str)
	{
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
		    ,$str
		    ,$found
		);
		
		return $found;
	}
	
	/**
	 * Finds all url() functions
	 * @access public
	 * @param $str
	 * @return array
	 */
	public function find_url_functions($str)
	{
		 preg_match_all('/url\\(\\s*([^\\)\\s]+)\\s*\\)/', $str, $found);
		 return $found;
	}
}