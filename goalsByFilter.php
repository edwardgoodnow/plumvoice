<?php 
ini_set('display_errors', 1);

//echo "Going to cheat a little bit here, but this is more of a real world scenario anyways";


//echo 'Update User on production server using our API class<br />';
require(dirname(__FILE__) . "/Curl.php");


$R = new CurlRequestor();
$data =  $_REQUEST['filters'];   



$data = $R->Goals(     $R->protocol() . '://api.example.com', $team, $data);//did not set up an SSL for this thats why the url is http and not https
print_r($data); 
