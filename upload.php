<?php
$uploadDir = "uploads/";
$path = "http://".$_SERVER["SERVER_NAME"]."/imagesearch/";
$output = array('statu'=>1, 'text'=>'nothing to happened.');
if(isset($_FILES['file']['tmp_name'])){
    $uploadedFiles = $_FILES["file"];
    $extName = getExtensionName($_FILES['file']['name']);
    $saveFileName = md5_file($_FILES['file']['tmp_name']).".".$extName;
    if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir.$saveFileName )){
        $output = array('statu'=>0, 'text'=>'Ready.', 'url'=>$path.$uploadDir.$saveFileName );
    }
    else{
        $output = array('statu'=>1, 'text'=>'Failed.', 'console'=>'file upload failed.');
    }
}
else{
    switch( strtolower(substr($_POST['file'], 0, 4)) ){
        case 'http':
            $extName = getExtensionName($_POST['file']);
            $data = file_get_contents($_POST['file']);
            if($data !== false){
                $fileName = md5($_POST['file']).".".$extName;
                file_put_contents($uploadDir.$fileName, $data);
                $output = array('statu'=>0, 'text'=>'Ready.', 'path'=>$uploadDir.$fileName, 'url'=>$path.$uploadDir.$fileName );
            }
            else{
                $output = array('statu'=>1, 'text'=>'error: '.$_POST['file'], 'console'=>'get file fail.');
            }
            break;
        case 'data':
            $pattern = "/^(data:image\/(jpeg|png|gif);base64).*/i";
            preg_match($pattern, $_POST['file'], $matches);
            if(0<count($matches)){
                $data = base64_decode(str_replace($matches[0], '', $_POST['file']));
                $fileName = md5($data).$matches[1];
                file_put_contents($uploadDir.$fileName, $data);
                $output = array('statu'=>0, 'text'=>'Ready.', 'url'=>$path.$uploadDir.$fileName );
            }
            else{
                $output = array('statu'=>1, 'text'=>'error', 'console'=>'extension name can not detect.');
            }
            break;
        default:
            $output = array('statu'=>1, 'text'=>'error', 'console'=>'data type can not detect.');
    }
    
}

echo json_encode($output);

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