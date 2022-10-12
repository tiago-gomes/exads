<?php
/*
* Write a PHP script that prints all integer values from 1 to 100.
* Beside each number, print the numbers it is a multiple of (inside brackets and comma-separated). If
* only multiple of itself then print “[PRIME]”.
*/
for($i=1;$i<=100;$i++) {
	echo $i . "\n";
	isMultiple($i);
}

function isMultiple(int $index){
	$multiple = [];
	for($i=1;$i<=100;$i++) {
		if ($i % $index == 0) {
			$multiple[] = $i;
		}
	}
	if (count($multiple) === 1 && $multiple[0] === $index) {
		echo "[PRIME]\n";
	} else {
        echo "[" . implode(",", $multiple) . "]\n";
    }
}
?>