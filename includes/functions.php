<?php

/**
 * Simple function to get a key out of an array
 * @param string $key
 * @param array $array
 * @param string $default (optional)
 * @return mixed
 */
function get($key, $array, $default='') {
	if (isset($array[$key])) {
		return $array[$key];
	}
	return $default;
}


/**
 * Load a view file. We put in some commonly used variables here for convenience
 *
 * @param string $file: name of a file inside of the "views" folder
 * @param array $data: an associative array containing key => value pairs, passed to the view
 * @return string
 */
function load_view($file, $data=array()) {

	$data['cmp_url'] = 'index.php?a='.get('a',$_GET);
	
	$output = '';
	if (file_exists(MODX_CORE_PATH.'components/xls_user_import/views/'.$file)) {
	    if (!isset($return) || $return == false) {
	        ob_start();
	        include (MODX_CORE_PATH.'components/xls_user_import/views/'.$file);
	        $output = ob_get_contents();
	        ob_end_clean();
	    }     
	} 
	else {
		global $modx;
		$output = $modx->lexicon('view_not_found', array('file'=> 'views/'.$file));
	}

	return $output;

}

/*EOF*/