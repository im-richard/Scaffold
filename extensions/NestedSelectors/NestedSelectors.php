<?php

/**
 * NestedSelectors
 *
 * @author Anthony Short
 */
class Scaffold_Extension_NestedSelectors extends Scaffold_Extension
{
	/**
	 * The character used to represent the parent selector
	 */
	const PARENT = '&';

	/**
	 * Array of selectors to skip and keep them nested.
	 * It just checks if the string is present, so it can
	 * just be part of a string, like the @media rule is below.
	 * @var array
	 */
	protected $skip = array
	(
		'@media'
	);
	
	/**
	 * Stores the CSS comments so they don't break processing
	 * @var array
	 */
	private $comments = array();

	/**
	 * @access public
	 * @return $source Scaffold_Source
	 */
	public function pre_process($source)
	{
		$output = "";
		$css = $source->get();
		$xml = $this->to_xml($css);

		foreach($xml->children() as $key => $value)
		{
			$attributes = (array)$value->attributes();
			$attributes = $attributes['@attributes'];
	
			# Parse properties
			if($key == 'property')
			{
				if($attributes['name'] == 'comment')
				{		
					$output .= $this->parse_comment($attributes['value']);
				}
			}
			
			# Parse imports
			elseif($key == 'import')
			{
				$imports[] = array
				(
					'url' => $attributes['url'],
					'media' => $attributes['media']
				);
			}
			
			# Parse normal rules
			else
			{
				$output .= $this->parse_rule($value);
			}
		}
		
		if(isset($imports))
		{
			foreach(array_reverse($imports) as $import)
			{
				$output = "@import '{$import['url']}' " . $import['media'] . ";" . $output;
			}
		}

		$source->set( str_replace('#NEWLINE#', "\n", $output) );
	}
	
	/**
	 * Parses the comment nodes
	 *
	 * @param $comment
	 * @return string
	 */
	private function parse_comment($comment)
	{
		return '/*'. $this->comments[$comment] .'*/';
	}
	
	/**
	 * Parse the css selector rule
	 *
	 * @author Anthony Short
	 * @param $rule
	 * @return return type
	 */
	public function parse_rule($rule, $parent = '')
	{
		$css_string = "";
		$property_list = "";
		$parent = trim($parent);
		$skip = false;
	
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
		# Convert comments
		$this->comments = array();
		$xml = preg_replace_callback('/\/\*(.*?)\*\//sx', array($this,'encode_comment') ,$css);

		# Convert imports
		$xml = preg_replace(
		    '/
		        @import\\s+
		        (?:url\\(\\s*)?      # maybe url(
		        [\'"]?               # maybe quote
		        (.*?)                # 1 = URI
		        [\'"]?               # maybe end quote
		        (?:\\s*\\))?         # maybe )
		        ([a-zA-Z,\\s]*)?     # 2 = media list
		        ;                    # end token
		    /x',
		    "<import url=\"$1\" media=\"$2\" />",
		    $xml
		);
		
		# Convert other at-rules

		# Add semi-colons to the ends of property lists which don't have them
		$xml = preg_replace('/((\:|\+)[^;])*?\}/', "$1;}", $xml);

		# Transform properties
		$xml = preg_replace('/([-_A-Za-z*]+)\s*:\s*([^;}{]+)(?:;)/ie', "'<property name=\"'.trim('$1').'\" value=\"'.trim('$2').'\" />\n'", $xml);
		
		# Transform selectors
		$start = Scaffold_Helper_CSS::$selector_start;
		$selector = Scaffold_Helper_CSS::$selector;
		
		$xml = preg_replace('/(\s*)('.$start.$selector.'*?)\{/me', "'$1\n<rule selector=\"'.htmlentities(preg_replace('/\s+/', ' ', trim('$2'))).'\">\n'", $xml);

		# Close rules
		$xml = preg_replace('/\;?\s*\}/', "\n</rule>", $xml);
		
		# Indent everything one tab
		$xml = preg_replace('/\n/', "\r\t", $xml);
				
		# Tie it up with a bow
		$xml = '<?xml version="1.0" ?'.">\r<css>\r\t$xml\r</css>\r";
		
		try
		{
			$xml = simplexml_load_string($xml);
		}
		catch(Exception $e)
		{
			// Error message
			$title 			= "Nested Selectors Syntax Error";
			$message 		= 'There was an error when the Nested Selectors extension tried to convert the CSS into XML.<pre><code>'.htmlentities($xml).'</code></pre>';
			
			// Throw the error
			throw new Scaffold_Extension_Exception($title,$message);
		}
		
		return $xml;
	}
	
	/**
	 * Turns CSS comments into an xml format
	 *
	 * @param $comment
	 * @return return type
	 */
	protected function encode_comment($comment)
	{
		// Encode new lines
		$comment = preg_replace('/\n|\r/', '#NEWLINE#',$comment[1]);
		
		// Save it
		$this->comments[] = $comment;

		return "<property name=\"comment\" value=\"" . (count($this->comments) - 1) . "\" />";
	}

}