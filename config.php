<?php
//mysql data
$data = array('server' => 'localhost',
			  'user'   => 'root',
			  'pass'   => 'password',
			  'db' 	   => 'ghostlulz');


// login info
$username = "admin";
$password = "g.h.o.s.t.l.u.z";

mysql_connect($data['server'],$data['user'],$data['pass']);
mysql_select_db($data['db']);
?>