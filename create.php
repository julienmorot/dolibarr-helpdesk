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


llxHeader("","Helpdesk");

print load_fiche_titre($langs->trans('HelpdeskCreateNew'),'','title_commercial.png');

if ($action == 'add') {
	$object = new HelpdeskTicket($db);
	$object->datec = time();
	$object->createdby = GETPOST("commercial_suivi_id");
	$object->entity = GETPOST('socid','int');
	$object->state = 0;
	$object->title = GETPOST('ticket_shortdesc','alpha');
	$object->description = GETPOST('ticket_longdescription','alpha');
    $object->callerid = GETPOST("contactid", "int");
    $object->category = GETPOST("category", "int");
	$object->create();
    header('Location: /helpdesk/list.php?state=0');
} else {


print '<form name="form_ticket" action="'.$_SERVER["PHP_SELF"].'" method="post">';
print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
print '<input type="hidden" name="action" value="add">';

dol_fiche_head();

print '<table class="border" width="100%">';

print '<tr><td class="fieldrequired">'.$langs->trans('ThirdParty').'</td>';
print '<td colspan="2">';
print $form->select_company('','socid','',1,1);
print '</td>';
print '</tr>'."\n";

print '<tr><td width="20%" class="nowrap"><span class="fieldrequired">'.$langs->trans("HelpdeskCaller").'</span></td><td>';
print $form->selectcontacts(0,0,'contactid',2,'','',1,'',false,1);
print '</td></tr>';

print '<tr><td width="20%" class="nowrap"><span class="fieldrequired">'.$langs->trans("HelpdeskAssignedTo").'</span></td><td>';
print $form->select_dolusers(GETPOST("commercial_suivi_id")?GETPOST("commercial_suivi_id"):$user->id,'commercial_suivi_id',1,'');
print '</td></tr>';

print '<tr><td class="fieldrequired">'.$langs->trans('HelpdeskTicketCategory').'</td>';
print '<td colspan="2">';
print '<select name="category" value="0">';
print '    <option value="0">'.$langs->trans("HelpdeskCategoryNone").'</option>';
print '    <option value="1">'.$langs->trans("HelpdeskCategoryIncident").'</option>';
print '    <option value="2">'.$langs->trans("HelpdeskCategoryRequest").'</option>';
print '    <option value="3">'.$langs->trans("HelpdeskCategoryChange").'</option>';
print '</select>';
print '</td>';
print '</tr>'."\n";


print '<tr><td class="filedrequired">'.$langs->trans('HelpdeskShortDescription').'</td>';
print '<td colspan="2"><input type="text" size="150" name="ticket_shortdesc" id="ticket_shortdescref_customer"></td></tr>';

print '<tr><td>'.$langs->trans("HelpdeskLongDescription").'</td><td valign="top">';

$doleditor=new DolEditor('ticket_longdescription', '', '', '100', 'ticket_notes', 'In', 1, true, true, 0, 70);
print $doleditor->Create();

dol_fiche_end();

print '<div align="center"><input type="submit" class="button" value="'.$langs->trans("Create").'"></div>';
print "</form>\n";





print "</table>\n";

}


llxFooter();


?>

