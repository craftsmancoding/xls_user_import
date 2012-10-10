<?php
/**
*  XLS_Importer_Controller Class 
*  @param modx class
*  @param config array 
* 
*/
class XLS_Importer_Controller {

	public $modx;
    public $config = array();

	/**
	 *
	 */
    public function __construct() {
    	global $modx;
		$modx->regClientCSS(MODX_ASSETS_URL.'components/xls_user_import/css/mgr.css');
		$modx->regClientStartupScript('https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		$modx->regClientStartupScript(MODX_ASSETS_URL.'components/xls_user_import/js/redips-drag-min.js');
		$modx->regClientStartupScript(MODX_ASSETS_URL.'components/xls_user_import/js/script.js');
    }

	/** 
	 * Handle 404s
	 *
	 * @param string $name
	 * @param array $args
	 *
	 */
	public function __call($name , $args) {
		global $modx;
		$data = array();
		$data['msg'] = $modx->lexicon('404_not_found');
		return load_view('404.php',$data);
	}

	/**
	 * This controller function displays the upload form
	 */
	public function upload_form() {
		// See http://rtfm.modx.com/display/revolution20/Adding+CSS+and+JS+to+Your+Pages+Through+Snippets
		global $modx;
		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
		$data['format'] = $modx->lexicon('xls_format');
		$data['choose'] = $modx->lexicon('choose');
		$data['upload'] = $modx->lexicon('xls_upload');
		
		return load_view('upload_form.php',$data);
	}
	
	/*
	* This function moves the file on uploads/ dir
	*/
	public function process_upload() {
		global $modx;
		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
		// Where the file is going to be placed 
		$target_path = MODX_CORE_PATH . "components/xls_user_import/uploads/";

		$file_path = $target_path . basename( $_FILES['uploaded_file']['name']); 

		if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path)) {
			$data['upload_msg'] = $modx->lexicon('upload_msg_success');
		} else{
		    $data['upload_msg'] = $modx->lexicon('upload_msg_error');
		}

		$data['header_fields'] = $this->read_file($file_path);

		return load_view('process_upload.php',$data);
	}


	public function read_file($file) {
		$excel = new Spreadsheet_Excel_Reader();
    	$excel->read($file);   
    	$all_fields = $excel->sheets[0]['cells'];
    	$header_fields = array_slice($all_fields, 0, 1);
    	$header_fields = $header_fields[0];
    	return $header_fields;
    
	}


}


/*EOF*/