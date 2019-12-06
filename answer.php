<?php

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/helpdesk/class/helpdeskticket.class.php';
require_once DOL_DOCUMENT_ROOT.'/helpdesk/class/helpdeskcomment.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formcontract.class.php';
require_once DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php';
require_once DOL_DOCUMENT_ROOT.'/helpdesk/points.php';

require_once DOL_DOCUMENT_ROOT.'/fichinter/class/fichinter.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/modules/fichinter/modules_fichinter.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/fichinter.lib.php';


$langs->load("helpdesk");
$langs->load("contracts");
$langs->load("companies");
$action=GETPOST('action','alpha');
$ticket=GETPOST('ticket','int');


//FIXME : Update pas optimal du tout!
if ($action == 'update') {
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set entity='.GETPOST('socid','int').' WHERE rowid='.$ticket.';';
    $db->query($sql);
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set createdby='.GETPOST('commercial_suivi_id','int').' WHERE rowid='.$ticket.';';
    $db->query($sql);
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set callerid='.GETPOST('contactid','int').' WHERE rowid='.$ticket.';';
    $db->query($sql);
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set state='.GETPOST('state','int').' WHERE rowid='.$ticket.';';
    $db->query($sql);

    if (GETPOST('state','int') == 4) {
        $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set datem=NOW() WHERE rowid='.$ticket.';';
        $db->query($sql);

    }

    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set category='.GETPOST('category','int').' WHERE rowid='.$ticket.';';
    $db->query($sql);

    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set title="'.GETPOST('ticket_shortdesc','alpha').'" WHERE rowid='.$ticket.';';
    $db->query($sql);
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set description="'.GETPOST('ticket_longdescription','alpha').'" WHERE rowid='.$ticket.';';
    $db->query($sql);
    
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set datem=NOW() WHERE rowid='.$ticket.';';
    $db->query($sql);
    

    if ( ( (GETPOST('ticket_private_comment','alpha') != "" )) || ( (GETPOST('ticket_public_comment','alpha') != "")) ) {
        $tcmin=(int)GETPOST('timecard_minutes','alpha');
        $tchour=60*(int)GETPOST('timecard_hour','int');
        $tc=$tcmin+$tchour;
        $pnumber=0;
        $pnumber=count_points($tc,GETPOST('techprofile','int'));
        update_contract_points($db, GETPOST('contractid','int'), $pnumber);

        $object = new Helpdeskcomment($db);
        $object->datec = time();
        $object->userid = GETPOST("commercial_suivi_id");
        $object->ticketid = $ticket;
        $object->pubdesc = GETPOST('ticket_public_comment','alpha');
        $object->privdesc = GETPOST('ticket_private_comment','alpha');
        $object->timecard=$tc;
        $object->contractid=GETPOST('contractid','int');
        $object->techprofile=GETPOST('techprofile','int');
        $object->points=$pnumber;
        $ret = $object->create();
    }

    header('Location: /helpdesk/');

} elseif ($action == "inter") {
  $fiobject = new Fichinter($db);
//  $fiextrafields = new ExtraFields($db);
//  $fiextralabels=$extrafields->fetch_name_optionals_label($object->table_element);
  #$fiobject->socid = GETPOST('socid','int');
  $ticketnum=GETPOST('ticket','int');
  $commentnum=GETPOST('comment','int');
  $sqlt="SELECT * FROM ".MAIN_DB_PREFIX."helpdesk_ticket  WHERE rowid=".$ticketnum.";";
  $sqlc="SELECT * FROM ".MAIN_DB_PREFIX."helpdesk_comment  WHERE rowid=".$commentnum.";";

  $resultt = $db->query($sqlt);
  if ($resultt) {
    $objt = $db->fetch_object($resultt);
  }

  $resultc = $db->query($sqlc);
  if ($resultc) {
    $objc = $db->fetch_object($resultc);
  }

  $fiobject->socid          = $objt->entity;
  $fiobject->duration       = intval($objc->timecard);
  $fiobject->fk_contrat     = $objc->contractid;
  $fiobject->author         = $user->id;
  $fiobject->description    = $objt->title;
//  $fiobject->note_private   = $objc->privdesc;
  $fiobject->note_public    = $objt->description;
  $id = $fiobject->create($user);

  $iduree = intval($objc->timecard)*60;
  $sqlidet="INSERT INTO llx_fichinterdet VALUES (NULL, ".$id.", NULL, '".$objt->datec."', '".$objc->pubdesc."', '".$iduree."', 0);";
  $db->query($sqlidet);
  header('Location: /fichinter/card.php?leftmenu=ficheinter&id='.$id);



} else {

llxHeader("","Helpdesk");

print load_fiche_titre($langs->trans('HelpdeskAnswer'),'','title_commercial.png');


$sql="SELECT * ";
$sql.= 'FROM '.MAIN_DB_PREFIX."helpdesk_ticket as t ";
$sql.= ' WHERE '.$ticket.' = t.rowid';
$sql.= ';';

$staticticket = new HelpdeskTicket($db);

$result = $db->query($sql);
if ($result) {
   $obj = $db->fetch_object($result);
}


print '<form name="form_ticket" action="'.$_SERVER["REQUEST_URI"].'&action=update" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="update">';
print '<input type="hidden" name="ticket" value="'.$ticket.'">';

dol_fiche_head();

print '<table class="border" width="100%">';

print '<tr><td class="fieldrequired">'.$langs->trans('ThirdParty').'</td>';
print '<td colspan="2">';
print $form->select_company($obj->entity,'socid','',1);
print '</td>';
print '</tr>'."\n";

print '<tr><td width="20%" class="nowrap"><span class="fieldrequired">'.$langs->trans("HelpdeskCaller").'</span></td><td>';
print $form->selectcontacts($obj->entity,$obj->callerid,'contactid',2,'','',1,'',false,1);
print '</td></tr>';

print '<tr><td class="fieldrequired">'.$langs->trans('HelpdeskTicketState').'</td>';
print '<td colspan="2">';
print '<select name="state" value="'.$obj->state.'">';
if ($obj->state == 0) { $selected="selected";} else { $selected = ""; }
print '    <option '.$selected.' value="0">'.$langs->trans("HelpdeskStateNew").'</option>';
if ($obj->state == 1) { $selected="selected";} else { $selected = ""; }
print '    <option '.$selected.' value="1">'.$langs->trans("HelpdeskStateAwaitingOperator").'</option>';
if ($obj->state == 2) { $selected="selected";} else { $selected = ""; }
print '    <option '.$selected.' value="2">'.$langs->trans("HelpdeskStateAwaitingCustomer").'</option>';
if ($obj->state == 3) { $selected="selected";} else { $selected = ""; }
print '    <option '.$selected.' value="3">'.$langs->trans("HelpdeskStateAwaitingEditor").'</option>';
if ($obj->state == 4) { $selected="selected";} else { $selected = ""; }
print '    <option '.$selected.' value="4">'.$langs->trans("HelpdeskStateSolved").'</option>';
print '</select>';
print '</td>';
print '</tr>'."\n";

print '<tr><td class="fieldrequired">'.$langs->trans('HelpdeskTicketCategory').'</td>';
print '<td colspan="2">';
print '<select name="category" value="'.$obj->category.'">';
if ($obj->category == 0) { $selected="selected";} else { $selected = ""; }
print '    <option '.$selected.' value="0">'.$langs->trans("HelpdeskCategoryNone").'</option>';
if ($obj->category == 1) { $selected="selected";} else { $selected = ""; }
print '    <option '.$selected.' value="1">'.$langs->trans("HelpdeskCategoryIncident").'</option>';
if ($obj->category == 2) { $selected="selected";} else { $selected = ""; }
print '    <option '.$selected.' value="2">'.$langs->trans("HelpdeskCategoryRequest").'</option>';
if ($obj->category == 3) { $selected="selected";} else { $selected = ""; }
print '    <option '.$selected.' value="3">'.$langs->trans("HelpdeskCategoryChange").'</option>';
print '</select>';
print '</td>';
print '</tr>'."\n";


print '<tr><td width="20%" class="nowrap"><span class="fieldrequired">'.$langs->trans("HelpdeskAssignedTo").'</span></td><td>';
print $form->select_dolusers($obj->createdby,'commercial_suivi_id',1,'');
print '</td></tr>';
print '<tr><td class="filedrequired">'.$langs->trans('HelpdeskShortDescription').'</td>';
print '<td colspan="2"><input type="text" size="150" name="ticket_shortdesc" id="ticket_shortdescref_customer" value="'.$obj->title.'"></td></tr>'."\n";

print '<tr><td>'.$langs->trans("HelpdeskLongDescription").'</td><td valign="top">';

$doleditor=new DolEditor('ticket_longdescription', $obj->description, '', '100', 'ticket_notes', 'In', 1, true, true, 0, 70);
print $doleditor->Create(1);
print '<tr><td height="100"></td><td height="100"></td></tr>';
print '<tr><td class="nowrap">'.$langs->trans("HelpdeskPrivateComment").'</td>';
print '<td><textarea style="color: black; background-color: #ffff99" id="ticket_private_comment" name="ticket_private_comment" rows="8" cols="100" class="flat"></textarea></td></tr>';
print '<tr><td>'.$langs->trans("HelpdeskPublicComment").'</td>';
print '<td colspan="2"><textarea id="ticket_public_comment" name="ticket_public_comment" rows="8" cols="100" class="flat"></textarea></td></tr>';

print '<tr><td>'.$langs->trans("HelpdeskTimeCard").'</td><td><div align="center">';
print '<select name="timecard_hour">';
for ($i=0;$i<24;$i++) {
print '    <option value="'.$i.'"">'.$i.'</option>';
}
print '</select>';

print '<select name="timecard_minutes">';
print '    <option value="00">00</option>';
print '    <option value="10">10</option>';
print '    <option value="20">20</option>';
print '    <option value="30">30</option>';
print '    <option value="40">40</option>';
print '    <option value="50">50</option>';
print '</select>';
print '</div></td></tr>';

print '<tr><td>'.$langs->trans("HelpdeskTechProfile").'</td><td><div align="center">';
print '<select name="techprofile">';
print '    <option value="0">'.$langs->trans("HelpdeskTechProfileWorkshop").'</option>';
print '    <option value="1">'.$langs->trans("HelpdeskTechProfileTech").'</option>';
print '    <option value="2">'.$langs->trans("HelpdeskTechProfileEngineer").'</option>';
print '</select>';
print '</div></td></tr>';

print '<tr><td>'.$langs->trans("HelpdeskServiceContract").'</td><td><div align="center">';
print '<select name="contractid">';
$contracts=contract_list_for_company($db,$obj->entity);
foreach ($contracts as $contract) {
print '    <option value="'.$contract->rowid.'">';
print '    '.$contract->label.' - '.$langs->trans("HelpdeskFromDate").' '.$contract->dateo.' '.$langs->trans("HelpdeskToDate").' '.$contract->datef.' ('.$contract->pcur.'/'.$contract->pinit.')';
print '    </option>';
}
print '</select>';

print '</div></td></tr>';




print '<tr><td colspan="2"><br /><div align="center"><input type="submit" class="button" value="'.$langs->trans("Update").'"></div></td></tr>';

print "</form>\n";

print "</table>\n";


print "<br /><br /><br />";

print "<table>\n";

$sql="SELECT c.rowid, c.userid, c.datec, c.pubdesc, c.privdesc, c.timecard, c.techprofile, c.points, u.firstname, u.lastname ";
$sql.= 'FROM '.MAIN_DB_PREFIX."helpdesk_comment as c, ";
$sql.= MAIN_DB_PREFIX."user as u ";
$sql.= ' WHERE c.ticketid = '.$ticket;
$sql.= ' AND u.rowid = c.userid';
$sql.= ' ORDER BY datec DESC';
$sql.= ';';

$result = $db->query($sql);

if ($result)
{
    while ($obj = $db->fetch_object($result)) {
        print '<tr>';
        print '<td>Intervenant : '.$obj->firstname.' '.$obj->lastname.'<br />';
        print 'Date : '.date( 'Y-m-d H:i:s', strtotime($obj->datec)).'<br />';
        $tc_hours = floor($obj->timecard / 60);
        $tc_minutes = $obj->timecard % 60;
        print 'Temps passé : '.$tc_hours.':'.$tc_minutes.'<br />';
        print 'Points : '.$obj->points.'<br />';
        print 'Profil : '.getProfileNameFromID($obj->techprofile).'<bbr />';
        print '</td>';
        print '</tr>';

        if ($obj->privdesc != "") {
            print '<tr><td></td><td><textarea rows="8" cols="100" style="color: black; background-color: #ffff99">'.$obj->privdesc.'</textarea></td></tr>';
        }
        if ($obj->pubdesc != "") {
            print '<tr>';
            print '<td></td>';
            print '<td><textarea rows="8" cols="100">'.$obj->pubdesc.'</textarea></td>';
            print '</tr>';
        }
        print '<tr><td></td><td><a href="'.$_SERVER['PHP_SELF'].'?ticket='.$ticket.'&comment='.$obj->rowid.'&action=inter">Cr&#233;er une fiche d\'intervention</a></td></tr>';
        print '<tr><td colspan="2"><hr /></td></tr>';
    }
}



print "</table>\n";

}



llxFooter();


?>

