<?php
/**
 * Mixins
 *
 * Allows you to use SASS-style mixins, essentially assigning classes
 * to selectors from within your css. You can also pass arguments through
 * to the mixin.
 *
 *		@mixin table-base {
		   th {
		     text-align: center;
		     font-weight: bold;
		   }
		   td, th {padding: 2px}
		 }
		 
		 @mixin left($dist) {
		   float: left;
		   margin-left: $dist;
		 }
		 
		 #data {
		   @include left(10px);
		   @include table-base;
		 }
 * 
 * @author Anthony Short
 */
class Scaffold_Extension_Mixins extends Scaffold_Extension
{
	/**
	 * Default settings which are used if the configuration
	 * settings from the file aren't set.
	 * @var array
	 */
	public $_defaults = array();

	/**
	 * Stores the mixins for debugging purposes
	 * @var array
	 */
	public $mixins = array();

	/**
	 * Extracts the mixin bases
	 * @param $source
	 * @return return type
	 */
	public function process($source)
	{	
		// Find any mixins
		if(preg_match_all('/\@mixin\s+([0-9a-zA-Z_\-]+) (\((.*?)\))? \s* \{/sx',$source->contents,$mixins))
		{
			$css = $source->contents;
			
			foreach($mixins[0] as $key => $mixin)
			{
				// Find the content of the mixins
				$mixin_start = strpos($css,$mixin);
				$pos = $start = $mixin_start + strlen($mixin);
				$depth = 0;
				$content = false;
				
				while($content == false)
				{
					$current = substr($css,$pos,1);
		
					if($current == '}')
					{
						if($depth == 0)
						{
							$content = substr($css, $start, $pos - $start);
						}
						else
						{
							$depth--;
						}
					}
					if($current == '{')
					{
						$depth++;
					}						
					if($pos == strlen($css))
					{
						throw new Exception('Unmatched }');
					}
						
					$pos++;
				}
				
				if($mixins[3][$key] != false)
				{
					$params = explode(',',$mixins[3][$key]);
					$default_params = array();
					
					foreach($params as $param_key => $param)
					{
						if(strstr($param, '='))
						{
							$equals = strpos($param, '=');
							$default_params[$param_key] = substr($param, $equals + 1, strlen($param) - $equals);
							$params[$param_key] = substr($param, 0, $equals - 1);
						}
						else
						{
							$default_params[$param_key] = false;
						}
					}
				}
				else
				{
					$params = false;
					$default_params = false;
				}
				
				$this->mixins[$mixins[1][$key]] = array
				(
					'params' 			=> $params,
					'default_params' 	=> $default_params,
					'content' 			=> trim($content)
				);
				
				$css = substr_replace($css, '', $mixin_start, $pos - $mixin_start);
			}
			
			// Now we need to replace them in the CSS
			if(preg_match_all('/\@include\s+([0-9a-zA-Z_\-]+)(\((.*?)\))?\s*\;/sx', $css, $includes))
			{
				foreach($includes[1] as $include_key => $include)
				{
					// If the mixin doesn't exist	
					if(!isset($this->mixins[$include]))
						throw new Exception("Mixin does not exist - $include");
				
					$mixin = $this->mixins[$include];
					$params = ($includes[3][$include_key] != false) ? explode(',',$includes[3][$include_key]) : false;
					$content = $mixin['content'];
					
					if($mixin['params'] !== false)
					{
						foreach($mixin['params'] as $key => $param)
						{
							// Missing a parameter
							if(!isset($params[$key]))
							{
								// No default value
								if($mixin['default_params'][$key] === false)
								{
									throw new Exception("Missing parameter $key from $include");
								}
								
								$params[$key] = $mixin['default_params'][$key];
							}
							
							$content = str_replace(trim($param),$params[$key],$content);
						}
					}

					$css = str_replace($includes[0][$include_key],$content,$css);
				}
			}	
			
			$source->contents = $css;
		}
	}
}