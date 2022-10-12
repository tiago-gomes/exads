<?php
/*
* Write a PHP script to generate a random array containing all the ASCII characters from comma (“,”) to
* pipe (“|”). Then randomly remove and discard an arbitrary element from this newly generated array.
* Write the code to efficiently determine the missing character.
*/
$array = range(",", "|");
$count = count($array);
$rand = rand(1, $count);
unset($array[$rand]);

$newArray = range(",", "|");
foreach($newArray as $character) {
    if (!in_array($character, $array)) {
        echo "Missing character is: " . $character . "\n";
    }
}
?>