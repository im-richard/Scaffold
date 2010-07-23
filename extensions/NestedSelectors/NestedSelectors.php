<?php

/**
 * NestedSelectors
 *
 * @author Anthony Short
 */
class Scaffold_Extension_NestedSelectors extends Scaffold_Extension
{
	/**
	 * @var Scaffold_Helper_String
	 */
	public $string;
	
	/**
	 * @var Scaffold_Helper_CSS
	 */
	public $css;
	
	/**
	 * @access public
	 * @param $source
	 * @param $scaffold
	 * @return void
	 */
	public function __construct($scaffold,$config)
	{
		$this->config = array_merge($this->_defaults,$config);
		$this->string = $scaffold->helper->string;
		$this->css = $scaffold->helper->css;
	}

	/**
	 * @access public
	 * @return $source Scaffold_Source
	 */
	public function pre_process($source,$scaffold)
	{
		$this->selector_start = $this->css->selector_start;
		$this->selector = $this->css->selector;
	
		$output = "";
		$xml = $this->to_xml($source->contents);
		
		$source->contents = $this->parse_children($xml->children());
		
		print_r($source->contents);exit;

		$source->set( str_replace('#NEWLINE#', "\n", $output) );
	}
	
	/**
	 * Parse XML nodes
	 *
	 * @author your name
	 * @param $param
	 * @return return type
	 */
	public function parse_children(SimpleXMLElement $children, $parent = null)
	{
		$output = '';
		$content = '';

		foreach($children as $key => $value)
		{						
			# Selectors
			if($key == 'rule')
			{
				# Selector name
				$selector = (string)$value->attributes()->selector;
				
				# We're in a nested rule
				if($parent !== null)
				{		
					# We need to append each parent to each child selector
					$parents 	= explode(',',$parent);
					$child 		= explode(',',htmlentities($selector));
					$new		= array();
					
					foreach($child as $sv)
					{						
						foreach($parents as $pv)
						{
							if(strstr($sv,'&'))
							{
								$new[] = str_replace(htmlentities('&'), $pv, $sv);
							}
							else
							{
								$new[] = $pv.' '.$sv;
							}
						}
					}

					$selector = implode(',',$new);
					
					
				}

				# Parse the nested selectors
				$content = $this->parse_children($value->rule,$selector);
				
				# Remove the rules
				unset($value->rule);
				
				# Parse the inner content
				$output .= $selector . '{' . $this->parse_children($value->children()) . '}';
				
				# Add the extracted nested selectors
				$output .= $content;
			}
	
			# Properties
			elseif($key == 'property')
			{
				$output .= $value->name . ':' . $value->value . ';';
			}
			
			# @ rules
			elseif($key == 'at_rule')
			{
				$output .= '@'.(string) $value->attributes()->name.' '.(string) $value->attributes()->params;
				
				# With block
				if((array)$value->children() !== array())
				{
					$output .= '{' . $this->parse_children($value) . '}';
				}
				
				# Without block
				else { $output .= ';'; }
			}
		}
		
		return $output;
	}
	
	/**
	 * Parse the css selector rule
	 *
	 * @author Anthony Short
	 * @param $rule
	 * @return return type
	 */
	public function parse_rulez($rule, $parent = '')
	{
		$css_string = "";
		$property_list = "";
		$parent = trim($parent);
	
		# Get the selector and store it away
		foreach($rule->attributes() as $type => $value)
		{
			$child = (string)$value;
			
			# if its NOT a root selector and has parents
			if($parent != "")
			{				
				$parent = explode(",", $parent);

				foreach($parent as $parent_key => $parent_value)
				{
					$parent[$parent_key] = $this->parse_selector(trim($parent_value), $child);
				}
				
				$parent = implode(",", $parent);
			}
			
			elseif( strstr($child,'@media') )
			{
				$skip = true;
				$parent = $child;
			}
			
			# Otherwise it's a root selector
			else
			{
				$parent = $child;
			}
		}

		foreach($rule->property as $p)
		{
			$property = (array)$p->attributes(); 
			$property = $property['@attributes'];
			
			if($property['name'] == 'comment')
			{
				$property_list .= $this->parse_comment($property['value']);
			}
			else
			{
				$property_list .= $property['name'].":".$property['value'].";";
			}
		}
		
		# Create the css string
		if($property_list != "" && $skip !== true)
		{
			$css_string .= $parent . "{" . $property_list . "}";
		}

		foreach($rule->rule as $inner_rule)
		{			
			# If the selector is in our skip array in the 
			# member variable, we'll leave the selector as nested.
			foreach($this->skip as $selector)
			{				
				if(strstr($parent, $selector))
				{
					$skip = true;
					continue;
				}
			}
			
			# We don't want the selectors inside @media to have @media before them
			if($skip)
			{
				$css_string .= $this->parse_rule($inner_rule, '');
			}
			else
			{
				$css_string .= $this->parse_rule($inner_rule, $parent);
			}
		}
		
		# Build our @media string full of these properties if we need to
		if($skip)
		{
			$css_string = $parent . "{" . $css_string . "}";
		}

		return $css_string;
	}
		
