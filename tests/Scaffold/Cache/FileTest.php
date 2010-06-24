<?php

require_once __DIR__ .'/../../_init.php';

class Scaffold_Cache_FileTest extends PHPUnit_Framework_TestCase
{
	var $cache_dir;
	
	public function setUp() 
	{
		$this->cache_dir  = realpath(__DIR__ .'/../../_cache/') . DIRECTORY_SEPARATOR;
		$this->object = new Scaffold_Cache_File($this->cache_dir,0);
	}

	/**
	 * Set the contents
	 * @author Anthony Short
	 * @test
	 */
	public function Set_the_contents()
	{
		$this->assertTrue($this->object->set('foo','bar'));
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
		$this->assertEquals('bar',$this->object->get('foo'));
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
		$get = $this->object->get('file',true);
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
		$this->assertTrue($this->object->set('foo',$array));
	} // Set array
	
	/**
	 * Get array
	 * @author Anthony Short
	 * @test
	 */
	public function Get_array()
	{
		$array = $this->object->get('foo');
		$array = unserialize($array);
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
		$this->object->empty_dir('bar/foo');
		$this->assertFalse( is_file($this->cache_dir.'bar/foo/foo') );
	} // Empty directory
	
	/**
	 * Delete directory
	 * @author Anthony Short
	 * @test
	 */
	public function Delete_directory()
	{
		$this->object->delete_dir('bar');
		$this->assertFalse( is_dir($this->cache_dir.'bar/foo/') );
		
		$this->object->delete_dir('foobar');
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
		# Cache expires in an hour from when it was created
		$this->object->set_expires(3600);
		
		//
		
		# Cache file was just modified
		touch($this->object->find('foo'),time());
		
		# Original file was modified an hour ago
		$original = time() - 3600;

		# Check to see if the cache is expired by comparing it's modified time to the modified time of the original file
		$this->assertFalse($this->object->expired('foo',$original));
		
		// ---------------------------
		// This will occur when the original file is updated, the cache should update too
		// ---------------------------
	
		# Cache was modified an hour ago
		touch($this->object->find('foo'),time() - 3600);
		
		# Original file was just modified
		$original = time();

		# The cache should be remade to match the new file
		$this->assertTrue($this->object->expired('foo',$original));
		
		//
		
		# Cache was modified an 30 mins ago
		touch($this->object->find('foo'),time() - 1800);

		# Original file was modified an hour ago
		$original = time() - 3600;

		# The cache should still be fresh
		$this->assertFalse($this->object->expired('foo',$original));
	}
	
}