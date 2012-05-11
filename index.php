<?php
	require_once("config.php");
	require_once("Task.php");
	require_once("functions.php");

	if(isset($_GET["action"])){
		if($_GET["action"] == "insert"){
			$description = $_GET["description"];
			$project = $_GET["project"];
			
			$pending = parse_tasks($PENDING_DATA_PATH, 0);
			create_task($description, $project, $pending, $PENDING_DATA_PATH);
		}
	}
?>

<html>
<body>

<?php
	$pending = parse_tasks($PENDING_DATA_PATH, 0);
	$completed = parse_tasks($COMPLETED_DATA_PATH, 1);
	
	sort_tasks($pending);
	sort_tasks($completed);
	
	display_by_projects($pending, $completed, $TITLE);
?>

</body>
</html>