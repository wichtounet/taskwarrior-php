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

        for($i = 0; $i < sizeof($parts); $i += 2){
            $key = $parts[$i];
            $value = $parts[$i+1];

            $key = trim($key);
            $value = trim($value);

            switch($key){
                case "description:":
                  $task->description = $value;
                  break;
                case "project:":
                  $task->project = $value;
                  break;
                case "entry:":
                  $task->entry = $value;
                  break;
                case "uuid:":
                   $task->uuid = $value;
                   break;
            }
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
    $(function() {
            $( \".button\", \".body\" ).button();
            });
    </script>";

    echo "<div id=\"accordion\">";
}

function accordion_footer(){
    echo "</div>";
}

function display_task($task){
    echo 
        "<li>" . $task->description . ", age = " . task_age($task) . 
        "&nbsp;<small><a class=\"button\" href=\"?action=done&uuid=" . $task->uuid . "\">Done</a></small>" . 
        "&nbsp;<small><a class=\"button\" href=\"?action=delete&uuid=" . $task->uuid . "\">Delete</a></small>" . 
        "</li>";
}

function section_header($title, $completion = -1){
    echo "<h3><a href='#'>" . $title;
    if($completion > -1){
        echo " (Completed: " . $completion  . "%)";
    }
    echo "</a></h3>";
    echo "<div>";
    echo "<ul>";
}

function section_footer(){
    echo "</ul>";
    echo "</div>";
}

function display_by_projects(&$pending, &$completed, $title){
    page_header($title);
    accordion_header();

    $project = "";
    $first = 0;

    foreach($pending as $task){
        if($task->project == ""){
            $no_project[] = $task;

            continue;
        }

        if($task->project != $project){
            if($first == 1){
                section_footer();
            }

            if($first == 0){
                $first = 1;
            }

            section_header($task->project, project_completion($task->project, $pending, $completed));

            $project = $task->project;
        }

        display_task($task);
    }

    if(count($no_projects) > 0){
        section_header("No projects");

        foreach($no_project as $task){
            display_task($task);
        }

        section_footer();
    }

    accordion_footer();
}

function page_header($title){
    echo "<div>";
    echo "<h1 style=\"text-align:left;float:left;\">" . $title . "</h1>";
    echo "<div style=\"padding-top:20px;\">";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;<small><a class=\"button\" href=\"insert.php\">New task</a></small>";
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


function delete_task($uuid){
    global $PENDING_DATA_PATH;

    $pending = parse_tasks($PENDING_DATA_PATH);
    
    $fd = fopen($PENDING_DATA_PATH, 'w') or die("Can't open file");
   
    for($i = 0; $i < sizeof($pending); $i++){
        $task = $pending[$i];

        if($task->uuid != $uuid){
            $content = "\n[description:\"" . $task->description . "\" entry:\"" . $task->entry . "\" project:\"" . $task->project . "\" status:\"pending\" uuid:\"" . $task->uuid . "\"]";
            fwrite($fd, $content);
        }
    }

    fclose($fd);
}

function done_task($uuid){
    global $PENDING_DATA_PATH;
    global $COMPLETED_DATA_PATH;
    
    $pending = parse_tasks($PENDING_DATA_PATH);
    $completed = parse_tasks($COMPLETED_DATA_PATH);
    
    $fd = fopen($PENDING_DATA_PATH, 'w') or die("Can't open file");
   
    for($i = 0; $i < sizeof($pending); $i++){
        $task = $pending[$i];

        if($task->uuid != $uuid){
            $content = "\n[description:\"" . $task->description . "\" entry:\"" . $task->entry . "\" project:\"" . $task->project . "\" status:\"pending\" uuid:\"" . $task->uuid . "\"]";
            fwrite($fd, $content);
        } else {
            $removed_task = $task;
        }
    }

    fclose($fd);

    $content = "\n[description:\"" . $removed_task->description . "\" entry:\"" . $removed_task->entry . "\" end:\"" . time() . "\" project:\"" . $removed_task->project . "\" status:\"completed\" uuid:\"" . $uuid . "\"]";

    file_put_contents($COMPLETED_DATA_PATH, $content, FILE_APPEND | LOCK_EX);
}

?>
