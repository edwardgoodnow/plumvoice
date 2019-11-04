<?php 
ini_set('display_errors', 1);
//echo 'Update User on production server using our API class<br />';
require(dirname(__FILE__) . "/Curl.php");


$R = new CurlRequestor();


$user = array(
            'password' => base64_encode('Pa55w0rd'),//this of course should all be part of a form that is first validated
            'confirm_password' => base64_encode('Pa55w0rd'),//let's be sure to encrypt our passwords
            'street_number' => '1600 Pennsylvania Ave.',
            'apartment_number' => '1',//this I am sure is optional
            'city' => 'Washington',
            'state' => 'DC'
            
            );
$uid = 23234;
$data = $R->UpdateUser(     $R->protocol() . '://api.example.com', $uid, $user);//did not set up an SSL for this thats why the url is http and not https
print_r($data);
