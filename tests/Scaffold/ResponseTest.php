<?php

class Scaffold_ResponseTest extends PHPUnit_Framework_TestCase
{
	private $object;

	public function setUp()
	{
		$encoder = $this->getMock('Scaffold_Response_Encoder');
		$cache = $this->getMock('Scaffold_Response_Cache');
		
		// Make the encoding method return false
		$encoder->expects($this->any())
		        ->method('get_encoding_method')
		        ->will($this->returnValue('gzencode'));
		        
		$encoder->expects($this->any())
		        ->method('get_encoding_type')
		        ->will($this->returnValue('gzip'));
		        
		$encoder->expects($this->any())
		        ->method('encode')
		        ->will($this->returnValue('foo'));
		        
		$cache->expects($this->any())
		        ->method('valid')
		        ->will($this->returnValue(false));
		        
		$this->time = time();
		$this->content = 'foo';
		
		$this->object = new Scaffold_Response($encoder,$cache);		
	}
	
	/**
	 * @test
	 */
	public function Get_encoding()
	{
		$actual = $this->object->encoding;
		$expected = 'gzip';
		$this->assertEquals($expected,$actual);
	}

	/**
	 * @test
	 */
	public function Test_set_content_without_encoding()
	{
		$this->object->encoding = false;
		$this->object->set('foo',0);
		
		$actual = $this->object->output;
		$expected = 'foo';
		$this->assertEquals($expected,$actual);
		
		$actual = $this->object->headers['Content-Type'];
		$expected = 'text/plain';
		$this->assertEquals($expected,$actual);
	}
	
	/**
	 * @test
	 */
	public function Test_set_content_with_encoding()
	{
		$this->object->encoding = 'gzip';
		$this->object->set('foo',0);
		
		$actual = $this->object->output;
		$expected = 'foo';
		$this->assertEquals($expected,$actual);
		
		$actual = $this->object->headers['Content-Type'];
		$expected = 'text/plain';
		$this->assertEquals($expected,$actual);
	}
}
