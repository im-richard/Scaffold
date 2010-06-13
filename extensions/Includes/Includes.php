<?php

/**
 * Includes
 *
 * This allows you to include files before processing for compiling
 * into a single file and later cached. 
 *
 * @author Anthony Short
 */
class Scaffold_Engine_Includes
{
	/**
	 * Stores which files have already been included
	 *
	 * @var array
	 */
	public $loaded = array();

	/**
	 * Imports css via @import statements
	 * 
	 * @param $css
	 */
	public function replace_includes($css)
	{
		if(preg_match_all('/\@include\s+(?:\'|\")([^\'\"]+)(?:\'|\")\;/', $css, $matches))
		{
			$unique = array_unique($matches[1]);
			$include = str_replace("\\", "/", Scaffold_Utils::unquote($unique[0]));
			
			# If they haven't supplied an extension, we'll assume its a css file
			if(pathinfo($include, PATHINFO_EXTENSION) == "")
				$include .= '.css';
			
			# Make sure it's a CSS file
			if(pathinfo($include, PATHINFO_EXTENSION) != 'css')
			{
				$css = str_replace($matches[0][0], '', $css);
				//throw new Scaffold_Engine_Exception('');
				//Scaffold::$log->add('Invalid @include file - ' . $include,1);
				$this->server_import($css,$base);
			}

			# Find the file
			if($path = Scaffold::find_file($include,$base))
			{
				# Make sure it hasn't already been included	
				if(!$this->is_loaded($path))
				{
					$this->loaded[] = $path;
					
					$contents = file_get_contents($path);
					
					//$contents = new Scaffold_CSS($contents);
					//$contents->remove_inline_comments();
					
					# Check the file again for more imports
					$contents->string = $this->server_import($contents->string, realpath(dirname($path)) . '/');
					
					$rule->replace_with($contents);
					//$css = str_replace($matches[0][0], $contents, $css);
				}
	
				# It's already been included, we don't need to import it again
				else
				{
					$css = str_replace($matches[0][0], '', $css);
				}
			}
			else
			{
				throw new Exception('Can\'t find the @include file - <strong>' . $unique[0] . '</strong>');
			}
			
			$css = $this->server_import($css,$base);
		}

		return $css;
	}
	
	/**
	 * Checks if a file has already been loaded
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function is_loaded($file)
	{
		if(!in_array($file,$this->loaded))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}