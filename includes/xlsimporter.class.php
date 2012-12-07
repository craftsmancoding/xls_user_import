<?php
/**
*  XLS_Importer_Controller Class 
*  Consist of methods that will parse the data from the xls file 
*  and import them on the user and profil tbl of modx dataabase
* 
*/


class XLS_Importer_Controller {

	public $modx;
    public $config = array();

    // set a modx_fields var which has the input type and defautl value
    // user array_keys to get the modx fields name
    public $modx_fields = array(
    	'email' => array( 'type' => 'text', 'value' => '' ),
    	'fullname' => array( 'type' => 'text','value' => '' ),
    	'phone' => array( 'type' => 'text','value' => true ),
    	'mobilephone' => array( 'type' => 'text', 'value' => ''),
    	'class_key' => array( 'type' => 'text','value' => 'modUser'),
    	'active' => array('type' => 'checkbox','value' => true ),
    	'primary_group' => array( 'type' => 'text', 'value' => '' ),
    	'sudo' => array( 'type' => 'checkbox','value' => false ),
    	'blocked' => array( 'type' => 'checkbox', 'value' => false ),
    	'blockeduntil' => array( 'type' => 'date', 'value' => '' ),
    	'blockedafter' => array( 'type' => 'date', 'value' => '' ),
    	'dob' => array( 'type' => 'date', 'value' => ''),
    	'fax' => array( 'type' => 'text', 'value' => '' ),
    	'photo' => array( 'type' => 'text', 'value' => '' ),
    	'comment' => array( 'type' => 'textarea', 'value' => '' ),
    	'website' => array( 'type' => 'text', 'value' => '' ),
    	'gender' => array( 'type' => 'select', 'value' => 'male,female' ),
    	'address' => array( 'type' => 'text', 'value' => '' ),
    	'country' => array( 'type' => 'text', 'value' => '' ),
    	'city' => array( 'type' => 'text', 'value' => '' ),
    	'state' => array( 'type' => 'text', 'value' => '' ),
    	'zip' => array( 'type' => 'text', 'value' => '' ),
    	'extended' => array( 'type' => 'textarea', 'value' => '' )
    );

    

    public $modx_fields_defaults = array('email,text,""','active,checkbox,1','fullname,text,""','phone,text,""','mobilephone,text,""','class_key,text,modUser','primary_group,text,""','sudo,checkbox,0','blocked,checkbox,0','blockeduntil,date,""','blockedafter,date,""','dob,date,""','fax,default,""','photo,text,""','comment,textarea,""','website,text,""','gender,text,""','address,text,""','country,text,""','city,text,""','state,text,""','zip,text,""');
	
