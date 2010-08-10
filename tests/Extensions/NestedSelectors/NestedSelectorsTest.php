<?php

class Scaffold_Extension_NestedSelectorsTest extends PHPUnit_Framework_TestCase
{
	private $source;
	private $scaffold;
	private $object;
	
	protected function setUp()
	{
		$system 			= realpath(__DIR__.'/../../../');
		$container 			= new Scaffold_Container($system);
		$this->scaffold 	= $container->build();
		$this->object 		= new Scaffold_Extension_NestedSelectors();
	}
	
	/**
	 * @test
	 */
	public function test_process()
	{
		$dir = __DIR__ . '/_files/original/';
		
		foreach(glob($dir.'*.css') as $original)
		{
			# The expected output
			$expected = file_get_contents(str_replace('/_files/original/','/_files/expected/',$original));
			
			# Create and parse the source
			$source = new Scaffold_Source_File($original);
			$this->object->post_process($source,$this->scaffold);
			
			// Remove unnecessary whitespace
			$actual = trim($source->contents);
			$actual = explode("\n",$actual);
			$actual = array_filter($actual, array($this,'remove_empty_lines'));
			$actual = implode("\n",$actual);
			
			# The source contents should equal the expect output
			$this->assertEquals($expected,$actual);
		}
	}
	
	private function remove_empty_lines($value)
	{
		$value = trim($value);
		return ($value != '');
	}
}