<?php

require_once __DIR__ . '/../_init.php';

class Scaffold_ResponseTest extends PHPUnit_Framework_TestCase
{
	private $object;

	public function setUp()
	{
		$encoder = $this->getMock('Scaffold_Response_Compressor');
		$cache = $this->getMock('Scaffold_Response_Cache');
		
		// Make the encoding method return false
		$encoder->expects($this->any())
		        ->method('get_encoding_method')
		        ->will($this->returnValue('gzencode'));
		        
		$encoder->expects($this->any())
		        ->method('get_encoding_type')
		        ->will($this->returnValue('gzip'));
		        
		$encoder->expects($this->any())
		        ->method('compress')
		        ->will($this->returnValue('foo'));
		        
		$this->time = time();
		$this->content = 'foo';
		
		$this->object = new Scaffold_Response($encoder,$cache);		
	}

	/**
	 * Test get content type header
	 * @author Anthony Short
	 * @test
	 */
	public function Test_get_content_type_header()
	{
		$this->assertEquals($this->object->header('Content-Type'),'text/css');
	}
	
	/**
	 * Get cache control header
	 * @author Anthony Short
	 * @test
	 */
	public function Get_cache_control_header()
	{
		$this->assertEquals($this->object->header('Cache-Control'),'max-age=3600, public');
	}
	
	/**
	 * Get vary header
	 * @author Anthony Short
	 * @test
	 */
	public function Get_vary_header()
	{
		$this->assertEquals($this->object->header('Vary'),'Accept-Encoding');
	} // Get vary header
	
	/**
	 * Get content encoding
	 * @author Anthony Short
	 * @test
	 */
	public function Get_content_encoding()
	{
		$this->assertEquals($this->object->header('Content-Encoding'),'gzip');
	} // Get content encoding
}
