<?php
/**
 * Mixins
 *
 * Allows you to use SASS-style mixins, essentially assigning classes
 * to selectors from within your css. You can also pass arguments through
 * to the mixin.
 *
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_Mixins extends Scaffold_Extension
{
	/**
	 * Stores the mixins for debugging purposes
	 * @var array
	 */
	public $mixins = array();

	/**
	 * @param $source
	 * @param $scaffold
	 * @return void
	 */
	public function process($source,$scaffold)
	{	
		// Find any mixins
		if(preg_match_all('/\@mixin\s+([0-9a-zA-Z_\-]+) (\((.*?)\))? \s* \{/sx',$source->contents,$mixins))
		{
			$css = $source->contents;
			
			foreach($mixins[0] as $key => $mixin)
			{
				// Position of the mixin in the CSS
				$mixin_start = strpos($css,$mixin);
				
				// The position of the opening brace
				$start = $mixin_start + strlen($mixin) - 1;
				
				// Get the content within the braces
				$content = $scaffold->helper->string->match_delimiter('{','}',$start,$css);
				
				// The content without the braces
				$inner_content = trim($content,'{} ');
				
				// Parse the params
				if($mixins[3][$key] != false)
				{
					$params = explode(',',$mixins[3][$key]);
					$default_params = array();
					
					foreach($params as $param_key => $param)
					{
						// Set the default param if it exists
						if(strstr($param, '='))
						{
							$equals = strpos($param, '=');
							$default_params[$param_key] = trim(substr($param, $equals + 1, strlen($param) - $equals));
							$params[$param_key] = trim(substr($param, 0, $equals));
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
				
				// Build the mixin
				$this->mixins[$mixins[1][$key]] = array
				(
					'params' 			=> $params,
					'default_params' 	=> $default_params,
					'content' 			=> $inner_content
				);
				
				// Remove it from the CSS
				$css = substr_replace($css, '', $mixin_start, strlen($content) + strlen($mixin) - 1 );
			}
			
			// Now we need to replace them in the CSS
			if(preg_match_all('/\@include\s+([0-9a-zA-Z_\-]+)(\((.*?)\))?\s*\;/sx', $css, $includes))
			{
				foreach($includes[1] as $include_key => $include)
				{
					// If the mixin doesn't exist	
					if(!isset($this->mixins[$include]))
						throw new Exception("Mixin does not exist - $include");
				
					$mixin 		= $this->mixins[$include];
					$params 	= ($includes[3][$include_key] != false) ? explode(',',$includes[3][$include_key]) : false;
					
					$content 	= $mixin['content'];
					
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