	/**
	 * __construct will just add web assets like css and js
	 *
	 */
    public function __construct() {
    	global $modx;
		$modx->regClientCSS(MODX_ASSETS_URL.'components/xls_user_import/css/mgr.css');
		$modx->regClientCSS(MODX_ASSETS_URL.'components/xls_user_import/css/colorbox/v2/colorbox.css');
		$modx->regClientStartupScript('https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		$modx->regClientStartupScript(MODX_ASSETS_URL.'components/xls_user_import/js/xls_import_plugins.js');
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
	* edit extended method
	* Edit the extended field key
	* @return back to preview_data page
	*/
	public function edit_extended() {
		global $modx;
		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
		$data['file_path'] = $_POST['file_path'];
		$data['mapped_data'] = array();
		$data['email_msg'] = $modx->getOption('xls.emailMessage');
		
		// get the mapped data from POST var
		$posted_mapped_data = json_decode($_POST['mapped_data'],true);
		// remove th emapped data on th epost var
		array_pop($_POST);
		// reset the modified $_POST to new var
		$extended_new = $_POST;

		// Replaced the extended value by the new extended key 
		foreach ($posted_mapped_data as &$user) {
			foreach ($extended_new as $old_ext => $new_ext) {
				if ($extended_new[$old_ext] != "") {
					$user['extended'] = str_replace($old_ext, preg_replace("![^a-z0-9]+!i", "_", $new_ext), $user['extended']);
				}
			}
				
		}

		$data['mapped_data'] = $posted_mapped_data;
	
		return load_view('preview_data.php',$data);
	}

	/**
	* import_users
 	* $_POST['import_fields'] is a json from the preview page
 	* loop though and add it on user modx tbl and profile tbl
	*/
 	public function import_users() {

 		global $modx;

 		//require_once $modx->getOption('login.core_path',null,$modx->getOption('core_path').'components/login/').'model/login/login.class.php';
/* 		echo '<pre>';
 		print_r($_POST);
 		die();*/
 		$import_fields = json_decode($_POST['import_fields'], true);

 		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
 		//set message 
		$user_added = 'failed';
		$profile_added = 'failed';
		$email_sent = 'failed';
			

		$data['result'] = array();
		$client_user_group_id = 123; // Id of a User Group  
		//$Login = new Login($modx);  

		foreach ($import_fields as $ifield) {
			
			$user = $modx->newObject('modUser');
			$profile = $modx->newObject('modUserProfile');

			// generate 6 random string
			$temp_password = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 6)), 0, 6);

			$user->set('username',$ifield['email']);
			$user->set('active',($ifield['active'] == true) ? 1 : 0  );
			$user->set('password', $temp_password);
			$user->set('class_key', $ifield['class_key']);
			$user->set('primary_group', $ifield['primary_group']);
			$user->set('sudo', ($ifield['sudo'] == true) ? 1 : 0  );

			$profile->set('email',$ifield['email']);
			$profile->set('internalKey',0);
			$profile->set('fullname', $ifield['fullname']);
			$profile->set('phone', $ifield['phone']);
			$profile->set('mobilephone', $ifield['mobilephone']);
			$profile->set('blocked', ($ifield['blocked'] == true) ? 1 : 0  );
			$profile->set('blockeduntil', strtotime($ifield['blockeduntil']));
			$profile->set('blockedafter', strtotime($ifield['blockedafter']));
			$profile->set('address', $ifield['address']);
			$profile->set('country', $ifield['email']);
			$profile->set('city', $ifield['city']);
			$profile->set('state', $ifield['state']);
			$profile->set('zip', $ifield['zip']);
			$profile->set('dob', strtotime($ifield['dob']));
			$profile->set('gender', $ifield['gender']);
			$profile->set('fax', $ifield['fax']);
			$profile->set('photo', $ifield['photo']);
			$profile->set('comment', $ifield['comment']);
			$profile->set('website', $ifield['website']);
			$profile->set('extended', $ifield['extended']);
			$user->addOne($profile,'Profile');

			/* save user */
			if (!$user->save()) {
			    $modx->log(modX::LOG_LEVEL_ERROR,'[User Creation] Could not create user: '.$user->get('id').' with username: '.$user->get('username'));
			} else {
				if(isset($_POST['email_notification'])) {
					// if user was  saved run the send_modx_email method
					$email_sent = $this->send_modx_email($ifield['email'], $temp_password, $ifield['fullname'], $_POST['email_msg']);
				} 
				$user_added = 'success';
				$profile_added = 'success';	
			}


			/* Add User to the default Clients group */
			$Member = $modx->newObject('modUserGroupMember');
			$Member->set('user_group', $client_user_group_id);
			$Member->set('member', $user->get('id'));
			$Member->set('role', '1');
			$Member->set('rank', '0');
			if (!$Member->save()) {
			    $modx->log(modX::LOG_LEVEL_ERROR,'[User Creation] Could not add newly created user to default "Clients" User Group!  User id '.$user->get('id').' with username: '.$user->get('username'));
			}

			$user_data = array(
				'username' => $ifield['email'],
				'added_msg' => $user_added,
				'email_msg' => $email_sent
			); 
			array_push($data['result'], $user_data);
		}

