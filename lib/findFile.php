<?php

/*
name: findFile
type: function
description: array findFile ( string $dir , string $needle [, string $method = 'EQUAL', bool $recursive = true ] )
parameters: dir, str, method(EQUAL, NOT_EQUAL, CONTAIN, NOT_CONTAIN), recursive(true, false)
return: return the resulting array.
*/

if(isset($argv[1]) && getcwd().DIRECTORY_SEPARATOR.$_SERVER['PHP_SELF']== __FILE__){
  $result = false;
  switch(count($argv)){
    case 3:
        $result = findFile($argv[1], $argv[2]);
      break;
    case 4:
      if( isset($argv[3]) && in_array($argv[3], array('EQUAL', 'NOT_EQUAL', 'CONTAIN', 'NOT_CONTAIN', 'REGEX')) ){
        $result = findFile($argv[1], $argv[2], $argv[3]);
      }
      else{
        echo "parameter[3] should be 'EQUAL' or 'NOT_EQUAL' or 'CONTAIN' or 'NOT_CONTAIN'.\r\n";
      }
      break;
    case 5:
      if( isset($argv[3]) && in_array($argv[3], array('EQUAL', 'NOT_EQUAL', 'CONTAIN', 'NOT_CONTAIN', 'REGEX')) ){
        if(($argv[4]=='true' || $argv[4]=='false')){
          $result = findFile($argv[1], $argv[2], $argv[3], $argv[4]);
        }
        else{
          echo "parameter[4] is not true or false.\r\n";
        }
      }
      else{
        echo "parameter[3] should be 'EQUAL' or 'NOT_EQUAL' or 'CONTAIN' or 'NOT_CONTAIN' or 'REGEX'.\r\n";
      }
      break;
  }
  if($result !== false){
    print_r($result);
  }
  else{
    echo 'false'."\r\n";
  }
}

function findFile($dir, $str, $method = 'EQUAL', $recursive = true){
  $output = array();
  $exclude = array('.', '..');
  if(is_dir($dir) && is_readable($dir)){
    if(substr($dir, -1) != DIRECTORY_SEPARATOR){
      $dir .= DIRECTORY_SEPARATOR;
    }
    $d = dir($dir);
    while($read = $d->read() ){
      if(is_readable($dir.$read)){
        if( is_file($dir.$read)  ){
          switch($method){
            case 'EQUAL':
              if($read == $str){
                $output[] = $dir.$read;
              }
              break;
            case 'NOT_EQUAL':
              if($read != $str){
                $output[] = $dir.$read;
              }
              break;
            case 'CONTAIN':
              if( strstr($read, $str) ){
                $output[] = $dir.$read;
              }
              break;
            case 'NOT_CONTAIN':
              if( !strstr($read, $str) ){
                $output[] = $dir.$read;
              }
              break;
            case 'REGEX':
              if(preg_match($str, $read)){
                $output[] = $dir.$read;
              }
              break;
          }
        }
        else if(is_dir($dir.$read) && !in_array($read, $exclude) && $recursive===true){
          $output = array_merge($output, findFile($dir.$read, $str, $method, $recursive));
        }
      }
    }
    if(isset($output) && is_array($output)){
      return $output;
    }
  }
  else{
    return $output;
  }
}
?>