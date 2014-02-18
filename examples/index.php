<?php

use SugiPHP\Pagination\Pagination;

include "../vendor/autoload.php";
include "bootstrap3.php";

$pagination = new Pagination();
$pagination->setTotalItems(105)->setProximity(2)->setItemsPerPage(10); // same as setLimit()
?>
<html>
<head>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet" />
<style>
body {
	max-width: 580px;
	margin: 20px auto;
}
</style>
</head>
<body>
<h3>Bootstrap3 Pagination</h3>
<?= renderPagination($pagination->toArray()); ?>
<h3>Bootstrap3 Pager</h3>
<?= renderPager($pagination->toArray()); ?>
<h3>Bootstrap3 Aligned Pager</h3>
<?= renderAlignedPager($pagination->toArray()); ?>
</body>
</html>
