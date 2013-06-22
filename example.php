<?php
/**
 * Created by Ascendro S.R.L.
 * User: Michael
 * Date: 19.06.13
 * Time: 08:54
 */

include "PBOExtractor.php";

$now = microtime(true);
printf("MEM:  %d<br/>\nPEAK: %d<br/>\n", memory_get_usage(), memory_get_peak_usage());

$missionSQMFile = PBOExtractor::extract("mission.sqm","./test/Arma2Mission.pbo");
$missionSQMFile = PBOExtractor::extract("mission.sqm","./test/ArmaMission.pbo");
$missionSQMFile = PBOExtractor::extract("mission.sqm","./test/Arma3Mission.pbo");
$missionSQMFile = PBOExtractor::extract("mission.sqm","./test/Arma2OAMission.pbo");

var_dump($missionSQMFile);
var_dump(stream_get_contents($missionSQMFile->content));

$then = microtime(true);
$time = $then-$now;
printf("MEM:  %d<br/>\nPEAK: %d<br/>\n", memory_get_usage(), memory_get_peak_usage());
echo "Total Elapsed: ".$time." seconds<br/>\n";
