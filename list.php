<?php
require('lib/findFile.php');
$root = "uploads";
$node = explode("/", $_SERVER['PHP_SELF']);
array_pop($node);
$path = "http://".$_SERVER["SERVER_NAME"]."/".array_pop($node)."/";

$files = findFile($root, "/\.jpg|\.png|.gif/", 'REGEX');

if(isset($files) && 0<count($files)){
    foreach($files as $filePath){
        echo "<img src=\"".$path.$filePath."\" /><br />";
        echo $filePath."<br />";
    }
}

?>