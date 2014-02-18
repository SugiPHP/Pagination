<?php

$string = '<ul class="pagination">';
foreach ($pages as $key => $page) {
	// link
	$href = $page["uri"];

	$rel = "";
	// label
	if ($key === "prev") {
		$label = "&laquo;";
		$rel = ' rel="prev"';
	} elseif ($key === "next") {
		$label = "&raquo;";
		$rel = ' rel="next"';
	} elseif ($key === "less" or $key === "more") {
		$label = "...";
	} else {
		$label = $page["page"];
	}

	// class
	if ($page["isCurrent"]) {
		$class = "active";
		$string .= '<li class="active"><a'.$rel.' href="'.$href.'">'.$label. '<span class="sr-only">(current)</span></a></li>';
	} elseif ($page["isDisabled"]) {
		$string .= '<li class="disabled"><span>'.$label.'</span></li>';
	} else {
		$string .= '<li><a'.$rel.' href="'.$href.'">'.$label.'</a></li>';
	}

}
$string .= '</ul>';

return $string;
