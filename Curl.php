<?php
ini_set('display_errors', 1);
//probably credentials should not include '&, =' or other url characters to avoid errors?
//documentation notes...shouldn't sucess be a 200 response, not a 201 response?
class CurlRequestor {
    function protocol(){
    $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isSecure = true;
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $isSecure = true;
        }
        $PROTOCOL = $isSecure ? 'https' : 'http';
        return $PROTOCOL;
    }
    
    function asXML($data){
    
    
    }
    function asHTML($data){
    
    
    }
    function asCSV($data){
    
    
    }
    function Curl($url, $endpoint, $data = array(), $credentials = array(), $method = 'post', $json = null, $convert = 0){//post data to api
    
    //check for valid credentials(basic testing)
            if(count($credentials)==0 | empty($credentials['user']) | empty($credentials['pass'])){
                    echo "Please provide valid credentials<br />";
                    return;
            }
    //check for vaid api method        
            $valid_endpoints = array('/user');
            if(empty($endpoint)){
                     echo "Please provide a valid API method<br />";
                    return;
            }
          
        
            
                $fields_string = 'api_user=' . $credentials['user'] . "&api_secret=" . base64_encode($credentials['pass']) . "&";
                 //url-ify the data for the POST
                foreach($data as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
                rtrim('&', $fields_string);
             
                
                //open connection
                $ch = curl_init();
                if($method == 'post'){
                        //set the url, number of POST vars, POST data
                        curl_setopt($ch,CURLOPT_URL, $url . $endpoint);
                    if(!isset($json)){
                        curl_setopt($ch,CURLOPT_POST, count($data));
                        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                     }else{
                         array_merge($data, array('api_user' => $credentials['user'], 'api_secret' => $credentials['pass']));
                         $data_string = json_encode($data);
                         //echo $data_string; exit;
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                                'Content-Type: application/json',                                                                                
                                'Content-Length: ' . strlen($data_string))                                                                       
                            );                                                                                                                   
               
                     }
                     
                }else if($method == 'delete'){
                        curl_setopt($ch,CURLOPT_URL, $url . $endpoint . '?' . $fields_string);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                }
                else{
                        curl_setopt($ch,CURLOPT_URL, $url . $endpoint . '?' . $fields_string);
                
                }
                
                //return the transfer as a string
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER  , true);
 

                //execute post
                $result = curl_exec($ch);
                if(!empty(curl_error($ch))){
                    echo curl_error($ch);
                     
                }
                
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                ob_start();
               
                switch($httpcode){
                    case(400):
                        echo "invalid parameters supplied, do not submit with the same parameters";
                        ob_flush();
                        exit;
                    break;
                    case(401):
                        echo "permission denied, invalid http basic credentials";
                        ob_flush();
                        exit;                    
                    break;
                    case(500):
                        echo "Internal Server Error";
                        ob_flush();
                        exit;                    
                    break;
                    case(409):
                        echo " user already exists";
                        ob_flush();
                        exit;                    
                    break;
                    case(404):
                        echo "page not found";
                        ob_flush();
                        exit;                    
                    break;
                    /*
                    The following http codes are possible:
                        204 – user deleted successfully
                        401 – permission denied, invalid http basic credentials
                        404 – user not found
                        500 – internal server error
                    */    

                }    
                ob_end_clean();
                
                
                 curl_setopt( $ch, CURLOPT_HEADER, false);
                $result = curl_exec($ch);
                   
                //close connection
                curl_close($ch);
              
                switch($convert){
                    case(2)://as array
                        return json_decode($result, true);
                    break;    
                    case(1)://as object  
                        return json_decode($result);
                    break;
                    case(0)://as json
                        header('content-type: application/json');
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
            case($this->protocol() . '://staging-api.example.com'):
                    //matches dev server
                    return array('user' => 'devUsername', 'pass' => '$*rs9D(');
            break;
            case($this->protocol() . '://api.example.com'):
                    //matches production server
                    return array('user' => 'Username', 'pass' => '&&KeXt97&sd');
            break;
         }   
    }
            function CreateUser($url, $data, $creds = null){
                    $creds = $this->Credentials($url);
                    //print_r($creds); 
                    //echo "<br />server => " . $url . "/user<br />";
                    //echo "<br />get our credentials dynamically if we own the server<br />"; 
                    return $this->Curl($url, '/user', $data, $creds, 'post');
            }
         
            function RetrieveUser($url, $id){
                    $creds = $this->Credentials($url);
                    return $this->Curl($url, '/user/' . $id, array(), $creds, 'get');
            }
            function deleteUser($url, $id){
           
                    $creds = $this->Credentials($url);
                    return $this->Curl($url, '/delete/' . $id, array(), $creds, 'delete');
            }
            function UpdateUser($url, $id, $data){
                    $creds = $this->Credentials($url);
                    return $this->Curl($url, '/user/' . $id, $data, $creds, 'post', 1);
            }
            function Players($url, $id, $data){
                    $creds = $this->Credentials($url);
                    if(is_numeric($id)){
                        return $this->Curl($url, '/players/' . $id, $data, $creds, 'get', 1);
                    }else{
                    
                        return $this->Curl($url, '/players/' . $id, $data, $creds, 'post', 1);
                    }
            }
            function Goals($url, $id, $data){
                    $creds = $this->Credentials($url);
                    
                        return $this->Curl($url, '/players/' . $id, $data, $creds, 'get', 1);
            }            
}


