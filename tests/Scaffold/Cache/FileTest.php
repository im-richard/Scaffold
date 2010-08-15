<?php

class Scaffold_Cache_FileTest extends PHPUnit_Framework_TestCase
{
	var $cache_dir;
	
	public function setUp() 
	{
		$this->cache_dir  = realpath(__DIR__ .'/../../_cache/') . DIRECTORY_SEPARATOR;
		$this->object = new Scaffold_Cache_File($this->cache_dir,3600);
	}

	/**
	 * Set the contents
	 * @author Anthony Short
	 * @test
	 */
	public function Set_and_get_the_contents()
	{
		$this->object->set('foo','bar');
		$this->assertTrue(file_exists($this->cache_dir.'/foo'));
	} // Set the contents
	
	/**
	 * Find foo
	 * @author Anthony Short
	 * @test
	 */
	public function Find_foo()
	{
		$this->assertEquals( $this->object->find('foo'), $this->cache_dir.'foo');
	} // Find foo
	
	/**
	 * Checks if foo exists
	 * @author Anthony Short
	 * @test
	 */
	public function Checks_if_foo_exists()
	{
		$this->assertTrue( $this->object->exists('foo') );
	} // Checks if foo exists

	/**
	 * Get the contents
	 * @author Anthony Short
	 * @test
	 */
	public function Get_the_contents()
	{
		$data = $this->object->get('foo');
		$this->assertEquals('bar',$data->contents);
	}
	
	/**
	 * Delete foo
	 * @author Anthony Short
	 * @test
	 */
	public function Delete_foo()
	{
		$this->object->delete('foo');
		$this->assertFalse($this->object->exists('foo'));
	} // Delete foo
	
	/**
	 * Get default value
	 * @author Anthony Short
	 * @test
	 */
	public function Get_default_value()
	{
		$get = $this->object->get('file',null,true);
		$this->assertTrue( $get );
	} // Get default value
	
	/**
	 * Set array
	 * @author Anthony Short
	 * @test
	 */
	public function Set_array()
	{
		$array = array('foo'=>'bar');
		$this->assertNotNull($this->object->set('foo',$array));
	} // Set array
	
	/**
	 * Get array
	 * @author Anthony Short
	 * @test
	 */
	public function Get_array()
	{
		$data = $this->object->get('foo');
		$array = unserialize($data->contents);
		$this->assertEquals( $array['foo'], 'bar' );
		$this->object->delete('foo');
	} // Get array
	
	/**
	 * Delete file that doesnt exist
	 * @author Anthony Short
	 * @test
	 */
	public function Delete_file_that_doesnt_exist()
	{
		$this->assertFalse($this->object->delete('nonexistant'));
	} // Delete file that doesnt exist
	
	/**
	 * Create directory
	 * @author Anthony Short
	 * @test
	 */
	public function Create_directory()
	{
		$this->object->create('foobar');
		$this->assertTrue( is_dir($this->cache_dir.'foobar') );
		
		$this->object->create('bar/foo');
		$this->assertTrue( is_dir($this->cache_dir.'bar/foo') );
	} // Create directory
	
	/**
	 * Find directory
	 * @author Anthony Short
	 * @test
	 */
	public function Find_directory()
	{
		$this->assertTrue( is_dir($this->object->find('bar/foo')) );
	} // Find directory
	
	
	/**
	 * Empty directory
	 * @author Anthony Short
	 * @test
	 */
	public function Empty_directory()
	{
		$this->object->set('bar/foo/foo','I gonna get deleted');
		$this->object->delete('bar/foo');
		$this->assertFalse( is_file($this->cache_dir.'bar/foo/foo') );
	} // Empty directory
	
	/**
	 * Delete directory
	 * @author Anthony Short
	 * @test
	 */
	public function Delete_directory()
	{
		$this->object->delete('bar');
		$this->assertFalse( is_dir($this->cache_dir.'bar/foo/') );
		
		$this->object->delete('foobar');
		$this->assertFalse( is_dir($this->cache_dir.'bar/foobar/') );
	} // Delete directory
	
	/**
	 * Empty out the entire cache
	 * @author Anthony Short
	 * @test
	 */
	public function Empty_out_the_entire_cache()
	{
		$this->object->set('foo','bar');
		$this->object->delete_all();
		
		$contents = glob($this->cache_dir.'/*',GLOB_MARK);
		
		$this->assertEquals( count($contents), 0 );		
	} // Empty out the entire cache
	
	/**
	 * Check expiration with a max age
	 * @author Anthony Short
	 * @test
	 */
	public function Check_expiration_with_a_max_age()
	{
		$this->object->max_age = $max_age = 3600;
		$file = $this->object->find(__FUNCTION__);
		$now = time();

		//
		
		$this->object->set(__FUNCTION__,'bar');
		$data = $this->object->get(__FUNCTION__);
		$this->assertEquals($data->expires,$now + $max_age);
		
		//
		
		$this->object->set(__FUNCTION__,'bar');
		$data = $this->object->get(__FUNCTION__,$now + 3600); // Cache has existed for an hour
		$this->assertFalse($data);
		
		//

		$this->object->set(__FUNCTION__,'bar');
		$data = $this->object->get(__FUNCTION__,$now + 1800); // Cache has existed for 30 minutes
		$this->assertNotNull($data);
		
		$this->object->delete_all();
	}	
}