<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('../../lib/simpleHtmlDom/simple_html_dom.php');
$searchService = "https://www.google.com/searchbyimage?&image_url=";
$searchUrl = $_POST['url'];
$output = array('statu'=>1, 'console'=>'no execute.', 'objs'=>array() );

$data = fetch_google($searchUrl);
$dom = str_get_html($data);

$result = $dom->find('div#ires div.srg li.g div.rc');

if(isset($result) && $result){
    $output['statu'] = 0;
    $output['console'] = 'success';
    foreach($result as $target){
        $obj = array('link'=>'', 'image'=>'');
        $links = $target->find('h3.r a');
        if($links){
            foreach($links as $link){
                $obj['link'] = $link->outertext;
            }
        }
        
        $images = $target->find('div.s div.thb a');
        if($images){
            foreach($images as $image){
                $image->href = 'http://www.google.com'.$image->href;
                /*$image_as = $image->find('a');
                if($image_as){
                    foreach($image_as as $image_a){
                        $image_a->href = 'http://www.google.com'.$image_a->href;
                    }
                }*/
                $obj['image'] = $image->outertext;
            }
        }
        $output['objs'][] = $obj;
    }
}
else{
    $output = array('statu'=>1, 'console'=>"can not find result.", 'objs'=>array() );
}
echo json_encode($output);


function fetch_google($u, $terms="sample search",$numpages=1,$user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0'){
    $ch = curl_init();
    $url = 'http://www.google.com/imghp?hl=en&tab=wi';
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_REFERER, 'http://www.google.com/');
    curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT,120);
    curl_setopt ($ch,CURLOPT_TIMEOUT,120);
    curl_setopt ($ch,CURLOPT_MAXREDIRS,10);
    curl_setopt ($ch,CURLOPT_COOKIEFILE,"cookie.txt");
    curl_setopt ($ch,CURLOPT_COOKIEJAR,"cookie.txt");
    curl_exec($ch);
    $searched="";
    for($i=0;$i<$numpages;$i++){
        $ch = curl_init();
        $url="http://www.google.com/searchbyimage?hl=en&image_url=".urlencode($u);
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_REFERER, 'http://www.google.com/imghp?hl=en&tab=wi');
        curl_setopt ($ch,CURLOPT_CONNECTTIMEOUT,120);
        curl_setopt ($ch,CURLOPT_TIMEOUT,120);
        curl_setopt ($ch,CURLOPT_MAXREDIRS,10);
        curl_setopt ($ch,CURLOPT_COOKIEFILE,"cookie.txt");
        curl_setopt ($ch,CURLOPT_COOKIEJAR,"cookie.txt");
        $searched=$searched.curl_exec ($ch);
        curl_close ($ch);
    }
    return $searched;
}
?>