<?php

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/helpdesk/class/helpdeskticket.class.php';
require_once DOL_DOCUMENT_ROOT.'/helpdesk/class/helpdeskcomment.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';


$langs->load("helpdesk");
$langs->load("contracts");
$langs->load("companies");

if ($page < 0) { $page = 0; }
$limit = GETPOST('limit')?GETPOST('limit','int'):$conf->liste_limit;
$offset = $limit * $page;
//$state = GETPOST('state')?GETPOST('state','int'):'%';
$state = GETPOST('state','int');

llxHeader("","Helpdesk");

print load_fiche_titre($langs->trans('HelpDeskListOfTickets'),'','title_commercial.png');

print '<table class="liste">'."\n";
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("HelpDeskTicketNumber"));
print_liste_field_titre($langs->trans("ThirdParty"));
print_liste_field_titre($langs->trans("HelpdeskCreatedBy"));
print_liste_field_titre($langs->trans("HelpdeskShortDescription"));
print_liste_field_titre($langs->trans("HelpdeskDateCreation"));
print_liste_field_titre($langs->trans("HelpdeskTicketState"));
print '</tr>'."\n";

$sql="SELECT t.rowid, t.datec, t.createdby, t.state, t.title, s.nom as entity, u.firstname, u.lastname ";
$sql.= ' FROM '.MAIN_DB_PREFIX.'societe as s, ';
$sql.= MAIN_DB_PREFIX."helpdesk_ticket as t, ";
$sql.= MAIN_DB_PREFIX."user as u ";
$sql.= ' WHERE t.entity = s.rowid';
$sql.= ' AND u.rowid = t.createdby';
$sql.= ' AND t.state ='.$state.'';
$sql.= ';';

$staticticket = new HelpdeskTicket($db);

$result = $db->query($sql);
if ($result)
{
    $num = $db->num_rows($result);
    $i = 0;
    while ($i < min($num,$limit))
    {
        $obj = $db->fetch_object($result);
        $staticticket->rowid = $obj->rowid;
        $staticticket->datec = $obj->datec;
        $staticticket->createdby = $obj->createdby;
        $staticticket->entity = $obj->entity;
        $staticticket->state = $obj->state;
        $staticticket->title = $obj->title;
        print "<tr>";
        print '<td><strong><a href="'.DOL_URL_ROOT.'/helpdesk/answer.php?ticket='.$obj->rowid.'">Ticket '.$obj->rowid.'</strong></td>';
        print '<td>'.$obj->entity.'</td>';
        print '<td>'.$obj->firstname.' '.$obj->lastname.'</td>';
        print '<td>'.$obj->title.'</td>';
        print '<td>'.$obj->datec.'</td>';
        print '<td>'.$obj->state.'</td>';
        $i++;
    }

}

print '</table>'."\n";

llxFooter();
$db->close();


?>

