<?php  
/*
Master controller for the XLS User Import CMP
*/
require_once 'includes/functions.php';
require_once 'includes/xlsimporter.class.php';

global $modx;
$modx->lexicon->load('xls_user_import:default');

$XLS = new XLS_Importer_Controller();

$page = get('p', $_GET, 'upload_form');

return $XLS->$page();

/*EOF*/