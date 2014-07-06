<?php
// Generate usage report from PaperCut database.

$link = new mysqli('database', 'papercut', 'papercut', 'papercut');

// per month total count

$printer_names = array();
$printer_sum_page = array ();
$printer_sum_sheet = array ();
$onemonth_page = array();
$onemonth_sheet = array();

$sql1 = 'SELECT printer_id,printer_name FROM tbl_printer WHERE deleted=\'N\' '.
     ' AND printer_id > 1001 ORDER BY printer_id ASC';
$res1 = $link->query($sql1);

echo "<table border=1>\n<tr><th>Month</th>";
while ($p = $res1->fetch_assoc())
{
  $id = $p['printer_id'];
  $printer_names["$id"] = $p['printer_name'];
  echo "<th>".$p['printer_name']."</th>";
  $printer_sum_page["$id"] = 0; $printer_sum_sheet["$id"] = 0;
}
echo "</tr>\n";

$sql2 = "SELECT YEAR(usage_date) AS year,MONTH(usage_date) AS month, ".
 "tbl_printer.printer_id AS id, printer_name AS name, ".
 "SUM(tbl_printer_usage_log.total_pages) AS pages, ".
 "SUM(tbl_printer_usage_log.total_sheets) AS sheets ".
 "FROM `tbl_printer_usage_log` ".
 "JOIN `tbl_printer` ON tbl_printer_usage_log.printer_id = tbl_printer.printer_id ".
 "WHERE deleted='N' AND printed='Y' AND tbl_printer.printer_id>1001 ".
 "GROUP BY YEAR(usage_date) ASC, MONTH(usage_date) ASC, tbl_printer_usage_log.printer_id ASC";

$res2 = $link->query($sql2);

foreach ($printer_names as $i => $n)
{
  $onemonth_page["$i"] = 0; $onemonth_sheet["$i"] = 0;
 }
$cur_year = 0; $cur_month = 0;
$p_year = 0; $p_month = 0;
while ($e = $res2->fetch_assoc())
{
  if ($e['year'] != $cur_year)
  {
   $p_year = $cur_year;
   $p_month = $cur_month;
   $cur_year = $e['year'];
   $cur_month = 0;
  }
  if ($e['month'] != $cur_month)
  {
   echo "<tr><td>".$p_year." ".$p_month."</td>";
   foreach ($printer_names as $i => $n)
   {
     echo "<td>".$onemonth_page["$i"]."|".$onemonth_sheet["$i"]."</td>";
     $onemonth_page["$i"] = 0; $onemonth_sheet["$i"] = 0;
   }
   $p_year = $cur_year;
   echo "</tr>\n";
   $cur_month = $e['month'];
   $p_month = $cur_month;
  }

  $id = $e['id'];
  $onemonth_page["$id"] = $e['pages'];
  $printer_sum_page["$id"] += $e['pages'];
  $onemonth_sheet["$id"] = $e['sheets'];
  $printer_sum_sheet["$id"] += $e['sheets'];
}
   echo "<tr><td>".$p_year." ".$p_month."</td>";
   foreach ($printer_names as $i => $n)
   {
     echo "<td>".$onemonth_page["$i"]."|".$onemonth_sheet["$i"]."</td>";
     $onemonth_page["$i"] = 0; $onemonth_sheet["$i"] = 0;
   }

echo "<tr><td>Sum</td>";
foreach ($printer_names as $i => $n)
{
  echo "<td>".$printer_sum_page["$i"]."|".$printer_sum_sheet["$i"]."</td>";
}
echo "</tr>\n";
echo '</table>'."\n";
$link->close();
?>
