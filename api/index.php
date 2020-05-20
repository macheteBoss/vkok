<?php
require_once 'route.php';

$pos1 = strpos($_SERVER["REQUEST_URI"], '/', 2);
$buf = substr($_SERVER["REQUEST_URI"], $pos1+1);

$count = 0;
for($i = 0; strlen($buf) >= 0; $i++) {
    if($buf[$i] == "/" || strlen($buf) <= $count) break;
    $count++;
}
$apiName = substr($buf, 0, $count);

try {
    $api = new Route($apiName);
    $run = $api->marsh();
    echo $run->run();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage(), 'code' => $e->getCode()));
}
?>