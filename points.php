<?php

function count_points($duration, $techprofile) {
    switch($techprofile) {
        // workshop
        case 0:
            $time_units = (int)floor($duration/HELPDESK_POINTS_WORKSHOP_PERIOD);
            return $time_units * HELPDESK_POINTS_WORKSHOP_PER_PERIOD;
            break;
        // tech
        case 1:
            $time_units = (int)floor($duration/HELPDESK_POINTS_TECH_PERIOD);
            return $time_units * HELPDESK_POINTS_TECH_PER_PERIOD;
            break;
        //engineer
        case 2:
            $time_units = (int)floor($duration/HELPDESK_POINTS_ENGINEER_PERIOD);
            return $time_units * HELPDESK_POINTS_ENGINEER_PER_PERIOD;
            break;
    }
}

function contract_list_for_company($db,$company) {
    $sql="select d.rowid, c.entity, DATE(d.date_ouverture_prevue) AS dateo, DATE(d.date_fin_validite) AS datef, e.pinit, e.pcur, p.label ";
    $sql.=" from llx_contrat as c, llx_contratdet as d, llx_contratdet_extrafields as e, llx_product as p ";
    $sql.=" where e.ctype=1 ";
    $sql.=" and c.fk_soc=".$company;
    $sql.=" and d.fk_product=p.rowid";
    $sql.=" and c.rowid=d.fk_contrat";
    $sql.=" and d.rowid=e.fk_object;";
    dol_syslog($message, LOG_DEBUG);
    $result = $db->query($sql);
    $num = $db->num_rows($result);
    $i = 0;
    $ret=[];
    while ($i < $num)
    {
        $obj = $db->fetch_object($result);
        $ret[]=$obj; 
        $i++;
    }
    return $ret;
}

function update_contract_points($db, $contractid, $number) {
    $sql="select pcur from llx_contratdet_extrafields where fk_object=".$contractid.";";
    $result = $db->query($sql);
    $obj = $db->fetch_object($result);
    $newval=$obj->pcur - $number;
    $sql="update llx_contratdet_extrafields set pcur=".$newval." where fk_object=".$contractid.";";
    $result = $db->query($sql);
}




?>
