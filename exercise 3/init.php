<?php
require_once 'tvSerieRepository.php';

$repository = new tvSerieRepository();
$data = $repository->listTvShowByTitle();
var_dump($data);
?>