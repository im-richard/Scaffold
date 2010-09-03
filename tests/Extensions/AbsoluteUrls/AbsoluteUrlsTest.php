<?php

class Scaffold_Extension_AbsoluteUrlsTest extends PHPUnit_Framework_TestCase
{
	private $source;
	private $scaffold;
	private $object;
	
	protected function setUp()
	{
		$system 			= realpath(__DIR__.'/../../../');
		$container 			= new Scaffold_Container($system);
		$this->scaffold 	= $container->build();
		$this->object 		= new Scaffold_Extension_AbsoluteUrls();
		
		$_SERVER['DOCUMENT_ROOT'] = $system;
	}
	
	/**
	 * @test
	 */
	public function test_up_directory()
	{
		$dir = '/this/is/my/path';
		$expected = '/this/is';
		$actual = $this->object->up_directory($dir,2);
		$this->assertEquals($expected,$actual);
		
		$dir = '/this/is/my/path/';
		$expected = '/this/is';
		$actual = $this->object->up_directory($dir,2);
		$this->assertEquals($expected,$actual);
		
		$dir = 'this/is/my/path/';
		$expected = '/this/is';
		$actual = $this->object->up_directory($dir,2);
		$this->assertEquals($expected,$actual);
		
		$dir = '/this/is/my/path/';
		$expected = '/';
		$actual = $this->object->up_directory($dir,5);
		$this->assertEquals($expected,$actual);
		
		$dir = 'this/is/my/path/';
		$expected = '/';
		$actual = $this->object->up_directory($dir,5);
		$this->assertEquals($expected,$actual);
		
		$dir = '/this/is/my/path';
		$expected = '/this/is/my/path';
		$actual = $this->object->up_directory($dir,0);
		$this->assertEquals($expected,$actual);
	}
	
	public function test_remove_up_directories()
	{
		$dir = '../../my/path';
		$expected = '/my/path';
		$actual = $this->object->remove_up_directories($dir);
		$this->assertEquals($expected,$actual);
		
		$dir = '..\..\my\path';
		$expected = '/my\path';
		$actual = $this->object->remove_up_directories($dir);
		$this->assertEquals($expected,$actual);
		
		$dir = '/my/path';
		$expected = '/my/path';
		$actual = $this->object->remove_up_directories($dir);
		$this->assertEquals($expected,$actual);
	}
	
	public function test_resolve_path()
	{
		$base = '/this/is/my/path';
		$dir = '../foo';
		$expected = '/this/is/my/foo';
		$actual = $this->object->resolve_path($base,$dir);
		$this->assertEquals($expected,$actual);
		
		$base = '/this/is/my/path/';
		$dir = '../foo';
		$expected = '/this/is/my/foo';
		$actual = $this->object->resolve_path($base,$dir);
		$this->assertEquals($expected,$actual);
		
		$base = '/this/is/my/path';
		$dir = '../../foo';
		$expected = '/this/is/foo';
		$actual = $this->object->resolve_path($base,$dir);
		$this->assertEquals($expected,$actual);
		
		$base = '/this/is/my/path';
		$dir = '/foo';
		$expected = '/foo';
		$actual = $this->object->resolve_path($base,$dir);
		$this->assertEquals($expected,$actual);
		
		$base = '/this/is/my/path';
		$dir = 'foo';
		$expected = '/this/is/my/path/foo';
		$actual = $this->object->resolve_path($base,$dir);
		$this->assertEquals($expected,$actual);
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
}