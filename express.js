/*
 * use as express api server...useage is as follows `node express.js --type (dev|prod)
 */
var nconf = require('nconf');

  //
  // Setup nconf to use (in-order):
  //   1. Command-line arguments
  //   2. Environment variables
  //   3. A file located at 'path/to/config.json'
  //
  nconf.argv()
   .env();
   
var SITE = nconf.get('type');   
//get environment to run as here 
switch(SITE){
    case('dev'):
        var pre = 'https://staging-'; //change this for production or staging
    break; 
    case('prod'):
        var pre = 'https://'; //change this for production or staging
    break;
}    
if(!pre){
    console.log('start server with CLI argv `--type (dev|prod)`');
    return;
}
var port = (pre == 'http://staging-')?5001:5000;
console.log(port)
var server = pre + 'api.example.com';
var APIUSERNAME = (pre == 'https://staging-')?'devUsername':'Username';
var APIPASSWORD = (pre == 'https://staging-')?'$*rs9D(':'&&KeXt97&sd';
    //these are set by the CLI switch above...but can be modified if need be as well, let's keep our environment somewhat flexible and upgradable

var dbuser = APIUSERNAME;
var dbpass = APIPASSWORD;
    //I know these should be different for security reasons....but for development purposes I did it this way so everyone could follow what I did here
var dbname = (pre == 'https://staging-')?'hockey':'hockey';
    //let's at least have separate dbases for production and development


/*
 * 
 * change values above if needed
 * port is set so both servers can be run at the same time
 * useage would be using pm2, forever, node-mon or even systemd service to run this in the background
 * 
 */

var express        =        require("express");
var bodyParser     =        require("body-parser");
var app            =        express();
//Here we are configuring express to use body-parser as middle-ware.
app.use(bodyParser.urlencoded({ extended: false }));
app.use(bodyParser.json());

var validator = require("email-validator");
 

var moment = require('moment');
var net = require('net');

var mysql = require('mysql');

var exec = require('child_process').exec,
			    child;

 
    var mysql_options =  {
      host     : '127.0.0.1',
      user     : dbuser,
      password : dbpass,
      database : dbname,
      connectionLimit : 2,
        multipleStatements: true,
        insecureAuth: true,
        supportBigNumbers: true,
        bigNumberStrings: true,
        dateStrings: true,
        //debug: true,
        nestTables: '_'
    };
   var pool = mysql.createPool(
		      mysql_options
   );      	     

   
/*
 * 
 * Nothing left to edit below....this is where our real logic is
 * 
 */
