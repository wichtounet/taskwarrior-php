<?php
	require_once("config.php");
	require_once("Task.php");
	require_once("functions.php");

	$pending = parse_tasks($PENDING_DATA_PATH);
	$completed = parse_tasks($COMPLETED_DATA_PATH);
	
	sort_tasks($pending);
	sort_tasks($completed);
	
	display_by_projects($pending, $TITLE);
?>
