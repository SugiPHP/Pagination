<?php

use SugiPHP\Pagination\Pagination;

include "../vendor/autoload.php";

$pagination = new Pagination();
$pagination->setTotalItems(105)->setProximity(2)->setItemsPerPage(10); // same as setLimit()
$pages = $pagination->toArray();
$bs3pagination = include "bootstrap3.php";
?>
<html>
<head>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<?=$bs3pagination;?>
</body>
</html>