function valid_user(params, res, connection, uid){
    if(!uid){
            if(validator.validate(params.email) == false){
            return res.json({"result": "error", "messages": {"email": "Please Provide A Valid Email"}}); 
            }
    }       

    
            if(params.password.length<8 | !Buffer(params.password, 'base64').toString('ascii').match(/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{8})$/ig)){
                
                return res.json({"result": "error", "messages": {"password": "Password Should be 8 Characters Long, 1 Number and 1 punctuation"}}); 
            }
            if(Buffer(params.password, 'base64').toString('ascii') != Buffer(params.confirm_password, 'base64').toString('ascii')){
                
                return res.json({"result": "error", "messages": {"confirm_password": "Passwords don't match"}});
            }
    if(!uid){        
            if(!params.first_name.match(/^[A-Za-z]+$/) | !params.last_name.match(/^[A-Za-z]+$/)){
                return res.json({"result": "error", "messages": {"names": "Please suppy a valid first and last name"}});
            }
    }
    if(!isNaN(uid)){
        connection.query("update player set password='" + Buffer(params.password, 'base64').toString('ascii') + "', street_number='" + params.street_number + "' where player_id=" + uid, function(err, result){
            console.log(err);
            return res.json({"result": "success", "messages":[{ "result": result, }], "status": "updated"});
        });     
            
    }else{
    connection.query("select * from player where email ='" + params.email + "'", function(err, result){
          if(result.length>0){
             return res.json({"result": "error", "messages": {"confirm_password": "email exists"}}); 
              
          }   else{ 
            connection.query("SET FOREIGN_KEY_CHECKS=0");
            connection.query("insert into team values(null, 'dallas penguins', 'Dallas', 'TX');", function(err, result){
            
                    connection.query("insert into player (player_id, first_name, last_name, signed_date, email, FK_team_id, password) values(null, '" + params.first_name + "', '" + params.last_name + "', NOW(), '" + params.email + "', '" + result.insertId + "', '" + Buffer(params.password, 'base64').toString('ascii') + "')", function(err, result2){
                        if(err){
                                console.log(err)
                            return res.json({"result": "error", "messages": {"database": err }});
                        }
                        
                        return res.json({"result": "success", "messages": {"database": result2, "user_id": result2.insertId, "team_id": result.insertId }});
                        connection.query("SET FOREIGN_KEY_CHECKS=1");
                    });
            });
          }   
    })       
    //table should have feilds for address password and more but it doesn't
    //also the sql you sent is invalid...the coumn was not set properly as a key
    
    //In production I would add an address lookup and check names as well here
    return {"result": "success"};
    }
}    
    pool.getConnection(function(dberr, connection) {
            if (dberr) {
                console.error('error connecting: ' + dberr.stack);
                return;
            }   
            app.route('/').get(function(req, res){
             
                    return res.json({"result":"You have reached our api", "mesages": { "username": req.body.username, "password": req.body.password} });
            })     
            
            //update user
            app.put('/user/:uid', function(req, res){
                connection.query("select * from player where player_id=" + req.params.uid, function(err, result){
                    if(result.length>0){
                        console.log('yep here we are');
                           console.log(req.body);
                          return valid_user(req.body, res, connection, req.params.uid);
                            
                    }else{
                            res.json({"error": "User was not found"});
                    }    
                    
                });     
              
            });
            //delete user 
             app.delete('/user/:user_id', function(req, res){
                console.log(req.params.user_id)
                connection.query("select player_id as user_id, concat(first_name, ' ', last_name) as name, email from player where player_id=" + req.params.user_id, function(err, result){
                   
                    if(result.length>0){
                        connection.query("delete from player where player_id=" + req.params.user_id);
                        res.json({"result": "success", "messages": result[0]});  
                        //delete the user here
                    }else{
                        res.json({"error": "User was not found"});
                        
                    }
                    return;
                });     
              
            });
            //retrieve user using get
            app.route('/user/:user_id').get(function(req, res){
                
                connection.query("select player.player_id as user_id, concat(first_name, ' ', last_name) as name, email from player where player_id=" + req.params.user_id, function(err, result){
                    if(result.length>0){
                        
                        res.json({"result": "success", "messages": result[0]});   
                    }else{
                        res.json({"error": "User was not found"});
                    }    
                    
                });     
              
            });
            //add a user using post
            app.route('/user').post(function(req, res){
                console.log(req);
                if(req.body.api_user != APIUSERNAME | Buffer(req.body.api_secret, 'base64').toString('ascii') != APIPASSWORD){//ugly hack to deal with protected symbols in password and user
                //return a 401 error here   
                    return res.json({"error":"You have reached our api", "mesages": "Please Check Your Credentials!" });
                }
                valid_user(req.body, res, connection);
            });
            
            //added the sql part of test here, will also include in notes
            
            app.route('/players/:team_id').get(function(req, res){
             if(req.params.team_id.match(/\d+/)){
                        console.log("select concat(first_name, ' ', last_name) as name, FK_position as  position, player_id as id, signed_date, count(goal_id) as goals  from player left join goal g on g.FK_player_id=player.player_id where FK_team_id=" + req.params.team_id);
                        connection.query("select concat(first_name, ' ', last_name) as name, FK_position as  position, player_id as id, signed_date, count(goal_id) as goals  from player left join goal g on g.FK_player_id=player.player_id where FK_team_id='" + req.params.team_id + "'", function(err, result){
                            if(result.length>0){
                                
                                res.json({"result": "success", "messages": result[0]});   
                            }else{
                                res.json({"error": "User was not found"});
                            }    
                            
                        });  
             }else{
                         console.log("select concat(first_name, ' ', last_name) as name, FK_position as  position, player_id as id, signed_date, count(goal_id) as goals  from player left join teams t on t.team_id=player.FK_team_id left join goal g on g.FK_player_id=player.player_id where FK_team_id='" + req.params.team_id + "'");
                        connection.query("select concat(first_name, ' ', last_name) as name, FK_position as  position, player_id as id, signed_date, count(goal_id) as goals  from player left join goal g on g.FK_player_id=player.player_id where t.name='" + req.params.team_id + "'", function(err, result){
                            if(result.length>0){
                                
                                res.json({"result": "success", "messages": result[0]});   
                            }else{
                                res.json({"error": "User was not found"});
                            }    
                            
                        });
                        
             }
            });
            
            
    })

app.listen(port)
