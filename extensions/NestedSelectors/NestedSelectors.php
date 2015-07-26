<?php
/**
 * Scaffold_Extension_NestedSelectors
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
	 * @return void
	 */
	public function post_process($source,$scaffold)
	{
		$filename = empty($source->path) ? $source->url : $source->path;
		$xml = $this->to_xml($source->contents, $filename);		
		$source->contents = html_entity_decode($this->_parse_children($xml->children()));
	}

	/**
	 * Parse XML nodes
	 * @access public
	 * @param $children SimpleXMLElement
	 * @return string
	 */
	private function _parse_children(SimpleXMLElement $children, $parent = null)
	{
		$output = '';

		foreach($children as $key => $value)
		{						
			# Selectors
			if($key == 'block')
			{
				# Type of block
				$type = (string)$value->attributes()->type;
				
				# Content (including properties and anything else that isn't a block)
				$content = (string)$value;
				
				if($type == 'selector')
				{
					# Selector name
					$selector = (string)$value->attributes()->rule;
					
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
								if(strstr($sv,htmlentities('&')))
								{
									$new[] = str_replace(htmlentities('&'), $pv, trim($sv));
								}
								else
								{
									$new[] = $pv.' '.trim($sv);
								}
							}
						}
	
						$selector = implode(",\n",$new);
					}
					
					# Parse the inner content
					$output .= $selector . "\n{\n" . preg_replace('/\t+/',"\t",$content) . "\n}\n";
					
					# Add the nested selectors after it
					$output .= $this->_parse_children($value->block,$selector);
				}
				
				# Block directive
				elseif($type == 'directive')
				{	
					# The directive
					$output .= '@'.(string) $value->attributes()->name.' '.(string) $value->attributes()->params;
					
					# Properties and other content
					$output .= "\n{\n" . $content . $this->_parse_children($value->children()) . "\n}\n";
				}
			}
			
			# Single line directives
			elseif($key == 'directive')
			{
				$output .= '@'.(string) $value->attributes()->name.' '.(string) $value->attributes()->params . ';' . "\n";
			}
			
			# Comments
			elseif($key == 'comment_block')
			{
				//$output .= '/*' . (string)$value . '*/' . "\n\n";
			}
		}
		
		return $output;
	}

	/**
	 * Transforms CSS into XML
	 *
	 * @return string $css
	 */
	public function to_xml($css, $filename = null)
	{
		# Convert comments
		$xml = preg_replace_callback('/\/\*(.*?)\*\//sx', array($this,'encode_comment') ,$css);
		
		# Selectors
		$xml = preg_replace_callback('/(\;|^|{|}|\s*)\s*([^{};]*?)\{/sx', array($this,'_rule'), $xml);

		# Convert single line at-rules
		$xml = preg_replace_callback('/@([^\s]+)\s(.*?);/',array($this,'single_line_directive'),$xml);
		
		# Add semi-colons to the ends of property lists which don't have them
		$xml = preg_replace('/((\:|\+)[^;])*?\}/', "$1;}", $xml);
		
		# Close rules
		$xml = preg_replace('/\;?\s*\}/', "\n</block>", $xml);
		
		# Transform properties
		//$xml = preg_replace_callback('/([-_A-Za-z*]+)\s*:\s*([^;}{]+)(?:;)/sx', array($this,'_property'), $xml);

		$xml = '<?xml version="1.0" ?><css>'.$xml.'</css>';
	
		libxml_use_internal_errors(true); 
		$result = simplexml_load_string($xml);
		$errors = libxml_get_errors();
		libxml_clear_errors();
		foreach ($errors as $error) {
			echo $this->display_xml_error($error,$xml);
			throw new Exception('NestedSelectors failed on ['.$filename.']: '.$error->message, $error->code);
		}
		
		return $result;
	}
	
	protected function display_xml_error($error, $xmlstr)
	{
		$xml = explode("\n", $xmlstr);
	
	    $return  = $xml[$error->line - 1] . "\n";
	    $return .= str_repeat('-', $error->column) . "^\n";
	
	    switch ($error->level) {
	        case LIBXML_ERR_WARNING:
	            $return .= "Warning $error->code: ";
	            break;
	         case LIBXML_ERR_ERROR:
	            $return .= "Error $error->code: ";
	            break;
	        case LIBXML_ERR_FATAL:
	            $return .= "Fatal Error $error->code: ";
	            break;
	    }
	
	    $return .= trim($error->message) .
	               "\n  Line: $error->line" .
	               "\n  Column: $error->column";
	
	    if ($error->file) {
	        $return .= "\n  File: $error->file";
	    }
	
	    return "$return\n\n--------------------------------------------\n\n";
	}
	
	protected function encode_comment($comment)
	{
		//return "<comment_block>".htmlentities($comment[1])."</comment_block>";
	}
	
	protected function single_line_directive($rule)
	{
		$result = '<directive name="'.$rule[1].'" params="'.str_replace('"','\'',$rule[2]).'" />';
		return $result;
	}
	
	protected function _property($property)
	{
		$name 	= htmlentities(trim($property[1]));
		$value 	= htmlentities(trim($property[2]));
		return '<property name="'.$name.'">'.$value.'</property>';
	}
	
	protected function _rule($rule)
	{
		$selector = htmlentities(trim($rule[2]));
		
		if($selector[0] != '@')
		{
			return $rule[1] . "\n<block type=\"selector\" rule=\"$selector\">";
		}
		else
		{
			preg_match('/@([^\s]+)\s*(.*)/sx',$selector,$match);
			return $rule[1] . "<block type=\"directive\" name=\"{$match[1]}\" params=\"{$match[2]}\">";
		}
	}
}