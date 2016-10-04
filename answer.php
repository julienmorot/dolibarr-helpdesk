<?php

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/helpdesk/class/helpdeskticket.class.php';
require_once DOL_DOCUMENT_ROOT.'/helpdesk/class/helpdeskcomment.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';

$langs->load("helpdesk");
$langs->load("contracts");
$langs->load("companies");
$action=GETPOST('action','alpha');
$ticket=GETPOST('ticket','int');


llxHeader("","Helpdesk");

print load_fiche_titre($langs->trans('HelpdeskAnwser'),'','title_commercial.png');

//FIXME : Update pas optimal du tout!
if ($action == 'update') {
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set entity='.GETPOST('socid','int').' WHERE rowid='.$ticket.';';
    $db->query($sql);
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set createdby='.GETPOST('commercial_suivi_id','int').' WHERE rowid='.$ticket.';';
    $db->query($sql);
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set state='.GETPOST('state','int').' WHERE rowid='.$ticket.';';
    $db->query($sql);
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set title="'.GETPOST('ticket_shortdesc','alpha').'" WHERE rowid='.$ticket.';';
    $db->query($sql);
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set description="'.GETPOST('ticket_longdescription','alpha').'" WHERE rowid='.GETPOST('ticket','int').';';
    $db->query($sql);
    
    $sql='UPDATE '.MAIN_DB_PREFIX.'helpdesk_ticket set datem='.time().' WHERE rowid='.GETPOST('ticket','int').';';
    $db->query($sql);

} else {

$sql="SELECT * ";
$sql.= 'FROM '.MAIN_DB_PREFIX."helpdesk_ticket as t ";
$sql.= ' WHERE '.$ticket.' = t.rowid';
$sql.= ';';

$staticticket = new HelpdeskTicket($db);

$result = $db->query($sql);
if ($result) {
   $obj = $db->fetch_object($result);
}



print '<form name="form_ticket" action="'.$_SERVER["PHP_SELF"].'" method="post">';
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

// FIXME : un select ça ne serait pas de refus
print '<tr><td class="fieldrequired">'.$langs->trans('HelpdeskTicketState').'</td>';
print '<td colspan="2">';
print '<input type="text" size="1" name="state" value="'.$obj->state.'">';
print '</td>';
print '</tr>'."\n";

print '<tr><td width="20%" class="nowrap"><span class="fieldrequired">'.$langs->trans("HelpdeskCaller").'</span></td><td>';
print $form->select_dolusers($obj->createdby,'commercial_suivi_id',1,'');
print '</td></tr>';
print '<tr><td class="filedrequired">'.$langs->trans('HelpdeskShortDescription').'</td>';
print '<td colspan="2"><input type="text" size="150" name="ticket_shortdesc" id="ticket_shortdescref_customer" value="'.$obj->title.'"></td></tr>'."\n";

print '<tr><td>'.$langs->trans("HelpdeskLongDescription").'</td><td valign="top">';

$doleditor=new DolEditor('ticket_longdescription', $obj->description, '', '100', 'ticket_notes', 'In', 1, true, true, 0, 70);
print $doleditor->Create();


print "\n".'<tr><td>'.$lang->trans("HelpdeskPrivateComment").'</td>';
print "\n".'<td><textarea id="ticket_private_comment" name="ticket_private_comment" rows="5" cols="70" class="flat"></td></tr>'."\n";
print "\n".'<tr><td>'.$lang->trans("HelpdeskPublicComment").'</td>';
print "\n".'<td><textarea id="ticket_private_comment" name="ticket_public_comment" rows="5" cols="70" class="flat"></td></tr>'."\n";

print '<div align="center"><input type="submit" class="button" value="'.$langs->trans("Update").'"></div>';

print "</form>\n";





print "</table>\n";

}

dol_fiche_end();


llxFooter();


?>

