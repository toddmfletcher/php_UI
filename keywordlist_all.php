<?php include ("dialdb_select.php");?>
<?php

$keywords = array();
$index = 0;


//execute the SQL query and return records
//$result = mysql_query("SELECT * FROM tblPatternMetadata");

$querykey = "SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(t.sKeywords, ',', n.n), ',', -1) tags, count(*) as counts FROM vwPattern_copy t CROSS JOIN (SELECT a.N + b.N * 10 + 1 n FROM (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a,(SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b ORDER BY n) n WHERE n.n <= 1 + (LENGTH(t.sKeywords) - LENGTH(REPLACE(t.sKeywords, ',', ''))) group by tags order by tags";

$keywords = range('A','Z');
$keywords[] = '#';

//execute the SQL query and return records
if (!$result = $db->query($querykey)){
	die('There was an error running the query [' . $db->error . ']');
}

while ($row = $result->fetch_assoc()) {
	
	foreach ($keywords as &$letter) {
	
				if ($letter == strtoupper(substr($row['tags'],0,1))) {
					$keywords[$letter][] = $row;
				}
		
	}
}
	
/*	if (!is_numeric(substr($row['tags'],0,2)) and $row['tags'] != ''){
		$keywords[] = $row;
	}*/

echo "<div class='css-treeview'><ul>";
//array_multisort($keywords[0]);
foreach ($keywords as $categorykey => $singlecategory) {
		//if (!is_numeric($categorykey)) {
			if (strtoupper(substr($_GET['findkey'],0,1)) == strtoupper($categorykey)) {
				echo "<li><input type='checkbox' id='item-" . $categorykey . "' checked/><label for='item-" . $categorykey . "'>" . $categorykey . " </label><ul>";
			} else {
				echo "<li><input type='checkbox' id='item-" . $categorykey . "' /><label for='item-" . $categorykey . "'>" . $categorykey . " </label><ul>";
			}
			foreach ($singlecategory as $categoryentry) {
					
					echo "<li><a href='javascript:void(0)' onclick=UpdateQueryString('findkey','" . str_replace(" ", "%20", $categoryentry['tags']) . "')>" . strtolower($categoryentry['tags']) . " (" . $categoryentry['counts'] . ")</a></li>";
				
			}
			echo "</ul></li>";
		//}
}

echo "</ul></div>";

//print_r($keywords);
//close the connection
mysql_close($dbhandle);
?>
