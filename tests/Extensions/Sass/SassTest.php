<?php

class Scaffold_Extension_SassTest extends PHPUnit_Framework_TestCase
{
	private $source;
	private $scaffold;
	private $object;
	
	protected function setUp()
	{
		$system 			= realpath(__DIR__.'/../../../');
		$container 			= new Scaffold_Container($system);
		$this->scaffold 	= $container->build();
		$this->object 		= new Scaffold_Extension_Sass();
	}
	
	/**
	 * @test
	 */
	public function test_scss()
	{
		$dir = __DIR__ . '/_files/original/';
		
		foreach(glob($dir.'*.css') as $original)
		{
			# The expected output
			$expected = file_get_contents(str_replace('/_files/original/','/_files/expected/',$original));
			
			# Create and parse the source
			$source = new Scaffold_Source_File($original);
			$this->object->process($source,$this->scaffold);

			# The source contents should equal the expect output
			$this->assertEquals($expected,$source->contents);
		}
	}
	
	/**
	 * @test
	 */
	public function test_sass()
	{
		$dir = __DIR__ . '/_files/original/';
		
		foreach(glob($dir.'*.css') as $original)
		{
			# The expected output
			$expected = file_get_contents(str_replace('/_files/original/','/_files/expected/',$original));
			
			# Create and parse the source
			$source = new Scaffold_Source_File($original);
			$this->object->process($source,$this->scaffold);

			# The source contents should equal the expect output
			$this->assertEquals($expected,$source->contents);
		}
	}
}