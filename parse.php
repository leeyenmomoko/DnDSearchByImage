<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('lib/simpleHtmlDom/simple_html_dom.php');
$searchService = "https://www.google.com/searchbyimage?&image_url=";
$searchUrl = isset($_REQUEST['url']) ? $_REQUEST['url'] : 'http://test.leeyen.idv.tw/imagesearch2/uploads/c39d87bd75f0d7fe8f7e310a125fb938.jpg';
$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
$output = array('statu'=>1, 'console'=>'no execute.', 'objs'=>array() );

$data = googleSearchByImages($searchUrl, 1);
$dom = str_get_html($data);
if($dom){
    $tmpBlock = $dom->find('div#infoDiv');
    if(!$tmpBlock){
        $result = $dom->find('div#ires div.srg li.g div.rc');
        if(isset($result) && $result){
            foreach($result as $target){
                $obj = array('link'=>'', 'image'=>'', 'class'=>'');
                $links = $target->find('h3.r a');
                if($links){
                    foreach($links as $link){
                        $link->target = "_blank";
                        $obj['link'] = $link->outertext;
                    }
                }
                
                $images = $target->find('div.s div.thb a');
                if($images){
                    foreach($images as $image){
                        $image->href = 'http://www.google.com'.$image->href;
                        $image->target = "_blank";
                        $obj['image'] = $image->outertext;
                    }
                }
                if( ''==$filter || strstr($obj['link'], $filter) ){
                    $obj['class'] = 'match';
                }
                else{
                    $obj['class'] = 'noMatch';
                }
                
                $output['objs'][] = $obj;
            }
            if(0<count($output['objs'])){
                $output['statu'] = 0;
                $output['console'] = 'success';
            }
            else{
                $output = array('statu'=>1, 'console'=>"no match filter image.", 'objs'=>array() );
            }
        }
        else{
            $output = array('statu'=>1, 'console'=>"no result.", 'objs'=>array() );
        }
    }
    else{
        $output = array('statu'=>1, 'console'=>"service temporarily unavailable", 'objs'=>array() );
    }
}
else{
    $output = array('statu'=>1, 'console'=>'request timeout.', 'objs'=>array() );
}

if(isset($_REQUEST['url'])){
    echo json_encode($output);
}
else{
    echo "<pre>"; print_r($output); "</pre>";
}
function googleSearchByImages($u, $numOfPages = 1, $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0'){
    /*$handle = fopen('cookie.txt', 'w');
    fwrite($handle, '');
    fclose($handle);*/
    $ch = curl_init();
    $url = 'http://www.google.com/imghp?hl=zh-TW&tab=wi';
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_REFERER, 'http://www.google.com/');
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt ($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt ($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt ($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_exec($ch);
    $searched = "";
    $output = "";
    
    $ch = curl_init();
    $url="http://www.google.com/searchbyimage?hl=zh-TW&image_url=".urlencode($u);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/imghp?hl=zh-TW&tab=wi');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    $searched = curl_exec($ch);
    curl_close($ch);
    
    $output = $searched;
    $dom = str_get_html($searched);
    $page = 1;
    if($dom){
        $next = $dom->find('div#main div#cnt div#navcnt a#pnnext');
        while( (isset($next) && $next && $page<=$numOfPages) ){
            //echo "page".$page." / ".$numOfPages."\r\n";
            $url="http://www.google.com/searchbyimage?hl=zh-TW&image_url=".urlencode($u)."&start=".$page*10;
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url );
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/imghp?hl=zh-TW&tab=wi');
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
            curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
            $output .= curl_exec($ch);
            //echo curl_exec($ch)."\r\n";
            curl_close($ch);
            $page++;
        }
    }
    
    return $output;
}

?>