	/**
	 * Parses the parent and child to find the next parent
	 * to pass on to the function
	 *
	 * @author Anthony Short
	 * @param $parent
	 * @param $child
	 * @param $atmedia Is this an at media group?
	 * @return string
	 */
	public function parse_selector($parent, $child)
	{
		# If there are listed parents eg. #id, #id2, #id3
		if(strstr($child, ","))
		{
			$parent = $this->split_children($child, $parent);
		}
		
		# If the child references the parent selector
		elseif (strstr($child, self::PARENT))
		{
			$parent = str_replace(self::PARENT, $parent, $child);
		}
		
		# Otherwise, do it normally
		else
		{
			$parent = "$parent $child";
		}
		
		return $parent;
	}
	
	/**
	 * Splits selectors with , and adds the parent to each
	 *
	 * @author Anthony Short
	 * @param $children
	 * @param $parent
	 * @return string
	 */
	public function split_children($children, $parent)
	{
		$children = explode(",", $children);
												
		foreach($children as $key => $child)
		{
			# If the child references the parent selector
			if (strstr($child, self::PARENT))
			{
				$children[$key] = str_replace(self::PARENT, $parent, $child);	
			}
			else
			{
				$children[$key] = "$parent $child";
			}
		}
		
		return implode(",",$children);
	}

	/**
	 * Transforms CSS into XML
	 *
	 * @return string $css
	 */
	public function to_xml($css)
	{
		# So it won't break the XML
		$css = htmlentities($css);
		
		# Convert comments
		$xml = preg_replace_callback('/\/\*(.*?)\*\//sx', array($this,'encode_comment') ,$css);
		
		# Convert single line at-rules
		$xml = preg_replace('/@(' . $this->selector . '+?)\s(.*?);/',"<at_rule name=\"$1\" params=\"$2\" />",$xml);
		print_r($xml);exit;
		# Convert at-rules with blocks
		if(preg_match_all('/@(' . $this->selector . '+?)\s(.*?){/',$xml,$atrules))
		{
			foreach($atrules[0] as $atrule_key => $atrule)
			{
				$start 				= strpos($xml,$atrule) + strlen($atrule) - 1;
				$contents 			= $this->string->match_delimiter('{','}',$start,$xml);
				$inner_contents 	= trim($contents,'{}');
				
				$replace = "<at_rule 
								name=\"".htmlentities($atrules[1][$atrule_key])."\" 
								params=\"".trim(htmlentities($atrules[2][$atrule_key]))."\">" . htmlentities($inner_contents) . 
							"</at_rule>";

				$xml = substr_replace($xml,$replace,strpos($xml,$atrule), strlen($atrule) - 1 + strlen($contents));
			}
		}

		# Add semi-colons to the ends of property lists which don't have them
		$xml = preg_replace('/((\:|\+)[^;])*?\}/', "$1;}", $xml);

		# Transform properties
		$xml = preg_replace('/([-_A-Za-z*]+)\s*:\s*([^;}{]+)(?:;)/ie', "'<property><name>'.htmlentities(trim('$1')).'</name><value>'.htmlentities(trim('$2')).'</value></property>'", $xml);
		
		# Close rules
		$xml = preg_replace('/\;?\s*\}/', "\n</rule>", $xml);
		
		# Transform selectors
		$start = $this->selector_start;
		$selector = $this->selector;
		
		$xml = preg_replace('/(\s*|^)('.$start.$selector.'*?)\{/me', "'$1\n<rule selector=\"'.htmlentities(trim('$2')).'\">\n'", $xml);
		
		$xml = '<?xml version="1.0" ?><css>'.$xml.'</css>';
		
		print_r($xml);exit;
		return simplexml_load_string($xml);
	}
	
	/**
	 * Turns CSS comments into an xml format
	 *
	 * @param $comment
	 * @return return type
	 */
	protected function encode_comment($comment)
	{
		return "<comment-block>".htmlentities($comment[1])."</comment-block>";
	}

}