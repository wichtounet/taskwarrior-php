<?php
	include "config.php";
	include "Task.php";
	include "functions.php";

	$tasks = parse_tasks($PENDING_DATA_PATH);
	
	sort_tasks($tasks);
	
	display_as_table($tasks);
?>
