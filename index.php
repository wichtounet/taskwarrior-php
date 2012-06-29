<?php
	include("config.php");
	include("Task.php");
	include("functions.php");

	if(isset($_GET["action"])){
		if($_GET["action"] == "insert"){
			$description = $_GET["description"];
			$project = $_GET["project"];
			
			$pending = parse_tasks($PENDING_DATA_PATH);
			create_task($description, $project, $pending, $PENDING_DATA_PATH);
		} else if($_GET["action"] == "delete"){
            $uuid = $_GET["uuid"];

            delete_task($uuid);
		} else if($_GET["action"] == "done"){
            $uuid = $_GET["uuid"];

            done_task($uuid);
        }
	}
?>

<html>
    <head>
        <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
        <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
    </head>
    <body>
        <div class="body">
            <?php
                $pending = parse_tasks($PENDING_DATA_PATH);
                $completed = parse_tasks($COMPLETED_DATA_PATH);
                
                sort_tasks($pending);
                sort_tasks($completed);
                
                display_by_projects($pending, $completed, $TITLE);
            ?>
        </div>
    </body>
</html>
