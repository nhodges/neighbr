<?php

	class inursql {

		function connect($hostname, $username, $password, $database) {

			global $hostname, $username, $password, $database;

			$c = mysql_connect($hostname, $username, $password) or die(mysql_error());
			mysql_select_db($database, $c);

			return $c;

		}

		function query($sql) {

			$query = mysql_query($sql) or die(mysql_error());

			return $query;

		}

		function grab($query) {

			$array = mysql_fetch_assoc($query);

			return $array;

		}

		function pick($query) {

			$array = mysql_fetch_row($query);

			return $rows;

		}

	}

?>