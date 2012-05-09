<?php
	function parse_tasks($file){
		//Open the pending tasks
		$file_handle = fopen($file, "r");
		
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
		
		return $tasks;
	}
	
	function sort_tasks(&$tasks){
		function cmp_task($a, $b){
			return strcmp($a->project, $b->project);
		}
		
		usort($tasks, "cmp_task");
	}
	
	function display_as_table(&$tasks, $title){
		echo table_header($title);
		
		//Output all tasks
		foreach($tasks as $task){
			echo "<tr>";
			echo "<td>" . $task->project . "</td>";
			echo "<td>" . $task->description . "</td>";
			echo "</tr>";
		}
		
		echo table_footer();
	}
	
	function table_header($title){
		return "<h1>" . $title . "</h1>". 
			"<table>
				<tr>
					<td><strong>Project</strong></td>
					<td><strong>Description</strong></td>
				</tr>";
	}
	
	function table_footer(){
		return "</table>";
	}
?>