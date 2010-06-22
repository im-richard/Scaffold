<?php

// Loads PHPUnit and autoloads classes
require_once __DIR__ . '/../_init.php';

class ScaffoldTest extends PHPUnit_Framework_TestCase
{
	var $object;
	var $source;
	
	public function setUp() 
	{
		$cache = $this->mock('Scaffold_Cache_File',array('get','set','modified'));
		
		// Set method
		$cache->expects($this->any())
		      ->method('set')
		      ->will($this->returnValue(true));
		
		// Get method
		$cache->expects($this->any())
		      ->method('get')
		      ->will($this->returnValue('foo'));
		
		// Modified method
		$cache->expects($this->any())
		      ->method('modified')
		      ->will($this->returnValue(0));
		
		$response 	= $this->mock('Scaffold_Response',array('render'));
		
		// Render method
		$response->expects($this->any())
		         ->method('render')
		         ->will($this->returnValue(true));
		
		$loader 	= $this->mock('Scaffold_Loader',array());
		
		$this->object = new Scaffold($cache,$response,$loader);
		
		$this->source = $this->mock('Scaffold_Source_File',array('id','last_modified','get'));
	}

	public function tearDown() 
	{
		
	}
	
	public function mock($class,$methods=array())
	{
	    $params = array();
	    $construct = false;
	    $name = $class . '_Mock';
	    return $this->getMock($class,$methods,$params,$name,$construct);
	}
	
	/**
	 * simple compile
	 * @author Anthony Short
	 * @test
	 */
	public function simple_compile()
	{
		$result = $this->object->compile($this->source);
		$this->assertEquals('foo',$result['string']);
		$this->assertEquals(0,$result['last_modified']);
	} // simple_compile
	
	/**
	 * simple render
	 * @author Anthony Short
	 * @test
	 */
	public function simple_render()
	{
		$this->assertEquals( $this->object->render('foo',0), true );
	} // simple render
	
}