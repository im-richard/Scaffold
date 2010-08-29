<?php
/**
 * Scaffold_Extension_XMLVariables
 *
 * Preloads variables to use within the CSS from XML files.
 * 
 * @package 		Scaffold
 * @author 			Anthony Short <anthonyshort@me.com>
 * @copyright 		2009-2010 Anthony Short. All rights reserved.
 * @license 		http://opensource.org/licenses/bsd-license.php  New BSD License
 * @link 			https://github.com/anthonyshort/csscaffold/master
 */
class Scaffold_Extension_XMLVariables extends Scaffold_Extension
{	
	/**
	 * @var array
	 */
	public $variables = array();
	
	/**
	 * @var array
	 */
	public $_defaults = array(
		'files' => array()
	);
	
	/**
	 * Loop through each file and load the XML variables
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function variables_start($source,$scaffold)
	{
		// Find all loaders within the CSS
		$urls = $this->find_directives($source->contents);
		
		foreach($urls[1] as $file)
		{
			if($found = $source->find($file))
			{
				$this->load($found);
			}
		}
		
		// Remove the @variable url directives from the CSS string
		foreach($urls[0] as $directive)
		{
			$source->contents = str_replace( $directive, '', $source->contents);
		}
		
		foreach($this->config['files'] as $file)
		{
			if($found = $scaffold->helper->load->file($file,false))
			{
				$this->load($found);
			}
		}
	}
	
	/**
	 * Loads an XML file and loads it's variables into the object.
	 * Returns true if successful
	 * @access public
	 * @param $file
	 * @return boolean
	 */
	public function load($file)
	{
		if(!is_file($file)) return false;
		$this->variables = $this->to_array(simplexml_load_file($file));
		return true;
	}
	
	/**
	 * Saves an array of variables as XML
	 * @access public
	 * @param $vars array
	 * @return boolean
	 */
	public function save(array $vars,$file)
	{
		$xml = "<?xml version=\"1.0\" ?><variables>";
		
		foreach($vars as $group => $group_vars)
		{
			$xml .= '<group name="'.$group.'">';
			
			foreach($group_vars as $key => $value)
			{
				$xml .= '<variable name="'.$key.'">'.$value.'</variable>';
			}
			
			$xml .= '</group>';
		}
		
		$xml .= '</variables>';
		
		return file_put_contents($file,$xml);
	}
	
	/**
	 * Load variables from an XML object
	 * @access public
	 * @param $xml
	 * @return array
	 */
	public function to_array($xml,$group = 'var')
	{
		$variables = array();

		foreach($xml->variable as $variable)
		{
			$key 	= (string)$variable->attributes()->name;
			$value 	= (string)$variable;
			$group  = (string)$group;
			
			$variables[$group][$key] = $value; 
		}
		
		foreach($xml->group as $vargroup)
		{
			$group = (string)$vargroup->attributes()->name;
			$variables = array_merge($variables,$this->to_array($vargroup,$group));
		}
		
		return $variables;
	}
	
	/**
	 * Merge our variables with the variables object before they are replaced
	 * @access public
	 * @param $variables Scaffold_Extension_Variables
	 * @return void
	 */
	public function variables_replace(Scaffold_Source $source,Scaffold_Extension_Variables $var)
	{	
		$var->variables = $this->helper->array->merge_recursive($var->variables,$this->variables);
	}
	
	/**
	 * Finds all @variable urls
	 * @access public
	 * @param $str
	 * @return array
	 */
	public function find_directives($str)
	{
		preg_match_all(
		    '/
		        @variables\\s+
		        (?:url\\(\\s*)?      # maybe url(
		        [\'"]?               # maybe quote
		        (.*?)                # 1 = URI
		        [\'"]?               # maybe end quote
		        (?:\\s*\\))?         # maybe )
		        ;                    # end token
		    /x'
		    ,$str
		    ,$found
		);

		return $found;
	}
}