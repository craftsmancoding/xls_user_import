<?php  
require_once 'xlsimporter.class.php';
// instantiate modx class
global $modx;
// instantiate the XLS Controller
$xls_importer = new XLS_Importer_Controller($modx);
$output = $xls_importer->get_template_content();
return $output;

?>