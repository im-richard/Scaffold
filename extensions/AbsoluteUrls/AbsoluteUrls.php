<?php

/**
 * Absolute_Urls
 *
 * Rewrites all URL's in the CSS to absolute paths.
 * 
 * @author Anthony Short
 */
class AbsoluteUrls extends Scaffold_Extension
{	
	/**
	 * Takes a CSS string, rewrites all URL's using Scaffold's built-in find_file method
	 *
	 * @author Anthony Short
	 * @param $css
	 * @return $css string
	 */
	public function format($engine)
	{
		# The absolute url to the directory of the current CSS file
		$dir = Scaffold::url($engine->css->path);

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
			    ,$engine->css
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
				$absolute = $this->up_directory($dir, substr_count($value, '..'.DIRECTORY_SEPARATOR , 0)) . str_replace('..'.DIRECTORY_SEPARATOR,'',$value);
					
				# Rewrite it
				$css->string = str_replace($found[0][$key], '@import \''.$absolute.'\'' . $media . ';', Scaffold::$css);
			}
		}
		
		# Convert all url()'s to absolute paths if required
		if( preg_match_all('/url\\(\\s*([^\\)\\s]+)\\s*\\)/', $engine->css, $found) )
		{
			foreach($found[1] as $key => $value)
			{
				$url = Scaffold_Utils::unquote($value);
	
				# Absolute Path
				if($this->skip($url))
					continue;
				
				# Absolute path				
				$absolute = $this->up_directory($dir, substr_count($url, '..'.DIRECTORY_SEPARATOR, 0)) . str_replace('..'.DIRECTORY_SEPARATOR,'',$url);
				
				# If the file doesn't exist
				if(!Scaffold::find_file($absolute))
					Scaffold::$log->add("Missing image - {$absolute}", 1);
					
				# Rewrite it
				$engine->css->string = str_replace($found[0][$key], 'url('.$absolute.')', $engine->css);
			}
		}
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
		# Absolute Path
		if(
			$url[0] == DIRECTORY_SEPARATOR || 
			$url[0] == "\\" ||
		    substr($url, 0, 7) == "http://" ||
			substr($url, 0, 5) == "data:"
		)
		{
			return true;
		}
		else
		{
			return false;
		}
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
		return implode(DIRECTORY_SEPARATOR,$exploded) . DIRECTORY_SEPARATOR;
	}

}