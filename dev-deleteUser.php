<?php 
ini_set('display_errors', 1);
//echo 'Delete User on production server using our API class<br />';
require(dirname(__FILE__) . "/Curl.php");


$R = new CurlRequestor();


$user = array(
            'id' => '23234'  //I used this with the hockey database with userid = 11 and it works
            );

$data = $R->deleteUser(  $R->protocol() . '://staging-api.example.com', $user['id']);//did not set up an SSL for this thats why the url is http and not https
print_r($data);
