<?php
/**
 * NestedSelectors
 *
 * Allows the use of nested selectors
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_NestedSelectors extends Scaffold_Extension
{	
	/**
	 * @access public
	 * @param $source Scaffold_Source
	 * @param $scaffold Scaffold
	 */
	public function post_process($source,$scaffold)
	{
		$tokenizer 			= new PHP_CodeSniffer_Tokenizers_CSS();
		$this->tokens 		= $tokenizer->tokenizeString($source->contents);
		
		print_r($this->tokens);exit;
		
		$source->contents 	= $this->_parse_tokens();
	}
	
	private function _parse_tokens()
	{
		$output = '';
		$depth = 0;
		$total = count($this->tokens);
		
		while($this->_current != $total)
		{
			$content 	= $this->tokens[$this->_current]['content'];
			$type 		= $this->tokens[$this->_current]['type'];
			
			// Comments and whitespace
			if( in_array($type, array('T_COMMENT','T_WHITESPACE')) )
			{
				$output .= $content;
			}
			
			// Closing block
			if($type == 'T_CLOSE_CURLY_BRACKET')
			{
				$depth++;
				$output .= $content;
			}
			
			// Opening block
			if($type == 'T_OPEN_CURLY_BRACKET')
			{
				$depth--;
				$output .= $content;
			}
			
			// Selector
			if($type == 'T_STRING' OR $type == 'T_HASH' OR $type == 'T_GREATER_THAN')
			{
				$output .= $content;
			}
			
			$this->_current++;
		}
		
		print_r($output);exit;
		
		return $output;
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
	 * Transforms CSS into XML
	 *
	 * @return string $css
	 */
	public function to_xml($css)
	{
		# Convert comments
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
		
		# Convert single line at-rules
		$xml = preg_replace_callback('/@([^\s]+)\s(.*?);/',array($this,'single_line_directive'),$xml);
		
		# Add semi-colons to the ends of property lists which don't have them
		$xml = preg_replace('/((\:|\+)[^;])*?\}/', "$1;}", $xml);

		# Transform properties
		$xml = preg_replace('/([-_A-Za-z*]+)\s*:\s*([^;}{]+)(?:;)/ie', "'<property name=\"'.htmlentities(trim('$1')).'\">'.htmlentities(trim('$2')).'</property>'", $xml);
		
		# Convert blocks
		if(preg_match_all('/'.$start.'.*?{/xs',$xml,$rules))
		{
			print_r($rules);exit;
			foreach($rules[0] as $rule)
			{
				$start 				= strpos($xml,$rule) + strlen($rule) - 1;
				$contents 			= $this->string->match_delimiter('{','}',$start,$xml);
				$inner			 	= trim($contents,'{}');
				
				// Directive
				if($rule[0] == '@')
				{
					preg_match('/@([^\s]+)\s([^{]+)/', $rule,$match);
					
					$name 	= htmlentities($match[1]);
					$params = htmlentities($match[2]);
					
					$replace = "<directive name=\"$name\" params=\"$params\">$inner</directive>";
				}
				
				// Selector
				else
				{
					$selector = htmlentities(rtrim($rule,' {'));
			
					$replace = "<rule selector=\"$selector\">$inner</rule>";	
				}

				$xml = substr_replace($xml,$replace,strpos($xml,$rule), strlen($rule) - 1 + strlen($contents));
			}
		}
		
		# Close rules
		$xml = preg_replace('/\;?\s*\}/', "\n</rule>", $xml);
		
		$xml = preg_replace('/(\s*|^)('.$start.'[^{]*?)\{/mexs', "'$1\n<rule selector=\"'.htmlentities(trim('$2')).'\">\n'", $xml);
		
		$xml = '<?xml version="1.0" ?><css>'.$xml.'</css>';
		
		return simplexml_load_string($xml);
	}
	
	/**
	 * Turns CSS comments into an xml format
	 * @param $comment
	 * @return return type
	 */
	protected function encode_comment($comment)
	{
		return "<comment_block>".htmlentities($comment[1])."</comment_block>";
	}
	
	protected function single_line_directive($rule)
	{
		return '<directive name="'.$rule[1].'" params="'.$rule[2].'" />';
	}
}