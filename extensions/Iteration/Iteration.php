<?php

/**
 * Iteration
 *
 * @author Anthony Short
 */
class Scaffold_Engine_Iteration
{
	
	/**
	 * Parses @fors within the css
	 *
	 * @author Anthony Short
	 * @param $string
	 * @return string
	 */
	public function parse($css)
	{
		if($found = $this->find_fors($css))
		{	
			foreach($found[0] as $key => $value)
			{				
				$s = "";
				
				$constant 	= $found[1][$key];
				$from 		= Constants::replace($found[2][$key]);
				$to 		= Constants::replace($found[3][$key]);
				$content 	= $found[6][$key];
				
				for ($i = $from; $i <= $to; $i++)
				{
					Constants::set($constant,$i);
					$s .= Constants::replace($content);
				}
				
				$css = str_replace($found[0][$key], $s, $css);				
			}
		}

		return $css;
	}

	/**
	 * Finds for statements in a string
	 *
	 * @author Anthony Short
	 * @param $string
	 * @return array
	 */
	public function find_fors($string = "")
	{		
		$regex = 
			'/
				# For info
				\@for\s\$(.+?)\sfrom\s(.+?)\sto\s([^{]+)
	
				(
					([0-9a-zA-Z\_\-\@*&]*?)\s*
					\{	
						( (?: [^{}]+ | (?4) )*)
					\}
				)
	
			/ixs';
		
		if(preg_match_all($regex, $string, $match))
		{
			return $match;
		}
		else
		{
			return false;
		}
	}

}