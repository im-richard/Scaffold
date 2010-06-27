<?php require_once __DIR__ . '/../../_init.php';

class Scaffold_Source_UrlTest extends PHPUnit_Framework_TestCase
{
	var $url =  'http://maxvoltar.com/assets/css/master.css';

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
}