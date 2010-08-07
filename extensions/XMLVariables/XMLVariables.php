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
	public $_defaults = array(
		'files' => array()
	);
	
	/**
	 * @var array
	 */
	public $variables = array();
	
	/**
	 * Loop through each file and load the XML variables
	 * @access public
	 * @param $source
	 * @return string
	 */
	public function initialize($source,$scaffold)
	{
		// List of XML files to parse
		$files = $this->config['files'];
		
		foreach($files as $file)
		{
			// Try and load the file
			$file = $scaffold->loader->find_file($file,false);
			
			if(!is_file($file)) continue;
			
			// The variables
			$xml = simplexml_load_file($file);
			$this->_load_variables_from_xml($xml);
		}
	}
	
	/**
	 * Load variables from an XML object
	 * @access private
	 * @param $xml
	 * @return void
	 */
	private function _load_variables_from_xml($xml,$group = 'var')
	{
		foreach($xml->variable as $name => $variable)
		{
			$key 	= (string)$variable->attributes()->name;
			$value 	= (string)$variable;
			$group  = (string)$group;
			
			if(isset($this->variables[$group]) === false)
			{
				$this->variables[$group] = array();
			}
			
			$this->variables[$group][$key] = $value; 
		}
		
		foreach($xml->group as $vargroup)
		{
			$group = (string)$vargroup->attributes()->name;
			$this->_load_variables_from_xml($vargroup,$group);
		}
	}
	
	/**
	 * Merge our variables with the variables object
	 * @access public
	 * @param $variables Scaffold_Extension_Variables
	 * @return void
	 */
	public function variables_start(Scaffold_Source $source,Scaffold_Extension_Variables $var)
	{
		$var->variables = array_merge($var->variables,$this->variables);
	}
}