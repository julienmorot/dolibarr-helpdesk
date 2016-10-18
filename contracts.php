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

print load_fiche_titre($langs->trans('HelpdeskServiceContracts'),'','title_commercial.png');

$sql="select c.rowid, c.ref, from llx_contrat as c, llx_contratdet as p, llxcontratdet_extrafileds as e where llx_contrat_extrafields.fk_object=llx_contrat.rowid and statut=1;";
$sql="select c.rowid, c.ref from llx_contrat as c, llx_contratdet as p, llx_contratdet_extrafields as e where p.fk_contrat=c.rowid and e.fk_object=p.rowid;";

print '<table class="liste">'."\n";
print '<tr class="liste_titre">';
print_liste_field_titre($langs->trans("HelpDeskTicketNumber"));
print_liste_field_titre($langs->trans("ThirdParty"));
print_liste_field_titre($langs->trans("HelpdeskCaller"));
print_liste_field_titre($langs->trans("HelpdeskCreatedBy"));
print_liste_field_titre($langs->trans("HelpdeskShortDescription"));
print_liste_field_titre($langs->trans("HelpdeskDateCreation"));
print '</tr>'."\n";




llxFooter();


?>

