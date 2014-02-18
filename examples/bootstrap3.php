<?php

function renderPagination(array $pages)
{
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
}

function renderPager(array $pages)
{
	$string = '<ul class="pager">';
	$string .= '<li rel="prev"'.($pages["prev"]["isDisabled"] ? 'class="disabled"' : '').'><a href="'.$pages["prev"]["uri"].'">Previous</a></li>';
	$string .= '<li rel="next"'.($pages["next"]["isDisabled"] ? 'class="disabled"' : '').'><a href="'.$pages["next"]["uri"].'">Next</a></li>';
	$string .= '</ul>';

	return $string;
}

function renderAlignedPager(array $pages)
{
	$string = '<ul class="pager">';
	$string .= '<li class="previous'.($pages["prev"]["isDisabled"] ? ' disabled' : '').'"><a href="'.$pages["prev"]["uri"].'">&larr; Older</a></li>';
	$string .= '<li class="next'.($pages["next"]["isDisabled"] ? ' disabled' : '').'"><a href="'.$pages["next"]["uri"].'">Newer &rarr;</a></li>';
	$string .= '</ul>';

	return $string;

}
