<?php

function array_greater_recursive($arr1, $arr2, $limit, $i = 0) {
	if ($i >= $limit)
		return 0;
	else if ($arr1[$i] > $arr2[$i])
		return 1;
	else if ($arr1[$i] == $arr2[$i])
		return array_greater_recursive($arr1, $arr2, $limit, $i+1);
	else
		return -1;
}
