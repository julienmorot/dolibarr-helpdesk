<?php
require('../../main.inc.php');
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

$langs->load("admin");
$langs->load("helpdesk");

if (! $user->admin)
    accessforbidden();

$action = GETPOST('action', 'alpha');

$list = array (
    'HELPDESK_POP_SERVER',
    'HELPDESK_POP_USER',
    'HELPDESK_POP_ADDRESS',
    'HELPDESK_POP_PASSWORD',
    'HELPDESK_POINTS_WORKSHOP_PER_PERIOD',
    'HELPDESK_POINTS_WORKSHOP_PERIOD',
    'HELPDESK_POINTS_TECH_PER_PERIOD',
    'HELPDESK_POINTS_TECH_PERIOD',
    'HELPDESK_POINTS_ENGINEER_PER_PERIOD',
    'HELPDESK_POINTS_ENGINEER_PERIOD',
);

/*
 * Actions
 */
if ($action == 'update') {
    $error = 0;

    foreach ($list as $constname) {
        $constvalue = GETPOST($constname, 'alpha');

        if (! dolibarr_set_const($db, $constname, $constvalue, 'chaine', 0, '', $conf->entity)) {
            $error ++;
        }
    }

    if (! $error) {
        setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    } else {
        setEventMessages($langs->trans("Error"), null, 'errors');
    }
}



llxHeader('', $langs->trans('Parameters'));

$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($langs->trans("HelpdeskSetup"), $linkback);

print '<form action="' . $_SERVER["PHP_SELF"] . '" method="post">';
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
print '<input type="hidden" name="action" value="update">';

dol_fiche_head($head, 'parameters', $langs->trans("Helpdesk"), 0, "user");

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td colspan="3">' . $langs->trans('HelpdeskMailbox') . '</td>';
print "</tr>\n";

foreach ( $list as $key ) {
    $var = ! $var;

    print '<tr ' . $bc[$var] . ' class="value">';

    // Param
    $label = $langs->trans($key);
    print '<td width="50%"><label for="' . $key . '">' . $label . '</label></td>';

    // Value
    print '<td>';
    print '<input type="text" size="20" id="' . $key . '" name="' . $key . '" value="' . $conf->global->$key . '">';
    print '</td></tr>';
}

print "</table>\n";

dol_fiche_end();

print '<div class="center"><input type="submit" class="button" value="' . $langs->trans('Modify') . '" name="button"></div>';

print '</form>';




llxFooter();



?>
