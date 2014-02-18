<?php
/**
 * @package    SugiPHP
 * @subpackage Pagination
 * @category   tests
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

use SugiPHP\Pagination\Pagination;

class PaginationTest extends PHPUnit_Framework_TestCase
{
	public function testDefaultPageIsOne()
	{
		$pagination = new Pagination();
		$this->assertSame(1, $pagination->getPage());
	}

	public function testSetPage()
	{
		$pagination = new Pagination();
		// when setting a page Pagination should return self
		$this->assertInstanceOf("SugiPHP\Pagination\Pagination", $pagination->setPage(33));
		$this->assertSame(33, $pagination->getPage());
		// must be 1 or more
		$this->setExpectedException("SugiPHP\Pagination\Exception");
		$pagination->setPage(0);
		$this->setExpectedException("SugiPHP\Pagination\Exception");
		$pagination->setPage(-5);
	}

	public function testItemsPerPage()
	{
		$pagination = new Pagination();
		$this->assertSame(20, $pagination->getItemsPerPage());
		// when setting an itemsPerPage Pagination should return self
		$this->assertInstanceOf("SugiPHP\Pagination\Pagination", $pagination->setItemsPerPage(10));
		$this->assertSame(10, $pagination->getItemsPerPage());
		// only positive integer
		$this->setExpectedException("SugiPHP\Pagination\Exception");
		$pagination->setItemsPerPage(0);
		$this->setExpectedException("SugiPHP\Pagination\Exception");
		$pagination->setItemsPerPage(-5);
	}

	public function testSetTotalNumberOfItems()
	{
		$pagination = new Pagination();
		// default items are 0
		$this->assertSame(0, $pagination->getTotalItems());
		// when setting total number of items Pagination should return self
		$this->assertInstanceOf("SugiPHP\Pagination\Pagination", $pagination->setTotalItems(1000));
		$this->assertSame(1000, $pagination->getTotalItems());
		$pagination->setTotalItems(0);
		$this->assertSame(0, $pagination->getTotalItems());
		// only positive numbers
		$this->setExpectedException("SugiPHP\Pagination\Exception");
		$pagination->setTotalItems(-100);
	}

	public function testProximity()
	{
		$pagination = new Pagination();
		// default proximity is 4
		$this->assertSame(4, $pagination->getProximity());
		$this->assertInstanceOf("SugiPHP\Pagination\Pagination", $pagination->setProximity(3));
		$this->assertSame(3, $pagination->getProximity());
		$pagination->setProximity(0);
		$this->assertSame(0, $pagination->getProximity());
		// only not negative values
		$this->setExpectedException("SugiPHP\Pagination\Exception");
		$pagination->setProximity(-2);
	}

	public function testSetUri()
	{
		$pagination = new Pagination();
		// from CLI there is no $_SERVER globals, so it will return "/"
		$this->assertSame("/", $pagination->getUri());
		// we'll set a $_SERVER global
		$_SERVER["REQUEST_URI"] = "/index.php?q=search";
		$this->assertSame("/index.php?q=search", $pagination->getUri());
		// we'll set a different URI with setUri() and Pagination should return self
		$this->assertInstanceOf("SugiPHP\Pagination\Pagination", $pagination->setUri("/foo/bar?one=1"));
		$this->assertSame("/foo/bar?one=1", $pagination->getUri());
	}

	public function testSetUriPattern()
	{
		$pagination = new Pagination();
		// default pattern is via get parameters
		$this->assertSame("page={page}", $pagination->getPattern());
		// when setting URI pattern Pagination should return self
		$this->assertInstanceOf("SugiPHP\Pagination\Pagination", $pagination->setPattern("/page/{page}"));
		$this->assertSame("/page/{page}", $pagination->getPattern());
	}

	public function testConstructor()
	{
		$config = array(
			"items" => 100,
			"page"  => 5,
			"itemsPerPage" => 10,
			"uri" => "/list/page/6",
			"pattern" => "/page/{page}"
		);
		$pagination = new Pagination($config);
		$this->assertSame(100, $pagination->getTotalItems());
		$this->assertSame(5, $pagination->getPage());
		$this->assertSame(10, $pagination->getItemsPerPage());
		$this->assertSame("/list/page/6", $pagination->getUri());
		$this->assertSame("/page/{page}", $pagination->getPattern());
	}

	public function testGetTotalPages()
	{
		$pagination = new Pagination();
		// by default total items are 0, so there is 0 pages
		$this->assertSame(0, $pagination->getTotalPages());
		$pagination->setTotalItems(5);
		$this->assertSame(1, $pagination->getTotalPages());
		$pagination->setTotalItems(20);
		$this->assertSame(1, $pagination->getTotalPages());
		$pagination->setTotalItems(21);
		$this->assertSame(2, $pagination->getTotalPages());
		$pagination->setTotalItems(40);
		$this->assertSame(2, $pagination->getTotalPages());
		$pagination->setTotalItems(866684);
		$this->assertSame(43335, $pagination->getTotalPages());
	}

	public function testGetOffset()
	{
		$pagination = new Pagination();
		$this->assertSame(0, $pagination->getOffset());
		$pagination->setTotalItems(30)->setItemsPerPage(10);
		$this->assertSame(0, $pagination->getOffset());
		$pagination->setPage(1);
		$this->assertSame(0, $pagination->getOffset());
		$pagination->setPage(2);
		$this->assertSame(10, $pagination->getOffset());
		$pagination->setPage(3);
		$this->assertSame(20, $pagination->getOffset());
	}

	public function testGetTotalPagesBySettingItemsPerPage()
	{
		$pagination = new Pagination();
		$pagination->setItemsPerPage(3);
		$this->assertSame(0, $pagination->getTotalPages());
		$pagination->setTotalItems(31);
		$this->assertSame(11, $pagination->getTotalPages());
		$pagination->setItemsPerPage(31);
		$this->assertSame(1, $pagination->getTotalPages());
		$pagination->setItemsPerPage(30);
		$this->assertSame(2, $pagination->getTotalPages());
	}

	public function testRetreivingPageFromUri()
	{
		$pagination = new Pagination();
		// default page is 1
		$this->assertSame(1, $pagination->getPage());
		// default pattern is "page={page}"
		$pagination->setUri("?page=4");
		$this->assertSame(4, $pagination->getPage());
		$pagination->setUri("?foo=bar&page=5");
		$this->assertSame(5, $pagination->getPage());
		$pagination->setUri("/posts/list?page=6");
		$this->assertSame(6, $pagination->getPage());
		$pagination->setUri("/posts/list?foo=bar&page=7");
		$this->assertSame(7, $pagination->getPage());
		// case insensitive
		$pagination->setUri("/posts/list?foo=bar&PAGE=8");
		$this->assertSame(8, $pagination->getPage());
		$pagination->setUri("/posts/list?foo=bar&page=9&bar=foo");
		$this->assertSame(9, $pagination->getPage());
		// "page=", but not "itemsperpage=", which should give us default page 1
		$pagination->setUri("/post/list?foo=bar&itemsperpage=10");
		$this->assertSame(1, $pagination->getPage());
		// wrong pages
		$pagination->setUri("/posts/list?foo=bar&page=abc");
		$this->assertSame(1, $pagination->getPage());
		$pagination->setUri("/posts/list?foo=bar&page=foo9");
		$this->assertSame(1, $pagination->getPage());
		$pagination->setUri("/posts/list?foo=bar&page=09");
		$this->assertSame(1, $pagination->getPage());
		$pagination->setUri("/posts/list?foo=bar&page=9foo");
		$this->assertSame(1, $pagination->getPage());
	}

	public function testRetreivingPageFromUriWithCustomGetParam()
	{
		$pagination = new Pagination();
		// default pattern is "page={page}", we will make it "p={page}"
		$pagination->setPattern("p={page}");
		// page is not working any more
		$pagination->setUri("?page=4");
		$this->assertSame(1, $pagination->getPage());
		// "p" is!
		$pagination->setUri("?p=4");
		$this->assertSame(4, $pagination->getPage());
		$pagination->setUri("?page=3&p=5");
		$this->assertSame(5, $pagination->getPage());
		$pagination->setUri("/posts/list?p=6&page=3");
		$this->assertSame(6, $pagination->getPage());
	}

	public function testRetreivingPageFriendlyURIs()
	{
		$pagination = new Pagination();
		$pagination->setPattern("page/{page}");
		// default page is 1
		$this->assertSame(1, $pagination->getPage());
		// GET doesn't work
		$pagination->setUri("?page=4");
		$this->assertSame(1, $pagination->getPage());
		// URI does!
		$pagination->setUri("/page/4");
		$this->assertSame(4, $pagination->getPage());
		$pagination->setUri("/posts/list/page/6");
		$this->assertSame(6, $pagination->getPage());
		$pagination->setUri("/posts/list/page/7");
		$this->assertSame(7, $pagination->getPage());
		// case insensitive
		$pagination->setUri("/posts/list/PAGE/8?page=9&pages=10");
		$this->assertSame(8, $pagination->getPage());
		$pagination->setUri("/posts/list/page/9/bar/foo");
		$this->assertSame(9, $pagination->getPage());
		// "page=", but not "itemsperpage=", which should give us default page 1
		$pagination->setUri("/post/list/itemsperpage/10");
		$this->assertSame(1, $pagination->getPage());
		// wrong pages
		$pagination->setUri("/posts/list/page/abc?foo=bar");
		$this->assertSame(1, $pagination->getPage());
		$pagination->setUri("/posts/list/page/foo9?foo=bar");
		$this->assertSame(1, $pagination->getPage());
		$pagination->setUri("/posts/list/page/09/bar/foo/?foo=bar");
		$this->assertSame(1, $pagination->getPage());
		$pagination->setUri("/posts/list/page/9foo");
		$this->assertSame(1, $pagination->getPage());
	}

	public function testEmptySet()
	{
		$pagination = new Pagination();
		$pages = $pagination->toArray();
		// always should have "prev" and "next"
		$this->assertArrayHasKey("prev", $pages);
		$this->assertArrayHasKey("next", $pages);
		// when there are 0 items - only 2 links (prev and next)
		$this->assertSame(2, count($pages));
		// both are inactive
		$this->assertTrue($pages["prev"]["isDisabled"]);
		$this->assertTrue($pages["next"]["isDisabled"]);
		// links are "#"
		$this->assertEquals("#", $pages["prev"]["uri"]);
		$this->assertEquals("#", $pages["next"]["uri"]);
		// page is set to false
		$this->assertFalse($pages["prev"]["page"]);
		$this->assertFalse($pages["next"]["page"]);
		// they are always not active (current flag is set to FALSE)
		$this->assertFalse($pages["prev"]["isCurrent"]);
		$this->assertFalse($pages["next"]["isCurrent"]);
	}

	public function testRealExample()
	{
		$pagination = new Pagination();
		$pagination->setTotalItems(105)->setItemsPerPage(10)->setUri("/posts?page=5");
		$this->assertSame(5, $pagination->getPage());
		$this->assertSame(11, $pagination->getTotalPages());

		$pages = $pagination->toArray();
		// always should have "prev" and "next"
		$this->assertArrayHasKey("prev", $pages);
		$this->assertArrayHasKey("next", $pages);
		// previous | first | 4 before | selected | 4 after | last | next
		$this->assertSame(13, count($pages));

		// Test with proximity 3
		$pagination->setProximity(3);
		$pages = $pagination->toArray();
		$this->assertSame(11, count($pages));

		// Test with proximity 2
		$pagination->setProximity(2);
		$pages = $pagination->toArray();
		$this->assertSame(9, count($pages));

		// Test with proximity 1
		$pagination->setProximity(1);
		$pages = $pagination->toArray();
		$this->assertSame(7, count($pages));

		// Test with proximity 0
		// previous | first | current | last | next
		$pagination->setProximity(0);
		$pages = $pagination->toArray();
		$this->assertSame(5, count($pages));
	}
}
