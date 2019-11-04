<?php 
ini_set('display_errors', 1);

//echo "Going to cheat a little bit here, but this is more of a real world scenario anyways";


//echo 'Update User on production server using our API class<br />';
require(dirname(__FILE__) . "/Curl.php");


$R = new CurlRequestor();
$data =  array(
                "signed_date" => date("Y-m-d H:i:s"),
                "timestamp" => time(),
                "retired_date" => null
           );     


//$team = 8;//to get from id
$team = $_REQUEST['team'];

$data = $R->Players(     $R->protocol() . '://api.example.com', $team, $data);//did not set up an SSL for this thats why the url is http and not https
print_r($data);