 		return load_view('import_result.php',$data);
 	}

 	/**
 	*  preview_map method 
 	*  posted var with a key of xls_fields must contain of the column number and 
 	*  a column name from the xls file
 	*  @param string $_POST[xls_fields]
 	*  @return array $data['mapped_fields']
 	*/
	public function preview_map() {
		global $modx;

		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
		$data['file_path'] = get('file_path', $_POST);
		$xls_fields = get('xls_fields', $_POST, array());
		$modx_fields = array_keys($this->modx_fields);
		//$data['modx_defaults'] = $this->modx_fields_defaults;

		$data['mapped_fields'] = array();
		
		echo '<pre>';
		print_r($xls_fields);
		

		foreach ($modx_fields as $mfield) {
			$pre_map[$mfield] = '';
			foreach ($xls_fields as $xfield_key => $xfield_val) {
				foreach ($xfield_val as $x_key => $x_val) {
					// construc array here
					// like 
					// [address] => Array (
					//    [6] => address1,
					//     [7] => address2,
					//     [8] => address3,
					// ) 

				}
			
			}
		}
		
		$data['mapped_fields'] = $pre_map;

		echo '<pre>';
		print_r($data['mapped_fields']);
		die();
		return load_view('preview_map.php',$data);
	}
	

	/**
	 * $_POST contains keys for all MODX user fields.  The values in each spot of the array
	 * are number(s) (comma-separated), corresponding to the source column.  E.g. 
	 * if the fullname is meant to be constructed from the first and second columns in the 
	 * uploaded XLS file, then $_POST['fullname'] = '1,2';
	 * @return $mapped_data array
	 *
	 */
	public function preview_import() {
		global $modx;
		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
		$data['file_path'] = $_POST['file_path'];
		$data['email_msg'] = $modx->getOption('xls.emailMessage');
		$xls_fields = $this->read_file($data['file_path']);
		$header_row = $xls_fields[1];


		// remove last element which is the file path
		array_pop($_POST);
		// remove set default value input
		unset($_POST['default_value']);


		// remove the header fields
		unset($xls_fields[1]);

		// set modx fields
		$modx_fields = $this->modx_fields;
		foreach ($modx_fields as $field) {
	
			$mapping[$field] = $_POST[$field];
		}

	
		$data['mapped_data'] = array();

/*		echo '<pre>';
		print_r($mapping);
		die();*/

		//remove the blank array value from extended field
		unset($mapping['extended'][0]);

		// loop the mapped extended data
		// set extended key to beused on line 191
		if(isset($mapping['extended'][1])) {
			foreach ($mapping['extended'] as $ext) {
				// get the extended field key
				$ext_key[] = $header_row[$ext];
			}
		}
		

		foreach ($xls_fields as $field) {
		
			$modx_data = array(); // for stuff that goes to the modx table
		
			foreach ($mapping as $key => $value) {

				foreach ($value as $c) {
					switch ($c) {
						case 'SPACE':
							$separator = ' ';
							break;
						case 'COMMA':
							$separator = ',';
							break;
						case 'COLON':
							$separator = ':';
							break;
						case 'DASH':
							$separator = '-';
							break;
						default:
							$separator = $c;
							break;
					}

					if (isset($modx_data[$key])) {

						// construct json encoded value for extended field
						if($key == 'extended') {
							// concatenate the mapped extended value and convert it to array
							$modx_data[$key] .= isset($field[$c]) ? '||' . $field[$c]  : '||'."";
						} else {
							$modx_data[$key] .= isset($field[$c]) ? $field[$c] : $separator;
						}


						
					}
					else {
						$modx_data[$key] = isset($field[$c]) ? $field[$c] :  '' ;

					}
				}
			}

			if(isset($ext_key)) {
				if(isset($modx_data['extended'])) {
					// explode the $modx_data['extended'] to array
					$ext_val = explode('||', $modx_data['extended']);			
					
					// combined the xtended value and key
					$ext = array_combine($ext_key, $ext_val);
								
					// json encode the form extended array values
					$ext_json = json_encode($ext);

					//set $modx_data['extended'] to json
					$modx_data['extended'] = $ext_json;
				} else {
					$modx_data['extended'] = '';
				}

			}

			
				// construct data consisting of the mapped fields
				array_push($data['mapped_data'], $modx_data);


		}
		

		return load_view('preview_data.php',$data);
	}

	/**
	* This function moves the file on uploads/ dir
	* Or direct the user on the upload form again if no file was entered
	* @param $file
	* @return array $data
	*/
	public function process_upload() {
		global $modx;
		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
		$data['format'] = $modx->lexicon('xls_format');
		$data['choose'] = $modx->lexicon('choose');
		$data['upload'] = $modx->lexicon('xls_upload');
		$data['title'] = $modx->lexicon('xls_user_import');
		$data['modx_fields'] = array_keys($this->modx_fields);

		// Where the file is going to be placed 
		$target_path = MODX_CORE_PATH . "components/xls_user_import/uploads/";


		if(isset($_FILES['uploaded_file']['name']) ) {
			$file_path = $target_path . basename( $_FILES['uploaded_file']['name']); 
			if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path)) {
				$data['upload_msg'] = $modx->lexicon('upload_msg_success');
			} else{
				// Direct back on the uplaod form if no file entered
				$data['upload_msg'] = $modx->lexicon('upload_msg_error');
				return load_view('upload_form.php',$data);
			    
			}
		} else {
			if (isset($_GET['file'])) {
				$data['upload_msg'] = $modx->lexicon('upload_msg_success');
				$file_path = $target_path . basename( $_GET['file']); 
			} else {
				// Direct back on the uplaod form if no file entered
				$data['upload_msg'] = $modx->lexicon('upload_msg_error');
				return load_view('upload_form.php',$data);
			}
		} 

		
		$data['file_path'] = $file_path;

		// Get the fields on the xls fiel header row
		$xls_fields = array_slice( $this->read_file($file_path) , 0, 1);
    	$xls_fields = $xls_fields[0];
		$data['xls_fields'] = $xls_fields;

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
	


 	/**
	* send_modx_email
	* @param strign email
	* @param string temp password
	* @param string fullname
 	* if user was added run this method
	*/
 	public function send_modx_email($email, $temp_password, $fullname, $email_msg) {
 		global $modx;

 		$email_tpl = isset($email_msg) ? $email_msg : $modx->getOption('xls.emailMessage');
 		
 		// Properties
		$props = array(
				'fullname' => $fullname,
				'password' => $temp_password,
				'username' => $email,
			);
 
		// Create the temporary chunk
		$uniqid = uniqid();
		$chunk = $modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
		$chunk->setCacheable(false);
		 
		$email_chunk = $chunk->process($props, $email_tpl);

 	

 		$email_sent = 'failed';
 		$reply_to_email = 'daniel.edano@gmail.com';
		$name = $email;
		$subject = 'New user created';

		// This function sends an email geared to solicit an activation clickback
		// $Login->sendEmail($email,$name,$subject);
		// so instead, we do it manually:

		$hash = array();
		$hash['url'] = $modx->makeUrl(53); // location of login page
		$hash['username'] = $name;
		$hash['password'] = $temp_password;

		$modx->getService('mail', 'mail.modPHPMailer');


		$modx->mail->set(modMail::MAIL_BODY, $email_chunk);
		$modx->mail->set(modMail::MAIL_FROM, $reply_to_email);
		$modx->mail->set(modMail::MAIL_FROM_NAME, 'Craftsmancoding');
		$modx->mail->set(modMail::MAIL_SENDER, $reply_to_email);
		$modx->mail->set(modMail::MAIL_SUBJECT, 'New Username and Password from Craftsmancoding');
		$modx->mail->address('to', $email, $fullname);
		$modx->mail->address('reply-to', $reply_to_email);
		$modx->mail->setHTML(true);
		$sent = $modx->mail->send();

		$email_sent = ($sent) ? 'success' : 'failed';

		$modx->mail->reset(); 
		
		return $email_sent;
 	}

}


/*EOF*/