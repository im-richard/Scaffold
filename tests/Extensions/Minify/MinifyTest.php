<?php

class Scaffold_Extension_MinifyTest extends PHPUnit_Framework_TestCase
{
	private $source;
	private $scaffold;
	private $object;
	
	protected function setUp()
	{
		$system 			= realpath(__DIR__.'/../../../');
		$container 			= new Scaffold_Container($system);
		$this->scaffold 	= $container->build();
		$this->object 		= new Scaffold_Extension_Minify();
	}
	
	/**
	 * @test
	 */
	public function test_post_format()
	{
		$dir = __DIR__ . '/_files/original/';
		
		foreach(glob($dir.'*.css') as $original)
		{
			# The expected output
			$expected = file_get_contents(str_replace('/_files/original/','/_files/expected/',$original));
			
			# Create and parse the source
			$source = new Scaffold_Source_File($original);
			$this->object->post_format($source,$this->scaffold);
			
			# The source contents should equal the expect output
			$this->assertEquals($expected,$source->contents);
		}
	}
	
	private function remove_empty_lines($value)
	{
		$value = trim($value);
		return ($value != '');
	}
}