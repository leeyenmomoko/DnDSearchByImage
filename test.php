<?php
getExtensionName("cc.jpg?jasdkald");
//getExtensionName("cdata:image/jpeg;base64dsajsdklj");
function getExtensionName($fileName){
    $extName = pathinfo(strtolower($fileName), PATHINFO_EXTENSION);
    $pattern = "/^(jpg|jpeg|png|gif).*/i";
    preg_match($pattern, $extName, $matches);
    if(0<count($matches)){
        return $matches[1];
    }
    else{
        return false;
    }
}


?>