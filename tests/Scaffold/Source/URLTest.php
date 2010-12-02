<?php

class Scaffold_Source_UrlTest extends PHPUnit_Framework_TestCase
{
	var $url =  'http://files.anthonyshort.com.au/stylesheets/screen.css';

	public function setUp() 
	{
		$this->object = new Scaffold_Source_Url($this->url);
	}

	/**
	 * Get the url
	 * @author Anthony Short
	 * @test
	 */
	public function Get_the_url()
	{
		$expected = $this->url;
		$actual = $this->object->url();
		$this->assertEquals( $expected, $actual );
	}
	
	/**
	 * Get the id
	 * @author Anthony Short
	 * @test
	 */
	public function Get_the_id()
	{
		$expected = md5($this->url);
		$actual = $this->object->id();
		$this->assertEquals( $expected, $actual );
	} // Get the id
	
	/**
	 * Get a url that fails
	 * @expectedException Exception
	 * @author Anthony Short
	 * @test
	 */
	public function Get_a_url_that_fails()
	{
		
		$source = new Scaffold_Source_Url('http://files.anthonyshort.com.au/stylesheets/foobar.css');
		$this->assertEquals( $expected, $actual );
	}
	
}