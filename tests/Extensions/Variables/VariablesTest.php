<?php

class Scaffold_Extension_VariablesTest extends PHPUnit_Framework_TestCase
{
	private $source;
	private $scaffold;
	private $object;
	
	protected function setUp()
	{
		$options = array(
			'extensions' => array(
				'Variables'
			)
		);

		$system 			= realpath(__DIR__.'/../../../');	
		$container 			= new Scaffold_Container($system,$options);
		$this->scaffold 	= $container->build();
		$this->object 		= $this->scaffold->extensions['Variables'];
	}

	public function test_process()
	{
		$dir = __DIR__ . '/_files/original/';
		
		foreach(glob($dir.'*.css') as $original)
		{
			# The expected output
			$expected = file_get_contents(str_replace('/_files/original/','/_files/expected/',$original));
			
			# Create and parse the source
			$source = new Scaffold_Source_File($original);
			$this->object->process($source,$this->scaffold);
			
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