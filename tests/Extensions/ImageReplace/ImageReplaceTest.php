<?php

class Scaffold_Extension_ImageReplaceTest extends PHPUnit_Framework_TestCase
{
	private $source;
	private $scaffold;
	private $object;
	
	protected function setUp()
	{
		$system 			= realpath(__DIR__.'/../../../');
		$container 			= new Scaffold_Container($system);
		$this->scaffold 	= $container->build();
		$this->object 		= new Scaffold_Extension_ImageReplace();
	}
	
	/**
	 * @test
	 */
	public function test_process()
	{
		$file = __DIR__ . '/_files/image1.jpeg';
		$source = new Scaffold_Source_File(__DIR__ . '/_files/source.css');
		$this->object->source = $source;
		$expected = 'background:url(image1.jpeg) no-repeat 0 0;height:0;padding-top:706px;width:425px;display:block;text-indent:-9999px;overflow:hidden;';
		
		// Assertion
		$properties = $this->object->image_replace('url("image1.jpeg")');
		$this->assertEquals($expected,$properties);
		
		// Assertion
		$properties = $this->object->image_replace('url(image1.jpeg)');
		$this->assertEquals($expected,$properties);
		
		// Assertion
		$properties = $this->object->image_replace("url('image1.jpeg')");
		$this->assertEquals($expected,$properties);
		
		// Assertion
		$properties = $this->object->image_replace("'image1.jpeg'");
		$this->assertEquals($expected,$properties);
		
		// Assertion
		$properties = $this->object->image_replace('"image1.jpeg"');
		$this->assertEquals($expected,$properties);
	}
}