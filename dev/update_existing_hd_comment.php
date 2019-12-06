<?php

include('points.php');
define('HELPDESK_POINTS_WORKSHOP_PER_PERIOD',1);
define('HELPDESK_POINTS_WORKSHOP_PERIOD',10);
define('HELPDESK_POINTS_TECH_PER_PERIOD',3);
define('HELPDESK_POINTS_TECH_PERIOD',20);
define('HELPDESK_POINTS_ENGINEER_PER_PERIOD',2);
define('HELPDESK_POINTS_ENGINEER_PERIOD',10);

$commentid=$argv[1];
$tc=$argv[2];
$tp=$argv[3];

$points=count_points($tc, $tp);
echo "UPDATE llx_helpdesk_comment set points=".$points." where rowid=".$commentid.";\n";

?>
