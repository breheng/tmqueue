<?php
$json_url = "http://itnthackathon.bweas.tm.com.my/api/getTMPointInfo";
$json = file_get_contents($json_url);
$data = json_decode($json, TRUE);
echo "<pre>";
print_r($data);
echo "</pre>";
?>
