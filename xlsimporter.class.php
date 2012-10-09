<?php
//require config file
require_once dirname(dirname(dirname(__FILE__))).'/config/config.inc.php';
//require modx class 
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

/**
*  XLS_Importer_Controller Class 
*  @param modx class
*  @param config array 
* 
*/
class XLS_Importer_Controller {

	public $modx;
    public $config = array();

    public function __construct($modx,array $config = array()) {
        $this->modx = $modx;

       $core_path = MODX_CORE_PATH . 'components/xls_user_import/';
        $this->config = array_merge(array(

            'core_path' => $core_path,
            'tpl_path' => $core_path .'templates/',
            'tpl' => 'default.tpl'
        ),$config);
 
    }

    /*
    * get_template_content method - get the defautl.tpl 
    * 
    */

    public function get_template_content() {

    	$file = $this->config['tpl_path'] . $this->config['tpl'];

  
    	$output = '';
		if (isset($file) && file_exists($file)) {
		    if (!isset($return) || $return == false) {
		        ob_start();
		        include ($file);
		        $output = ob_get_contents();
		        ob_end_clean();
		    } else {
		        $output = include ($file);
		    }
		} else {
		   $path_parts = pathinfo($file);
		   return sprintf('The file: %s was not found', $path_parts['basename']);
		}
	
		return $output;

    }
}


?>