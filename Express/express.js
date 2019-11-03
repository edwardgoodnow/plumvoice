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
var port = (pre == 'https://staging-')?8080:80;
var server = pre + 'api.example.com';
var APIUSERNAME = (pre == 'https://staging-')?'devUsername':'Username';
var APIPASSWORD = (pre == 'https://staging-')?'$*rs9D(':'&&KeXt97&sd';
    //these are set by the CLI switch above...but can be modified if need be as well, let's keep our environment somewhat flexible and upgradable

var dbuser = APIUSERNAME;
var dbpass = APIPASSWORD;
    //I know these should be different for security reasons....but for development purposes I did it this way so everyone could follow what I did here
var dbname = (pre == 'https://staging-')?'dev_plumvoice':'plumvoice';
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


var moment = require('moment');
var net = require('net');

var mysql = require('mysql');

var exec = require('child_process').exec,
			    child;

 
    var mysql_options =  {
      host     : 'localhost',
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

    pool.getConnection(function(dberr, connection) {
            if (dberr) {
                console.error('error connecting: ' + dberr.stack);
                return;
            }   
            app.route('/user').post(function(req, res){
                if(req.body.username != APIUSERNAME | req.body.password != APIPASSWORD){
                //return a 401 error here   
                    
                }
                if(valid_user(req) == true){
                    
                    
                }else{
                
                
                    
                }
            });

    })

app.listen(port)
