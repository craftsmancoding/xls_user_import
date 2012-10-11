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
		$data['file_path'] = $file_path;

		// Get the fields on the xls fiel header row
		$header_fields = array_slice( $this->read_file($file_path) , 0, 1);
    	$header_fields = $header_fields[0];
		$data['header_fields'] = $header_fields;

		return load_view('map_fields.php',$data);
	}

	/** 
	 * read_file method gets the content of the xls file
	 *
	 * @param string $file
	 * @return array $all_fields
	 *
	 */
	public function read_file($file) {
		$excel = new Spreadsheet_Excel_Reader();
    	$excel->read($file);   
    	$all_fields = $excel->sheets[0]['cells'];
    	return $all_fields;
	}

	/**
	 * $_POST contains keys for all MODX user fields.  The values in each spot of the array
	 * are number(s) (comma-separated), corresponding to the source column.  E.g. 
	 * if the fullname is meant to be constructed from the first and second columns in the 
	 * uploaded XLS file, then $_POST['fullname'] = '1,2';
	 *
	 *
	 */
	public function map_fields() {
		$file_path = $_POST['filepath'];
		$xls_fields = $this->read_file($file_path);
		$header_row = $xls_fields[1];
		unset($xls_fields[1]);
//print_r($_POST); exit;
//print_r($xls_fields);
		$mapping['fullname'] = explode(',',$_POST['fullname']);
		$mapping['age'] = explode(',',$_POST['age']);
		
		$modUser_columns = array('username','class_key','active','hash_class','primary_group');

		foreach ($xls_fields as $field) {
		
			$modUser_data = array(); // for stuff that goes to the modx_users table
			$modUserProfile_data = array(); // for stuff that goes to the modx_user_attributes table
		
			foreach ($mapping as $key => $value) {
				foreach ($value as $c) {
					// Special treatement for extended: we must store the key=>value (not just the value)
					if ($key == 'extended') {
						$header = $header_row[$c];
						if (isset($modUserProfile_data['extended'][$header])) {
							$modUserProfile_data['extended'][$header] .= ' '. isset($field[$c]) ? $field[$c] : '';
						}
						else {
							$modUserProfile_data['extended'][$header] = isset($field[$c]) ? $field[$c] : '';
						}
					}
					elseif(in_array($key, $modUser_columns)) {
						if (isset($modUser_data[$key])) {
							$modUser_data[$key] .= ' '. isset($field[$c]) ? $field[$c] : '';
						}
						else {
							$modUser_data[$key] = isset($field[$c]) ? $field[$c] : '';
						}
					}
					else {
						if (isset($modUserProfile_data[$key])) {
							$modUserProfile_data[$key] .= ' '. isset($field[$c]) ? $field[$c] : '';
						}
						else {
							$modUserProfile_data[$key] = isset($field[$c]) ? $field[$c] : '';
						}
					}
					// Avoid using $$variable notation: it's difficult to follow!
					//$$key = isset($field[$c]) ? $field[$c] : '';
				}
			}
//			echo '==' . $fullname . '<br>';
			print_r($modUserProfile_data);
			print_r($modUserProfile_data);
			print '<hr/>';
 	
		}

		die();  

	}

}


/*EOF*/