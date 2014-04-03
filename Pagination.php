<?php
/**
 * @package    SugiPHP
 * @subpackage Pagination
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Pagination;

class Pagination
{
	protected $totalItems = 0;
	protected $itemsPerPage = 20;
	protected $proximity = 4;
	protected $page;
	protected $uri;
	protected $uriPattern = "page={page}"; // default is with a $_GET[page] parameter. Set it to "page/{page}" for friendly URLs

	/**
	 * Pagination constructor.
	 *
	 * @param array $params Pagination's options
	 */
	public function __construct($params = array())
	{
		if ($params && is_array($params)) {
			foreach ($params as $key => $value) {
				if ($key == "items" || $key == "totalItems") {
					$this->setItems($value);
				} elseif ($key == "page") {
					$this->setPage($value);
				} elseif ($key == "itemsPerPage" || $key == "ipp") {
					$this->setItemsPerPage($value);
				} elseif ($key == "uri") {
					$this->setUri($value);
				} elseif ($key == "pattern") {
					$this->setPattern($value);
				} elseif ($key == "proximity") {
					$this->setProximity($value);
				}
			}
		}
	}

	/**
	 * Sets total number of items.
	 *
	 * @param integer $totalItems
	 */
	public function setTotalItems($totalItems)
	{
		return $this->setItems($totalItems);
	}

	/**
	 * Returns total number of items.
	 *
	 * @return int
	 */
	public function getTotalItems()
	{
		return $this->getItems();
	}

	/**
	 * setTotalItems() alias
	 */
	public function setItems($totalItems)
	{
		if ($totalItems < 0) {
			throw new Exception("totalItems must be not negative value");
		}
		$this->totalItems = (int) $totalItems;

		return $this;
	}

	/**
	 * getTotalItems() alias
	 */
	public function getItems()
	{
		return $this->totalItems;
	}

	/**
	 * Sets current page number.
	 *
	 * @param integer $page
	 */
	public function setPage($page)
	{
		if ($page < 1) {
			throw new Exception("page must be positive integer value");
		}
		$this->page = (int) $page;

		return $this;
	}

	/**
	 * Returns current page number.
	 * If the page was not set it'll try to guess it from the URI. If the pattern was not found it will return 1.
	 *
	 * @return integer
	 */
	public function getPage()
	{
		// the page was previously set
		if ($this->page) {
			return $this->page;
		}

		// get the current page from the URI
		if (preg_match($this->buildPattern(), $this->getUri(), $matches)) {
			return (int) $matches[1];
		}

		// default page is 1
		return 1;
	}

	/**
	 * Sets items per page.
	 *
	 * @param integer $itemsPerPage
	 */
	public function setItemsPerPage($itemsPerPage)
	{
		if ($itemsPerPage < 1) {
			throw new Exception("itemsPerPage must be positive integer value");
		}
		$this->itemsPerPage = (int) $itemsPerPage;

		return $this;
	}

	/**
	 * Returns number of items per page. Default is 20.
	 *
	 * @return integer.
	 */
	public function getItemsPerPage()
	{
		return $this->itemsPerPage;
	}

	/**
	 * setItemsPerPage() alias
	 */
	public function setLimit($itemsPerPage)
	{
		return $this->setItemsPerPage($itemsPerPage);
	}

	/**
	 * getItemsPerPage() alias
	 */
	public function getLimit()
	{
		return $this->getItemsPerPage();
	}

	/**
	 * Sets proximity - how many page links should be in front and after current page.
	 * Default is 4.
	 * Total number of links (items toArray() method returns) can be calculated by
	 * proximity * 2 + 1 (current page) + 1 (previous) + 1 (next). So if proximity
	 * is set to 4 total number of links will be 13; if proximity is 3 total pages = 9
	 *
	 * @param [type] $proximity [description]
	 */
	public function setProximity($proximity)
	{
		if ($proximity < 0) {
			throw new Exception("proximity must be non negative value");
		}
		$this->proximity = (int) $proximity;

		return $this;
	}

	/**
	 * Returns proximity - how many page links should be in front and after current page.
	 * Default is 4
	 *
	 * @return integer
	 */
	public function getProximity()
	{
		return $this->proximity;
	}


	/**
	 * Sets current URI.
	 *
	 * @param string $uri
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;

		return $this;
	}

	/**
	 * Returns current URI. If the URI was not set with setUri() it will return URI from the server.
	 *
	 * @return string
	 */
	public function getUri()
	{
		if ($this->uri) {
			return $this->uri;
		}
		if (isset($_SERVER["REQUEST_URI"])) {
			return $_SERVER["REQUEST_URI"];
		}
		return "/";
	}

	/**
	 * Sets the URI pattern for creating links for pages.
	 * Default pattern is "page={page}"  (URLs like /posts/show?page=5)
	 * Can be set for example to "p={page}" or anything else for $_GET parameter
	 * Can be set also to "page/{page}" for friendly URLs which will result with URLs like: /posts/show/page/5
	 *
	 * @param string $pattern
	 */
	public function setPattern($pattern)
	{
		$this->uriPattern = $pattern;

		return $this;
	}

	/**
	 * Returns URI pattern.
	 *
	 * @return string
	 */
	public function getPattern()
	{
		return $this->uriPattern;
	}

	/**
	 * Calculate and return total number of pages based on total items and items per page settings.
	 *
	 * @return integer
	 */
	public function getTotalPages()
	{
		return (int) ceil($this->totalItems / $this->itemsPerPage);
	}

	/**
	 * Returns first item's index in a page we are on. Used in SQLs (e.g. LIMIT 20 OFFSET 60)
	 *
	 * @return integer
	 */
	public function getOffset()
	{
		return (int) max(0, ($this->getPage() - 1) * $this->getItemsPerPage());
	}

	/**
	 * Returns pagination in form of an array.
	 * Each pagination item (link) will contain certain info that can be used in a template to build HTML.
	 *
	 * @return array
	 *     [
	 *         [
	 *             "page"        => page number,
	 *             "uri"         => anchor's URI,
	 *             "isCurrent"   => if the page is a current one,
	 *             "isDisabled"  => if the button should be marked as disabled,
	 *         ]
	 *     ]
	 */
	public function toArray()
	{
		$result = array();
		$page = $this->getPage();

		// do we need previous link?
		if (true) {
			// are we on first page?
			if ($page == 1) {
				$result["prev"] = array("page" => false, "uri" => "#", "isCurrent" => false, "isDisabled" => true);
			} else {
				$result["prev"] = array("page" => $page - 1, "uri" => $this->createLink($page - 1), "isCurrent" => false, "isDisabled" => false);
			}
		}
		// do we need first link?
		if ($this->getItems() > 0) {
			// are we on first page?
			if ($page == 1) {
				$result[1] = array("page" => 1, "uri" => $this->createLink(1), "isCurrent" => true, "isDisabled" => true);
			} else {
				$result[1] = array("page" => 1, "uri" => $this->createLink(1), "isCurrent" => false, "isDisabled" => false);
			}
		}

		$proximity = $this->getProximity();
		$maxPages = $proximity * 2 + 1;

		// 2 .. last_page - 1
		if ($last = $this->getTotalPages()) {
			if ($last <= $maxPages) {
				$from = 2;
				$to = $last - 1;
			} else {
				$from = max(2, $page - $proximity);
				$to = min($last - 1, $page + $proximity);
				while ($to - $from < $maxPages - 1) {
					if ($from > 2) {
						$from--;
					} elseif ($to < $last - 1) {
						$to++;
					}
				}
			}
			// do we need "..." button after first page
			if ($from > 2 && $proximity) {
				$result["less"] = array("page" => 0, "uri" => "#", "isCurrent" => false, "isDisabled" => true);
				$from++;
			}
			// do we need "..." button before last page
			if ($to < $last - 1) {
				$to--;
				$showMore = true;
			} else {
				$showMore = false;
			}
			for ($i = $from; $i <= $to; $i++) {
				$result[$i] = array("page" => $i, "uri" => $this->createLink($i), "isCurrent" => ($i == $page), "isDisabled" => false);
			}
			if (!$proximity) {
				$result[$i] = array("page" => $page, "uri" => $this->createLink($page), "isCurrent" => true, "isDisabled" => false);
			}
			if ($showMore && $proximity) {
				$result["more"] = array("page" => 0, "uri" => "#", "isCurrent" => false, "isDisabled" => true);
			}
		}

		// do we need last link?
		if ($this->getTotalPages() > 1) {
			// are we on last page?
			if ($page == $last) {
				$result[$last] = array("page" => $last, "uri" => $this->createLink($last), "isCurrent" => true, "isDisabled" => true);
			} else {
				$result[$last] = array("page" => $last, "uri" => $this->createLink($last), "isCurrent" => false, "isDisabled" => false);
			}
		}
		// do we need next link?
		if (1) {
			// are we on last page
			if ($page >= $last) {
				$result["next"] = array("page" => false, "uri" => "#", "isCurrent" => false, "isDisabled" => true);
			} else {
				$result["next"] = array("page" => $page + 1, "uri" => $this->createLink($page + 1), "isCurrent" => false, "isDisabled" => false);
			}
		}

		return $result;
	}

	/**
	 * Returns a link for a given page based on current URL and URI pattern.
	 *
	 * @param  integer $page Page number
	 * @return string
	 */
	public function createLink($page)
	{
		// if we find any matches, the URI we have to change the URI
		if (preg_match($this->buildPattern(), $this->getUri(), $matches)) {
			$link = str_replace($matches[0], preg_replace("~([1-9][0-9]*)~i", $page, $matches[0]), $this->getUri());
		// not found a pattern in the URI, we'll check do we deal with a get parameter
		} elseif (strpos($this->uriPattern, "=")) {
			if (strpos($this->getUri(), "?")) {
				$link = $this->getUri()."&".str_replace("{page}", $page, $this->uriPattern);
			} else {
				$link = $this->getUri()."?".str_replace("{page}", $page, $this->uriPattern);
			}
		// URI friendly
		} else {
			if (strpos($this->getUri(), "?")) {
				$parts = explode("?", $this->getUri(), 2);
				$link = $parts[0] . "/".str_replace("{page}", $page, $this->uriPattern) . "?" . $parts[1];
			} else {
				$link = $this->getUri()."/".str_replace("{page}", $page, $this->uriPattern);
			}
		}

		return $link;
	}

	protected function buildPattern()
	{
		// get the current page from the URI
		$pattern = str_replace("{page}", "([1-9][0-9]*)", $this->uriPattern);
		if (strpos($pattern, "=") > 0) {
			// starts with "&" or "?"
			// at the end of the URI or next char "&"
			$pattern = "[&\?]".$pattern."(&|\Z)";
		} else {
			// starts with "/";
			// it should be end of the URI or next char should be "/" or "?"
			$pattern = "/".$pattern."(/|\?|\Z)";
		}

		return "~{$pattern}~i";
	}
}
