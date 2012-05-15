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
    <head>
        <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.20.custom.css" rel="stylesheet" />
        <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
    </head>
    <body>
        <div class="body">
            <?php
                $pending = parse_tasks($PENDING_DATA_PATH, 0);
                $completed = parse_tasks($COMPLETED_DATA_PATH, 1);
                
                sort_tasks($pending);
                sort_tasks($completed);
                
                display_by_projects($pending, $completed, $TITLE);
            ?>
        </div>
    </body>
</html>
