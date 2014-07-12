<?php
// PRINT "Elapsed time was $time seconds.";
//echo '<script>window.open("http://127.0.0.1:1913/rssget/haivltv", "_blank", "width=400,height=500")</script>';

$starttime = time();
$endtime = date("Y-m-d", mktime(0,0,0,date("n", $time),date("j",$time)+ 1 ,date("Y", $time)));
// $totaltime = ($endtime - $starttime); 
// echo "This page was created in ".$totaltime." seconds"; 