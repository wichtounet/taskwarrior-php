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
        page_header($title);
		table_header();
		
		//Output all tasks
		foreach($tasks as $task){
			echo "<tr>";
			echo "<td>" . $task->project . "</td>";
			echo "<td>" . $task->description . "</td>";
			echo "</tr>";
		}
		
		table_footer();
	}

    function display_by_projects(&$tasks, $title){
        page_header($title);

		$project = "";
        $first = 0;
        
        foreach($tasks as $task){
            if($task->project == ""){
                $no_project[] = $task;

                continue;
            }

            if($task->project != $project){
                if($first == 1){
                    echo "</ul>";
                }

                if($first == 0){
                    $first = 1;
                }

                echo "<h2>" . $task->project . "</h2>";
                echo "<ul>";

                $project = $task->project;
            }

			echo "<li>" . $task->description . "</li>";
		}

        if(count($no_projects) > 0){
            echo "<h2>" . $task->project . "</h2>";
           
            echo "<ul>";

            foreach($no_project as $task){
                echo "<li>" . $task->description . "</li>";
            }

            echo "</ul>";
        }
    }

    function page_header($title){
		echo "<h1>" . $title . "</h1>";
    }
	
	function table_header(){
		echo "<table>
				<tr>
					<td><strong>Project</strong></td>
					<td><strong>Description</strong></td>
				</tr>";
	}
	
	function table_footer(){
		echo "</table>";
	}
?>
