<?php
require('../../lib/YenZ/fileSystem/findFile.php');
$root = "uploads";
$path = "http://".$_SERVER["SERVER_NAME"]."/imagesearch/";

$files = findFile($root, "/\.jpg|\.png|.gif/", 'REGEX');

if(isset($files) && 0<count($files)){
    foreach($files as $filePath){
        echo "<img src=\"".$path.$filePath."\" /><br />";
        echo $filePath."<br />";
    }
}

?>