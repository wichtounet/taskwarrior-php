<?php
	function parse_tasks($file, $type){
		//Open the pending tasks
		$file_handle = fopen($file, "r");
		
		//Parse all lines
		while (!feof($file_handle)) {
			$line = fgets($file_handle);
			$part = substr($line, 1, -2);
			$parts = explode("\"", $part);

			$task = new Task();
			
            //For pending.data
            if($type == 0){
                $task->project = $parts[5];
                $task->description = $parts[1];
                $task->uuid = $parts[11];
                $task->entry = $parts[3];
            }
			
            //For completed.data
            if($type == 1){
                $task->project = $parts[7];
                $task->description = $parts[1];
                $task->uuid = $parts[11];
                $task->entry = $parts[5];
            }
			
			$tasks[] = $task;
		}
		
		//Close the file
		fclose($file_handle);
		
		return $tasks;
	}
		
    function cmp_task($a, $b){
        return strcmp($a->project, $b->project);
    }
	
	function sort_tasks(&$tasks){
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

    function accordion_header(){
        echo "<script>
            $(function() {
                $( \"#accordion\" ).accordion({
                    collapsible: true,
                    autoHeight: false
                });
            });
            </script>";
    }

    function display_by_projects(&$pending, &$completed, $title){
        page_header($title);
        accordion_header();

        echo "<div id=\"accordion\">";

		$project = "";
        $first = 0;
        
        foreach($pending as $task){
            if($task->project == ""){
                $no_project[] = $task;

                continue;
            }

            if($task->project != $project){
                if($first == 1){
                    echo "</ul>";
                    echo "</div>";
                }

                if($first == 0){
                    $first = 1;
                }

                echo "<h3><a href='#'>" . $task->project . " (Completed: " . project_completion($task->project, $pending, $completed)  . "%)</a></h3>";
                echo "<div>";
                echo "<ul>";

                $project = $task->project;
            }

			echo "<li>" . $task->description . ", age = " . task_age($task)  . "&nbsp;<a href=\"?action=delete&uuid=" . $task->uuid . "\">done</a>&nbsp;</li>";
		}

        if(count($no_projects) > 0){
            echo "<h3><a href='#'>" . $task->project . "</a></h3>";
           
            echo "<div>";
            echo "<ul>";

            foreach($no_project as $task){
                echo "<li>" . $task->description . "</li>";
            }

            echo "</ul>";
            echo "</div>";
        }

        echo "</div>";
    }

    function page_header($title){
        echo "<div>";
            echo "<h1 style=\"text-align:left;float:left;\">" . $title . "</h1>";
            echo "<div style=\"padding-top:35px\">";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"insert.php\">New task</a>";
            echo "</div>";
        echo "</div>";
        echo "<hr style=\"clear:both;\"/>";
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

    function task_age($task){
        $current = time();
        $entry = intval($task->entry);
        $diff = $current - $entry;

        if($diff < 60){
            return $diff . " seconds";
        }

        $diff = floor($diff / 60);

        if($diff < 60){
            return $diff . " minutes";
        }
        
        $diff = floor($diff / 60);

        if($diff < 24){
            return $diff . " hours";
        }
        
        $diff = floor($diff / 24);
        
        return $diff . " days";
    }

    function project_completion($project, &$pending, &$completed){
        $pending_cnt = 0.0;
        $completed_cnt = 0.0;

        foreach($pending as $task){
            if($task->project == $project){
                $pending_cnt += 1;
            }
        }

        foreach($completed as $task){
            if($task->project == $project){
                $completed_cnt += 1;
            }
        }

        return 100 * round($completed_cnt / ($pending_cnt + $completed_cnt), 2);
    }
	
	function GUID(){
		if (function_exists('com_create_guid') === true){
			return trim(com_create_guid(), '{}');
		}

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}
	
	function uuid(){
		return strtolower(GUID());
	}
	
	function uuid_exists($uuid, &$pending){
		foreach($pending as $task){
            if($task->uuid == $uuid){
                return true;
            }
        }
		
		return false;
	}

    function iso8601_date(){
		$date = new DateTime();
		$entry = $date->format(DateTime::ISO8601);
		
		$entry = str_replace("-", "", $entry);
		$entry = str_replace(":", "", $entry);
		$entry = substr($entry, 0, -4);
		$entry = $entry . "Z";

        return $entry;
    }
	
	function create_task($description, $project, &$tasks, $file){
		$id = uuid();
		
		while(uuid_exists($id, $tasks)){
			$id = uuid();
		}
	
        $entry = time();
		
		$task = "\n[description:\"" . $description . "\" entry:\"" . $entry . "\" project:\"" . $project . "\" status:\"pending\" uuid:\"" . $id . "\"]";
		
		file_put_contents($file, $task, FILE_APPEND | LOCK_EX);
	}
?>
