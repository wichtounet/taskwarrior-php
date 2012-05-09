<h1>My tasks</h1>

<table>
	<tr>
		<td><strong>Project</strong></td>
		<td><strong>Description</strong></td>
	</tr>

<?php
	include "config.php";

	//Open the pending tasks
	$file_handle = fopen($PENDING_DATA_PATH, "r");
	
	//Output all lines
	while (!feof($file_handle)) {
		echo "<tr>";

		$line = fgets($file_handle);
		$part = substr($line, 1, -2);
		$parts = explode("\"", $part);

		echo "<td>" . $parts[5] . "</td>";
		echo "<td>" . $parts[1] . "</td>";

		echo "</tr>";
	}
	
	//Close the file
	fclose($file_handle);
?>

</table>