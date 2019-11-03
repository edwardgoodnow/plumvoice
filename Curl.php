<?php

//documentation notes...shouldn't sucess be a 200 response, not a 201 response?
class CurlRequestor {
    
    
    function asXML($data){
    
    
    }
    function asHTML($data){
    
    
    }
    function asCSV($data){
    
    
    }
    function Curl($url, $endpoint, $data = array(), $credentials = array(), $method = 'post', $convert = 0){//post data to api
    //check for valid credentials(basic testing)
            if(count($credentials)==0 | empty($credentials['user']) | empty($credentials['pass'])){
                    echo "Please provide valid credentials";
                    return;
            }
    //check for vaid api method        
            $valid_endpoints = array();
            if(empty($endpoint) | !in_array($endpoint, $valid_endpoints)){
                     echo "Please provide a valid API method";
                    return;
            }
            
                $fields_string = 'username=' . $credentials['user'] . "&pass=" . $creds['pass'] . "&";
                 //url-ify the data for the POST
                foreach($data as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                rtrim('&', $fields_string);
                
                
                
                //open connection
                $ch = curl_init();

                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, $url);
                curl_setopt($ch,CURLOPT_POST, count($fields));
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                //return the transfer as a string
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 

                //execute post
                $result = curl_exec($ch);
//      echo curl_error($ch);
                //close connection
                curl_close($ch);
              
                switch($convert){
                    case(2)://as array
                        return json_decode($result, true);
                    break    
                    case(1)://as object  
                        return json_decode($result);
                    break;
                    case(0)://as json
                        return $result;
                    break;
                    //okay just getting smart here...might as well think about future upgrades
                    case(3):
                        return $this->asXML($result);
                    break;
                    case(4):
                        return $this->asCSV($result);
                    break;
                    case(5):
                        return asHTML($result);
                    break;
                }
                return $result;//in case no option was provided lets just return the json
    }
    function Credentials($url){
        switch($url){
            case('https://staging-api.example.com'):
                    //matches dev server
                    return array('user' => 'devUsername', 'pass' => '$*rs9D(');
            break;
            case('https://api.example.com'):
                    //matches production server
                    return array('user' => 'Username', 'pass' => '&&KeXt97&sd');
            break;
         }   
    }
            function CreateUser($url, $data){
                    $creds = $this->Credentials($url);
                    return $this->Curl($url, '/user', $data, $creds, 'post');
            }
            function RetrieveUser($url){
                    $creds = $this->Credentials($url);
                    return $this->Curl($url, '/Get User', $data, $creds, 'post');
            }
            function DeleteUser($url0{
                    $creds = $this->Credentials($url);
                    return $this->Curl($url, '/Delete User', $data, $creds, 'post');
            }
            function updateUser($url)[
                    $creds = $this->Credentials($url);
                    return $this->Curl($url, '/Update User', $data, $creds, 'post');
            }
}
