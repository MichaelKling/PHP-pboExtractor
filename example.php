<?php
/**
 * Created by Ascendro S.R.L.
 * User: Michael
 * Date: 19.06.13
 * Time: 08:54
 */

include "PBOExtractor.php";


$missionSQMFile = PBOExtractor::extract("mission.sqm","./test/Arma2Mission.pbo");
var_dump($missionSQMFile);
$missionSQMFile = PBOExtractor::extract("mission.sqm","./test/ArmaMission.pbo");
var_dump($missionSQMFile);
$missionSQMFile = PBOExtractor::extract("mission.sqm","./test/Arma3Mission.pbo");
var_dump($missionSQMFile);
$missionSQMFile = PBOExtractor::extract("mission.sqm","./test/Arma2OAMission.pbo");
var_dump($missionSQMFile);

