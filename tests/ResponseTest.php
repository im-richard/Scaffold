<?php

require_once './init.php';

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
	
	// Make sure everything was constructed correctly
	public function testContentType()
	{
		$type = $this->object->header('Content-Type');
		$this->assertEquals($type,'text/css');
	}
	
	public function testCacheControl()
	{
		$control = $this->object->header('Cache-Control');
		$this->assertEquals($control,'max-age=3600, public');
	}
	
	public function testVary()
	{
		$vary = $this->object->header('Vary');
		$this->assertEquals($vary,'Accept-Encoding');
	}
	
	public function testContentEncoding()
	{
		$encoding = $this->object->header('Content-Encoding');
		$this->assertEquals($encoding,'gzip');
	}
	
	public function testRender()
	{
		$last_modified = strtotime($this->time);
		$this->object->render($this->content,$last_modified,false);
	}
	
	public function testContentLength()
	{
		//$str = strlen($this->content);
		//$length = $this->object->header('Content-Length');
		//$this->assertEquals($str,$length);
	}
	
	public function testLastModified()
	{
		$last_modified = strtotime($this->time);
		$lm = $this->object->header('Last-Modified');
		$this->assertEquals($lm,$last_modified);
	}
	
	public function testExpires()
	{
		$expires = strtotime($this->time + 3600);
		$e = $this->object->header('Expires');
		$this->assertEquals($e,$expires);
	}
}
