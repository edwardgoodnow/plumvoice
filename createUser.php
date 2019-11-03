<?php 
ini_set('display_errors', 1);
echo 'Create User on production server using our API class';
include(dirname(__FILE__) . "/Curl.php");


$R = new CurlRequestor();


$user = array(
            'email' => 'user@plumvoice.com',
            'password' => sha1('password'),//this of course should all be part of a form that is first validated
            'confirm_password' => sha1('confirm_password'),//let's be sure to encrypt our passwords
            'first_name' => 'John',
            'last_name' => 'Doughe',//because I have a sense of humor 
            'street_number' => '1600 Pennsylvania Ave.',
            'apartment_number' => ''//this I am sure is optional
            'city' => 'Washington',
            'state' => 'DC'
            );

$data = $R->CreateUser('https://api.example.com', $user);
print_r($data);
