Pagination
==========

[![Build Status](https://travis-ci.org/SugiPHP/Pagination.svg?branch=master)](https://travis-ci.org/SugiPHP/Pagination) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/66b3b165-5039-4811-9c37-b6ade6fb9298/mini.png)](https://insight.sensiolabs.com/projects/66b3b165-5039-4811-9c37-b6ade6fb9298)

SugiPHP Pagination is a simple to use class that provides pagination links for your app. You can customize the look and feel of the
pages links by writing a custom renderer or use one of the available ones like Twitter's Bootstrap or extend them.

Basic usage
-----------

Pagination class by it's own does not have any renders, instead it's primary goal is to give an array of items (links). This can be done easily:

```php
<?php

use SugiPHP\Pagination\Pagination;

$pagination = new Pagination();
$pagination->setTotalItems(45); // Set the total number of items
$pages = $pagination->toArray(); // described below

?>
```

If the web page's URL is http://example.com/index.php?page=3 Pagination will guess that the current page is 3 and will return something like this:

```php
<?php

var_dump($pages);

// some parameters are removed for better readability
[
	'prev' => [
		'page' => 2,
		'uri' => '/index.php?page=1',
		'isDisabled' => false
	],
	1 => [
		'page' => 1,
		'uri' => '/index.php?page=1',
		'isCurrent' => false,
	],
	2 => [
		'page' => 2,
		'uri' => '/index.php?page=2',
		'isCurrent' => false,
	],
	3 => [
		'page' => 3,
		'uri' => '/index.php?page=3',
		'isCurrent' => true,
		'isDisabled' => true,
	],
	'next' => [
		'page' => 2,
		'uri' => '#',
		'isDisabled' => true
	]
]
?>
```

Getters
-------

```php
<?php

/*
 * Returns current page number if it is set, or will try to guess it from the URL address.
 * Default is 1
 */
$pagination->getPage();

/*
 * Returns the maximum number of items viewed in a single page.
 * Default is 20
 */
$pagination->getItemsPerPage();

// getItemsPerPage() alias
$pagination->getLimit();

/*
 * Returns URI pattern that is used to generate links and to guess current page.
 * Default is 'page={page}'
 */
$pagination->getPattern();

// Returns total number of pages based on total items and items per page settings.
$pagination->getTotalPages();

/*
 * Returns first item's index in a page we are on.
 * Used primary in SQLs (e.g. SELECT * FROM test LIMIT 20 OFFSET 60)
 */
$pagination->getOffset();

/*
 * Returns proximity - how many page links should be in front and after current page.
 * Default is 4.
 * If there is not enough pages to display in front of the current page links
 * after will be more then proximity. So if you are on page 2 and proximity is 3
 * the pages after the page 2 would not be 3, but 5.
 * Total number of links (items toArray() method returns) can be calculated by
 * proximity * 2 + 1 (current page) + 1 (previous) + 1 (next) + 1 (first) + 1 (last).
 * So if proximity is set to 4 total number of links will be 13
 * if proximity is 3 total pages will be 11
 * Note: Total number of links will be less if there are not enough pages to show.
 */
$pagination->getProximity();

// returns previously set total items
$pagination->getTotalItems();

// getTotalItems() alias
$pagination->getItems();

?>
```

Setters
-------
```php
<?php

// Total number of items. This one MUST be set.
$pagination->setTotalItems($totalItems);

// setTotalItems() alias
$pagination->setItems($totalItems);

// Sets the number of items (lines) in a single page.
$pagination->setItemsPerPage($itemsPerPage);

// setItemsPerPage() alias
$pagination->setLimit($itemsParPage);

// Sets the page number manually.
$pagination->setPage($page);

/*
 * Sets the URI pattern for creating links for pages.
 * Default pattern is "page={page}"  (URLs like /posts/show?page=5)
 * Can be set for example to "p={page}" or anything else for $_GET parameter
 * Can be set also to "page/{page}" for friendly URLs. In this case Pagination
 * will build URLs like: /posts/show/page/5
 */
$pagination->setPattern($pattern);

/*
 * Sets the current URI. Default is $_SERVER["REQUEST_URI"]
 * Handy for unit tests.
 */
$pagination->setUri($uri);

// Sets the proximity. See getProximity() above for more explanations.
$pagination->setProximity($proximity);

?>
```

Each setting can be done in the Pagination constructor.

```php
<?php

$config = array(
	'items' => 100, // or 'totalItems'
	'itemsPerPage' => 10, // or 'ipp'
	'proximity' => 3,
	'uri' => 'http://example.com/show/page:6',
	'pattern' => 'page:{page}',
	'page' => 6,
);
$pagination = new Pagination($config)

?>
```

Basic renderer
--------------
```php
<?php

$pages = $pagination->toArray();

// Twitter Bootstrap 3 pagination
$items = "";
foreach ($pages as $key => $page) {
	// link
	$href = $page["uri"];

	// label
	if ($key === "prev") {
		$label = "&laquo;";
	} elseif ($key === "next") {
		$label = "&raquo;";
	} elseif ($key === "less" or $key === "more") {
		$label = "...";
	} else {
		$label = $page["page"];
	}

	// class
	if ($page["isCurrent"]) {
		$class = "active";
	} elseif ($page["isDisabled"]) {
		$class = "disabled";
	} else {
		$class = "";
	}
	$items .= '<li class="'.$class.'"><a href="'.$href.'">'.$label.'</a></li>';
}

echo '<ul class="pagination">' . $items . '</ul>';

?>
```

You can see more renders examples in project's [examples](https://github.com/SugiPHP/Pagination/blob/master/examples/bootstrap3.php)
