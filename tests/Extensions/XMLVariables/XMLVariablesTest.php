<?php

class Scaffold_Extension_XMLVariablesTest extends PHPUnit_Framework_TestCase
{
	private $source;
	private $scaffold;
	private $object;
	
	protected function setUp()
	{
		$system 			= realpath(__DIR__.'/../../../');
		$container 			= new Scaffold_Container($system);
		$this->scaffold 	= $container->build();
		$this->object 		= new Scaffold_Extension_XMLVariables();
	}

	public function test_variables_start()
	{
		$source = new Scaffold_Source_File(__DIR__ . '/_files/load.css');
		$this->object->variables_start($source,$this->scaffold);
		
		$this->assertEquals(array(
			'var' 			=> array('font_family'=>'Helvetica'),
			'colors' 		=> array('text'=>'#000'),
			'ad' 			=> array('height'=>'300px','width'=>'250px'),
		),$this->object->variables);
	}
	
	public function test_save()
	{
		$variables = array(
			'var' 			=> array('font_family'=>'Helvetica'),
			'colors' 		=> array('text'=>'#000'),
			'ad' 			=> array('height'=>'300px','width'=>'250px'),
		);
		
		$this->object->save($variables,__DIR__.'/_cache/saved_variables.xml');
	}
	
	private function get_expected($file)
	{
		return file_get_contents(__DIR__ . '/_files/expected/'.$file);
	}
	
	private function get_original($file)
	{
		return new Scaffold_Source_File(__DIR__ . '/_files/original/'.$file);
	}
	
	private function remove_empty_lines($value)
	{
		$value = trim($value);
		return ($value != '');
	}
}