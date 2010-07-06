<?php
/**
 * Mixins
 *
 * Allows you to use SASS-style mixins, essentially assigning classes
 * to selectors from within your css. You can also pass arguments through
 * to the mixin.
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
		print_r($source->get());exit;
		
		# Finds any selectors starting with =mixin-name
		if( $found = Scaffold_Helper_CSS::find_selectors('\@mixin\s+([0-9a-zA-Z_]+) (\((.*?)\))?', $source->get(), false) )
		{
			# Just to make life a little easier
			$full_base 		= $found[0];
			$base_names 	= $found['name'];
			$base_args 		= $found['args'];
			$base_props 	= $found['properties'];

			# Puts the mixin bases into a more suitable array
			foreach($base_names as $key => $value)
			{	
				$bases[$value]['properties'] = $base_props[$key];
				
				# If there are mixin arguments, add them
				$bases[$value]['params'] = ( $base_args[$key] != "" ) ? explode(',', $base_args[$key]) : array();
			}
						
			# Store this away for debugging
			$this->mixins = $bases;
			
			# Remove all of the mixin bases
			$source->set(
				str_replace($full_base,'',$source->get())
			);
		}
		
		print_r($this->mixins);exit;
	}

	/**
	 * The main processing function called by Scaffold. MUST return $css!
	 *
	 * @author Anthony Short
	 * @return $css string
	 */
	public function replace($css)
	{
		# Find the mixins
		if($mixins = $this->find_mixins($css->string))
		{
			# Loop through each of the found +mixins
			foreach($mixins[2] as $mixin_key => $mixin_name)
			{
				$css->string = str_replace($mixins[0][$mixin_key], $this->build_mixins($mixin_key, $mixins), $css->string);
			}
		}
	}
	
	/**
	 * Replaces the mixins with their properties
	 *
	 * @author Anthony Short
	 * @param $mixin_key - The bases array key corrosponding to the current mixin
	 * @param $mixins - An array of found mixins
	 * @return string
	 */
	public function build_mixins($mixin_key, $mixins, $already_mixed = array())
	{
		$bases =& $this->mixins;
		
		$mixin_name = $mixins[2][$mixin_key];
				
		if(isset($bases[$mixin_name]))
		{	
			$base_properties = $bases[$mixin_name]['properties'];
							
			# If there is no base for that mixin and we aren't in a recursion loop
			if(is_array($bases[$mixin_name]) AND !in_array($mixin_name, $already_mixed) )
			{
				$already_mixed[] = $mixin_name;

				# Parse the parameters of the mixin
				$params = $this->parse_params($mixins[0][$mixin_key], $mixins[4][$mixin_key], $bases[$mixin_name]['params']);

				# Replace the param variables				
				$new_properties = $this->replace_params($base_properties,$params,$mixin_name);
				
				# Parse conditionals if there are any in there
				$new_properties = $this->parse_conditionals($new_properties);
	
				# Find nested mixins
				if($inner_mixins = $this->find_mixins($new_properties))
				{
					# Loop through all the ones we found, skipping on recursion by passing
					# through the current mixin we're working on
					foreach($inner_mixins[0] as $key => $value)
					{
						# Parse the mixin and replace it within the property string
						$new_properties = str_replace($value, $this->build_mixins($key, $inner_mixins, $already_mixed), $new_properties);
					}
				}	
							
				# Clean up memory
				unset($inner_mixins, $params, $mixins);

				return preg_replace('/^(\s|\n|\r)*|(\n|\r|\s)*$/','',$new_properties);
			}
			elseif(in_array($mixin_name, $already_mixed))
			{
				throw new Exception('Recursion in mixin ' . $mixin_name,1);
			}
		}
		else
		{
			//throw new Scaffold_CSS_Exception('The mixin named <strong>' . $mixin_name . '</strong> doesn\'t exist.', 'myfile.css', 'css string');
		}
		
	}
	
	/**
	 * Replaces all of the params in a CSS string
	 * with the constants defined in the member variable $constants
	 * using PHP's interpolation.
	 */
	public function replace_params($css,$params,$mixin_name)
	{
		# Pull the constants into the local scope as variables
		extract($params, EXTR_SKIP);
		
		try
		{
			$css = stripslashes( eval('return "' . addslashes($css) . '";') );
		}
		catch(Exception $e)
		{
			$trace = $e->getTrace();
			$missing = str_replace('Undefined variable: ', '',$trace[0]['args'][1]);
			//throw new Exception('Missing variable inside the mixin <strong>'.$mixin_name.'</strong> named <strong>$' . $missing . '</strong>');
		}
		
		return $css;
	}
	
	/**
	 * Finds +mixins
	 *
	 * @author Anthony Short
	 * @param $string
	 * @return array
	 */
	public function find_mixins($string)
	{
		if(preg_match_all('/\+(([0-9a-zA-Z_-]*?)(\((.*?)\))?)\;/xs', $string, $found))
			return $found;
	}
	
	/**
	 * Parses the parameters of the base
	 *
	 * @author Anthony Short
	 * @param $params
	 * @return array
	 */
	public function parse_params($mixin_name, $params, $function_args = array())
	{
		$parsed = array();
		
		# Make sure any commas inside ()'s, such as rgba(255,255,255,0.5) are encoded before exploding
		# so that it doesn't break the rule.
		if(preg_match_all('/\([^)]*?,[^)]*?\)/',$params, $matches))
		{
			foreach($matches as $key => $value)
			{
				$original = $value;
				$new = str_replace(',','#COMMA#',$value);
				$params = str_replace($original,$new,$params);
			}
		}

		$mixin_params = ($params != "") ? explode(',', $params) : array();
		
		# Loop through each function arg and create the parsed params array
		foreach($function_args as $key => $value)
		{
			$v = explode('=', $value);
			
			# Remove the $ so we can set it as a constants
			$v[0] = str_replace('$','',$v[0]);

			# If the user didn't include one of the params, we'll check to see if a default is available			
			if(count($mixin_params) == 0 || !isset($mixin_params[$key]))
			{	
				# If there is a default value for the param			
				if(strstr($value, '='))
				{
					//$parsed_value = Constants::replace(Scaffold_Utils::unquote( trim($v[1]) ));
					//$parsed[trim($v[0])] = (string)$parsed_value;
				}
				
				# Otherwise they've left one out
				else
				{
					throw new Exception("Missing mixin parameter - " . $mixin_name);
				}
			}
			else
			{
				$value = (string)Scaffold_Utils::unquote(trim($mixin_params[$key]));
				$parsed[trim($v[0])] = str_replace('#COMMA#',',',$value);
			}		
		}

		return $parsed;
	}
	
	/**
	 * Parses a string for CSS-style conditionals
	 *
	 * @param $string A string of css
	 * @return void
	 **/
	public function parse_conditionals($string = "")
	{		
		# Find all @if, @else, and @elseif's groups
		if($found = $this->find_conditionals($string))
		{
			# Go through each one
			foreach($found[1] as $key => $value)
			{
				$result = false;
				
				# Find which equals sign was used and explode it
				preg_match("/\!=|\!==|===|==|\>|\<|\>=|\<=/", $value, $match); 

				# Explode it out so we can test it.
				$exploded = explode($match[0], $value);
				$val = trim($exploded[0]);

				if(preg_match('/[a-zA-Z]/', $val) && (strtolower($val) != "true" && strtolower($val) != "false") )
				{					
					$value = str_replace($val, "'$val'", $value);
				}
				
				eval("if($value){ \$result = true;}");
				
				# When one of them is if true, replace the whole group with the contents of that if and continue
				if($result)
				{
					$string = str_replace($found[0][$key], $found[3][$key], $string);
				}
				# If there is an @else
				elseif($found[5] != "")
				{
					$string = str_replace($found[0][$key], $found[7][$key], $string);
				}
				else
				{
					$string = str_replace($found[0][$key], '', $string);
				}	
			}
		}
		return $string;
	}
	
	/**
	 * Finds if statements in a string
	 *
	 * @author Anthony Short
	 * @param $string
	 * @return array
	 */
	public function find_conditionals($string = "")
	{
		$recursive = 2; 
		
		$regex = 
			"/
				
				# Find the @if's
				(?:@(?:if))\((.*?)\)
				
				# Return all inner selectors and properties
				(
					(?:[0-9a-zA-Z\_\-\*&]*?)\s*
					\{	
						((?:[^{}]+|(?{$recursive}))*)
					\}
				)
				
				\s*
				
				(
					# Find the @elses if they exist
					(@else)

					# Return all inner selectors and properties
					(
						(?:[0-9a-zA-Z\_\-\*&]*?)\s*
						\{	
							((?:[^{}]+|(?{$recursive}))*)
						\}
					)
				)?
				
			/xs";
		
		if(preg_match_all($regex, $string, $match))
		{
			return $match;
		}
		else
		{
			return array();
		}
	}

}