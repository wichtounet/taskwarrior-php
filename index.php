<h1>My tasks</h1>

<table>
	<tr>
		<td><strong>Project</strong></td>
		<td><strong>Description</strong></td>
	</tr>

<?php
	include "config.php";
	include "Task.php";

	//Open the pending tasks
	$file_handle = fopen($PENDING_DATA_PATH, "r");
	
	//Parse all lines
	while (!feof($file_handle)) {
		$line = fgets($file_handle);
		$part = substr($line, 1, -2);
		$parts = explode("\"", $part);

		$task = new Task();
		$task->project = $parts[5];
		$task->description = $parts[1];
		
		$tasks[] = $task;
	}
	
	//Close the file
	fclose($file_handle);
	
	function cmp_task($a, $b)
	{
		return strcmp($a->project, $b->project);
	}

	usort($tasks, "cmp_task");
	
	//Output all tasks
	foreach($tasks as $task){
		echo "<tr>";
		
		echo "<td>" . $task->project . "</td>";
		echo "<td>" . $task->description . "</td>";

		echo "</tr>";
	}
?>

</table>