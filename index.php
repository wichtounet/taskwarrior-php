<?php
	require_once("config.php");
	require_once("Task.php");
	require_once("functions.php");

	$tasks = parse_tasks($PENDING_DATA_PATH);
	
	sort_tasks($tasks);
	
	display_by_projects($tasks, $TITLE);
?>
