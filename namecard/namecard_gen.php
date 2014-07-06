<?php
/*
 Name Card Generator
*/
 function print_guide()
 {
?>
<div class="no_print">
<ul>
 <caption>操作要領：</caption>
 <li>人名一行、單位一行，每人兩行，不能多也不能少，可以空白。</li>
 <li>每一次顯示八張、大概剛好一頁A4，手動列印頁面。</li>
 <li>用Prev/Next前後捲動。</li>
 <li>按鈕和提示文字不會印出來。</li>
 <li>印之前要開啟列印背景 (大概都在設定列印格式或列印選項之類的地方)。</li>
 <li>預覽列印不會有背景。</li>
 <li>建議紙張組態：A4、直印(Portrait)、上下邊界各 0.02 公尺。</li>
</ul>
</div>
<?php
 }
 session_start();
 define ('CARDS_PER_PAGE', 8);
 $base = 0;
?>
<!doctype html>
<html lang="zh-tw">
<head>
<title>Name Card Generator</title>
<meta charset="utf-8">
</head>
<body>
<?php
 print_guide();
 if (isset($_POST['new_case']))
 {
  $_SESSION['host'] = $_POST['host'];
  $_SESSION['activity'] = $_POST['activity'];
  $tok = strtok($_POST['namelist'], "\n");
  $count = 0;
  $nl = array();
  while (false !== $tok)
  {
   $nl[$count]['name'] = $tok;
   $tok = strtok("\n");
   if ($tok === false)
   {
    die ('ERROR: Name - Unit number mismatch');
   }
   $nl[$count]['unit'] = $tok;
   $tok = strtok("\n");
   $count ++;
  }
  $_SESSION['namelist'] = $nl;
  $_SESSION['count'] = $count;
 }
 else if (isset($_POST['clear']))
 {
  unset($_SESSION['namelist']);
  unset($_SESSION['host']);
  unset($_SESSION['activity']);
  unset($_SESSION['count']);
 }
 if (isset($_SESSION['namelist']))
 {
  echo '<form class="no_print" action="namecard_gen.php" method="post">'."\n";
  echo '<input type="hidden" name="clear" value="1">'."\n";
  echo '<input type="submit" value="Clear"></form>'."\n";
  /* do nothing here, later in the body */
 }
 else
 {
?>
<form name="namelist" action="namecard_gen.php" method="post">
<input type="hidden" name="new_case" value="1">
Host:<br> <input name="host" type="text" size="40" value="Host"><br>
Activity:<br> <input name="activity" type="text" size="40" value="Activity"><br>
Namelist:<br>
<textarea name="namelist" rows="12" cols="60">
First_Name1 Last_Name1
Unit 1
First_Name2 Last_Name2
Unit 2
</textarea>
<br>
<input type="submit" value="Submit">
</form></body></html>
<?php
  exit();
 }
?>
<style type="text/css">
.namecard {
 float: left;
 width: 300px;
 height: 220px;
 padding: 1px;
 margin: 5px;
 border: 1px #e0e0e0 dashed
}

.nc_caption {
 background-color: #a68800;
 margin: 0;
}

.nc_activity{
 margin: 0;
 padding-top: 2px;
 padding-left: 2px;
 font-size: 18pt;
 color: white;
}

.nc_host {
 margin: 0;
 padding-top: 2px;
 padding-left: 2px;
 font-size: 14pt;
 color: #e0e0e0;
}

.nc_person {
 margin: 0;
 height: 125px;
 border-top: 12px solid #4d0000;
}

.nc_name {
 margin: 0;
 padding-top: 25px;
 padding-left: 15px;
 font-size: 15pt;
}

.nc_unit {
 margin: 0;
 padding-top: 10px;
 padding-left: 15px;
 font-size: 14pt;
}

.nc_padblock {
 float: left;
 margin: 5px;
 padding: 1px;
 border: 1px dashed red;
 height: 70px;
 width: 300px;
}

.nc_grouping {
 width: 630px;
 margin: 1px;
 clear: both;
}

@media print {
.no_print {
  display: none;
}
}

</style>
</head>
<body>
<?php
 if (isset($_SESSION['namelist']))
 {
  if (isset($_POST['base']))
  {
   $base = intval($_POST['base']);
  }
  echo '<div class="no_print">Current base/Total: '.$base.' / '.$_SESSION['count'];
  echo ' | CARDS_PER_PAGE: '.CARDS_PER_PAGE.'</div>';
  if ($base+CARDS_PER_PAGE < $_SESSION['count'])
  {
   echo '<form class="no_print" action="namecard_gen.php" method="post">'."\n";
   echo '<input type="hidden" name="base" value="'.($base+CARDS_PER_PAGE).'">'."\n";
   echo '<input type="submit" value="Next"></form>'."\n";
  }
  if ($base > 0)
  {
   if ($base >= CARDS_PER_PAGE) { $p_base = $base - CARDS_PER_PAGE; }
   else { $p_base = 0; }
   echo '<form class="no_print" action="namecard_gen.php" method="post">'."\n";
   echo '<input type="hidden" name="base" value="'.$p_base.'">'."\n";
   echo '<input type="submit" value="Prev"></form>'."\n";
  }
  echo '<div class="nc_grouping">'."\n";
  for ($i = $base;$i < $base+CARDS_PER_PAGE && $i < $_SESSION['count'];$i ++)
  {
?>
 <div class="namecard">
  <div class="nc_caption">
   <p class="nc_activity"><?php echo $_SESSION['activity']; ?></p>
   <p class="nc_host"><?php echo $_SESSION['host']; ?></p>
  </div>
  <div class="nc_person">
   <p class="nc_name"><?php echo $_SESSION['namelist'][$i]['name']; ?></p>
   <p class="nc_unit"><?php echo $_SESSION['namelist'][$i]['unit']; ?></p>
  </div>
 </div>
<?php
  }
  echo '</div><!--nc_grouping-->'."\n";
 }
?>
</body>
</html>
