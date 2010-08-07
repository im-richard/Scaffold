<?php

class ScaffoldTest extends PHPUnit_Framework_TestCase
{
	var $object;
	var $source;
	
	public function setUp() 
	{
		$system = realpath(__DIR__ . '/../../');
		$options = array
		(
			'production' => false,
			'max_age' => 3600,
			'load_paths' => array(),
			'output_compression' => false,
			'output_style' => 'none'
		);
		$container = new Scaffold_Container($system,$options);
		$this->object = $container->build();
	}
	
	/**
	 * simple compile
	 * @author Anthony Short
	 * @test
	 */
	public function simple_compile()
	{
		$source = new Scaffold_Source_String('foo');
		$result = $this->object->compile($source);
		$this->assertEquals('foo',$result->contents);
	} // simple_compile
}