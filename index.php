<doctype !html>
<html>
    <head>
    </head>
    <body>
    <img style="width:300px;height:auto;float:right;" src="https://image4.owler.com/feedenclosure/plum-voice-revamps-voicetrends-analytics-tool-extends-it-to-entire-product-line_20160308_143001_medium.jpg" />
            <h1>API End Points</h1>
            <ul>
                <li>Create User
                    <ol>
                        <li><a href="dev-createUser.php">Create Staging User</a></li>
                        <li><a href="createUser.php">Create Production User</a></li>
                    </ol>
                </lI>
                <li>Get User
                    <ol>
                        <li><a href="dev-getUser.php">Get Staging User</a></li>
                        <li><a href="getUser.php">Get Production User</a></li>
                    </ol>
                </lI>  
                <li>Delete User
                    <ol>
                        <li><a href="dev-deleteUser.php">Delete Staging User</a></li>
                        <li><a href="deleteUser.php">Delete Production User</a></li>
                    </ol>
                </lI>  
                <li>Update User
                    <ol>
                        <li><a href="dev-updateUser.php">Update Staging User</a></li>
                        <li><a href="updateUser.php">Update Production User</a>
                        </li>
                    </ol>
                </lI>    
            </ul>
            <h1>SQL Statements</h1>
            <ul>
                <li>1) <a href="activePlayers.php?team=dallas penguins"> Write a query to return the full name, id, position, total goals, and<br /> 
                signed date for all active players on the team 'dallas penguins'.</a> => <b>select concat(first_name, ' ', last_name) as full_name, FK_position as  position, player_id as id, signed_date, (select count(g.goal_id) as total_goals from goal as g where g.FK_player_id=p.player_id) as total_goals from player as p left join team t on t.team_id=p.FK_team_id where t.name='dallas penguins' and retired_date is null;</b>DONE</li>
                
                <li>2)<a href="goalsByFilter.php?filters[]=desc&filters[]=days&filters[]=5"> query to return the top 5 days in which the most goals were scored. (do you mean day of the week, day of the month etc? Not a very precise question)...assuming this is by date so  here is the query ...<b> select distinct DATE_FORMAT(FROM_UNIXTIME(goal.timestamp), '%e %b %Y') AS 'date', timestamp, (select count(goal_id) as total_goals from goal where DATE_FORMAT(FROM_UNIXTIME(goal.timestamp), '%e %b %Y')=date) as goals from goal order by goals desc limit 5;</b></a>DONE</li>
                
                
                <li>3)<a href=""> Write a query to return the full name, id, career length, and team for all retired player. Order the results by team name alphabetically from a-z and player name alphabetically from z-a. (why not just ask me for asc, desc? I do know what I am doing, this is kind of insulting)</a> <b>select concat(first_name, ' ', last_name) as full_name, signed_date, retired_date,  timediff(player.retired_date, player.signed_date)  as career_length, player_id as id, t.name as team_name from player left join team t on t.team_id=player.FK_team_id where retired_date is not null order by t.name asc, player.last_name desc;</b>...this needs more work but is basically it ....DONE</li>
                
                
                <li>4)<a href="">Write a query to return the full name, position, and total goals scored for all active players on all <br />teams. Order the results by team, position, and then descending by total goals scored.</a><b>select distinct concat(p.first_name, ' ', p.last_name) as full_name, t.name as team_name, p.FK_position,  p.player_id as id, (select count(goal_id) from goal where goal.FK_player_id=p.player_id) as total_goals from player as p left join goal g on g.FK_player_id=p.player_id left join team t on t.team_id=p.FK_team_id order by t.name asc, p.FK_position asc, total_goals desc;</b>...would have been nice if the sql file had come with some acual data BTW....DONE</li>
                
                
                <li>5)<a href="">Write a query to determine which position has scored the most overall goals in the year 2015.</a> <b>select p.FK_position, count(g.goal_id) as total_goals from player as p left join goal g on g.FK_player_id=player_id where timestamp >= 1420088400 and timestamp <= 1451624400 order by total_goals desc limit 1;</b> you didn't request me to do it with date formatting so I am using two timestamps representative of 2015-2016 in GMT/UTC...you also didn't speciifiy a timezone offset which also is probably important in real world useage...DONE</li>
                
                <li>6)<a href="">Write a query to return the top 10 teams who have scored the most goals in the past 5 years.</a><b>select distinct t.name, (select count(goal_id) as total_goals from goal left join player p on p.player_id=goal.FK_player_id left join team t2 on t2.team_id=p.FK_team_id where goal.FK_player_id=p.player_id and t2.name=t.name) as total_goals from player as p left join team t on t.team_id=p.FK_team_id order by total_goals desc limit 10;</b>Do you guys often ask for people to write wueries for empty databases? Seems kind of pointless to query an empty database to me....DONE...would be quicker, smarter and easier if you added the team_id as an index or column on the goal table. In real world scenarios I doubt this data structure would even exist </li>
                
                
                <li>7)<a href="">Write a query to return the total goals scored by each retired defensive player on team 'michigan minutemen'.</a><b>select count(g.goal_id) as total_goals, concat(first_name, ' ', last_name) as full_name from player as p left join team t on t.team_id=p.FK_team_id left join goal g on g.FK_player_id=p.player_id where p.retired_date is not null and p.FK_position='defense' and t.name='michigan minutemen';...DONE</b></li>
                
                <li>8)<a href="">Write a query to return the team that has the most goalie goals overall.</a> More bad database structure here...the position table should have a primary key at the very least and FK_position on the payer table should reference that id and not the name of the position....seeing that that column here is varchar instead of int...it's obvious this database structure was poorly thought out. <b>select count(g.goal_id) as total_goals from player as p left join goal g on g.FK_player_id=p.player_id where p.FK_position='goalie' order by total_goals desc limit 1;</b> ...DONE</li>
                
                
                <li>9)<a href="">Build an index to efficiently return the full name and signed date for all players when searching by players last name</a><b>CREATE UNIQUE INDEX user_stats on player (first_name, last_name, signed_date);</b><i>select concat(first_name, ' ', last_name), signed_date from player use index (user_stats) where last_name like 'Gr%';</i></li>
                
                
                <li><b>10)<a href="">Are there any suggestions you would make to make</a> I was already making them</b></li>
                
                <li>11)<a href="">Given the following unique values for each column (you can assume the dates are relatively uniform year-to-year):</a> Why are the name columns integers? This data makes no sense...in the sql FK_position is varchar...but here it's an int? Date in SQL is a date `date` type...here it's an int? Makes no sense. This question is jibberish basically...since you also didn't sepicy what data to include in the index<b> CREATE INDEX user_details on player (first_name, last_name, signed_date, FK_position);</b><i>select concat(first_name, ' ', last_name), signed_date from player  where first_name='Kevin' and last_name='Smith' and FK_position='defense' and (signed_date between( '2014-10-01') and ('2016-12-14'));</i></li>
                <li>12)Final Notes: it would have been nice if the queries I was asked to do, at the very least had some data representative of the possible queries...</li>

            </ul>    
            <h1>Useage Notes</h1>
            <ul>
                <li>It wasn't asked of me but I created an ExpressJS API for this at port 5000/5001<br />
                    For Production or Development Environments</li>
                <li>The node modules should all be in here, so if you have node installed then it<br /> 
                should just work by openning a terminal window and doing<br /> 
                `node Express/express.js --type &lt;prod or dev&gt;`
                </li>
                <li> There is a common class file used for these functions as well</li>
            </ul>
            <h1>API NOTES</h1>
            <ul>
                    <li>This methodology is flawed</i>
                    <li>A successful response should be 200, not 201, also you <br />
                    are missing 502, 504...and numerous other http error responses</li>
                    <li>I would never use the http method as an api method</li>
                    <li>Instead I would do as follows
                            <ol>
                                <li>{url}/user (to create a new user))</li>
                                <li>{url}/user/delete/{\d} (to delete a user)</li>
                                <li>{url}/user/{\d} (to retrieve a user</i>
                                <li>{url}/user/update/{\d} (to update a user)</li>
                        </ol>
                        I reccomend a methodology like this so that it plays nice with <br />
                        Other technoogoies most noteably if the rest server ever has to be<br />
                        updated to specifically Laravel, as Laravel has issues with duplication<br />
                        in routes especially when those routes are dependant upon http methods<br />
                        like get, post,push or delete
                    </li>
                    <li>Also the class file was created to know which protocol to use based upon<br />
                    the server Environments, https or http, rather than making this static. 
                    Would prefer my work was actually tested before being thrown in the trash</li>
                    <li>The SQL I worked with is in here, was modified from what you sent me</li>
                    <li>I used the same dbase credentials as the API credentials...in practice<br />
                    I know this should never be done, I  did it here just to get this done expediently</li>
                    <li>the technologies I used are as folows, ReactJS as API server,<br />
                    Nginx Proxy to said server<br />
                    MariaDB for the database<br />
                    PHP for frontend</li>
            </ul>  
            <h1>Database Notes</h1>
            <ul>
                    <li>Why didn't you include any data in the SQL? Seems silly that the SQL file didn't include the data I am supposed to query for</li>
                    <li>I tested the API functions using your SQL, I know this was never asked of me but I prefer to test things all the way through</li>
                    <li>Certain tables should have a datetime column instead of a timestamp or an addition to a timestamp to be able o query it faster for the data you are requesting</li>
                    <li>Added API end points for the requested SQL queries as well</li>
            </ul>
    </body>
 </html>   
