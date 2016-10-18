<?php

require '../main.inc.php';
error_reporting(E_ALL);
require_once DOL_DOCUMENT_ROOT.'/helpdesk/class/helpdeskticket.class.php';
//require_once DOL_DOCUMENT_ROOT.'/helpdesk/class/helpdeskcomment.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/project.lib.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';

llxHeader("","Helpdesk");

print '<div class="fichecenter"><div class="fichethirdleft">';

$listofsearchfields['search_inc']=array('text'=>'Incident');

if (count($listofsearchfields))
{
        print '<form method="post" action="'.DOL_URL_ROOT.'/core/search.php">';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<table class="noborder nohover centpercent">';
        $i=0;
        foreach($listofsearchfields as $key => $value)
        {
                if ($i == 0) print '<tr class="liste_titre"><td colspan="3">'.$langs->trans("Search").'</td></tr>';
                print '<tr '.$bc[false].'>';
                print '<td class="nowrap"><label for="'.$key.'">'.$langs->trans($value["text"]).'</label>:</td><td><input type="text" class="flat" name="'.$key.'" id="'.$key.'" size="18"></td>';
                if ($i == 0) print '<td rowspan="'.count($listofsearchfields).'"><input type="submit" value="'.$langs->trans("Search").'" class="button"></td>';
                print '</tr>';
                $i++;
        }
        print '</table>';
        print '</form>';
        print '<br>';
}
print '</div>';


print '<div class="fichetwothirdright"><div class="ficheaddleft">';
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="2">'.$langs->trans("HelpdeskStats").'</td></tr>';


$sql="select count(rowid) as number, state from llx_helpdesk_ticket group by state;";
$result = $db->query($sql);
if ($result)
{
    $num = $db->num_rows($result);
    $i = 0;
    while ($i < $num)
    {
        $obj = $db->fetch_object($result);
        print "<tr><td>";
switch ($obj->state) {
    case 0:
        print $langs->trans("HelpdeskStateNew");
        break;
    case 1:
        print $langs->trans("HelpdeskStateAwaitingOperator");
        break;
    case 2:
        print $langs->trans("HelpdeskStateAwaitingCustomer");
        break;
    case 3:
        print $langs->trans("HelpdeskStateAwaitingEditor");
        break;
    case 4:
        print $langs->trans("HelpdeskStateSolved");
        break;
}
        print '</td><td>'.$obj->number.'</td>';
        $i++;
    }

}
print '</table>';

print '</div>';

llxFooter();


?>

