<?php
/**
 *  XLS_Importer_Controller Class
 *  Consist of methods that will parse the data from the xls file
 *  and import them on the user and profil tbl of modx dataabase
 *
 * The following nodes of the $_SESSION array are utilized:
 *
 * $_SESSION['xlsimporter']['file_path']  -- contains the filepath and name of the uploaded file
 * $_SESSION['xlsimporter']['mapping'][$xls_fieldname] -- contains the name of the destination of a
 *	 		MODX field where the data in the $xls_fieldname will be copied.
 * $_SESSION['xlsimporter']['columns'][$xls_fieldname] the column number where this field came from (starts with 1)
 * 
 * @package
 */


class XLS_Importer_Controller {

	public $modx;
	public $config = array();
	
	// Where the file is going to be placed relative to MODX_CORE_PATH. Use a trailing slash.
	public $target_path = 'cache/xls_user_import/';

	// set a modx_fields var which has the input type and defautl value
	// user array_keys to get the modx fields name
	public $modx_fields = array(
		'email' => array( 'type' => 'text', 'value' => '' ),
		'fullname' => array( 'type' => 'text', 'value' => '' ),
		'phone' => array( 'type' => 'text', 'value' => true ),
		'mobilephone' => array( 'type' => 'text', 'value' => ''),
		'class_key' => array( 'type' => 'text', 'value' => 'modUser'),
		'active' => array('type' => 'checkbox', 'value' => true ),
		'primary_group' => array( 'type' => 'text', 'value' => '' ),
		'sudo' => array( 'type' => 'checkbox', 'value' => false ),
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



	public $modx_fields_defaults = array('email,text,""', 'active,checkbox,1', 'fullname,text,""', 'phone,text,""', 'mobilephone,text,""', 'class_key,text,modUser', 'primary_group,text,""', 'sudo,checkbox,0', 'blocked,checkbox,0', 'blockeduntil,date,""', 'blockedafter,date,""', 'dob,date,""', 'fax,default,""', 'photo,text,""', 'comment,textarea,""', 'website,text,""', 'gender,text,""', 'address,text,""', 'country,text,""', 'city,text,""', 'state,text,""', 'zip,text,""');



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
		
		$this->target_path = MODX_CORE_PATH . $this->target_path;
	}


	/**
	 * Handle 404s
	 *
	 *
	 * @param string  $name
	 * @param array   $args
	 * @return unknown
	 */
	public function __call($name , $args) {
		global $modx;
		$data = array();
		$data['msg'] = $modx->lexicon('404_not_found');
		return load_view('404.php', $data);
	}

	/**
	* Checked if target_path doesnt exist create the directory
	* @param string $target_path
	* @return true
	*/
	private function __checked_target_path() {
		$dir_exist = true;
		if (!is_dir($this->target_path)) {
			$dir_check = (!@mkdir($this->target_path)) ? false : true;
		}
		return $dir_exist;
	}


	//------------------------------------------------------------------------------
	/**
	 * Calculate all available <options> for the mapping dropdowns.  
	 * Pre-select options if possible.
	 * 
	 * @param array $modx_fields a list of all available MODX fields
	 * @param string $selection current selection of this field
	 * @return string HTML <option>'s that should be sandwhiched inbetween <select>
	 */
	private function _get_mapping_options($modx_fields,$selection) {
		$output = '<option value=""></option>';
		foreach ($modx_fields as $f) {
			$is_selected = '';
			if ($f == $selection) {
				$is_selected = ' selected="selected"';
			}
			$output .= sprintf('<option value="%s"%s>%s</option>',$f,$is_selected,$f);
		}
		return $output;
	}

	
	
	//------------------------------------------------------------------------------
	/**
	 * Given a fieldname from the XLS file, take our best guess as to which MODx field
	 * would correspond. Defaults to "extended" if nothing better comes along.
	 *
	 * @param string $xls_fieldname
	 * @return string MODX fieldname
	 */
	private function _guess_modx_fieldname($xls_fieldname) {
		$xls_fieldname = strtolower($xls_fieldname);
		// First check the easy options
		foreach ($this->modx_fields as $modx_fieldname => $tmp) {
			$pos = strpos($modx_fieldname, $xls_fieldname);
			if ($pos !== false) {
				return $modx_fieldname;
			}
		}
		// Next try some more daring options...
		// If we remove numbers, do any fields match up?
		$new_string = preg_replace('/[0-9]/','',$xls_fieldname);
		foreach ($this->modx_fields as $modx_fieldname => $tmp) {
			$pos = strpos($modx_fieldname, $new_string);
			if ($pos !== false) {
				return $modx_fieldname;
			}
		}
		// Try some old standards:
		switch($xls_fieldname) {
			case 'firstname':
			case 'first_name':
			case 'first name':
			case 'name':
			case 'lastname':
			case 'last_name':
			case 'last name':
			case 'middle':
			case 'middle initial':
			case 'title':
				return 'fullname';
				break;
		}
		
		// give up... fall back to extended data
		return 'extended';
	}
	/**
	 * edit extended method
	 * Edit the extended field key
	 *
	 * @return back to preview_data page
	 */
	public function edit_extended() {
		global $modx;
		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
		$data['file_path'] = $_SESSION['xlsimporter']['file_path'];
		$data['mapped_data'] = array();
		$data['email_msg'] = $modx->getOption('xls.emailMessage');

		// get the mapped data from POST var
		$posted_mapped_data = json_decode($_POST['mapped_data'], true);
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

		return load_view('preview_data.php', $data);
	}


	/**
	 * import_users
	 * $_POST['import_fields'] is a json from the preview page
	 * loop though and add it on user modx tbl and profile tbl
	 *
	 * @return unknown
	 */
	public function import_users() {

		global $modx;

		//require_once $modx->getOption('login.core_path',null,$modx->getOption('core_path').'components/login/').'model/login/login.class.php';
		/* 		echo '<pre>';
 		print_r($_POST);
 		die();*/


		$import_fields = json_decode($_POST['import_fields'], true);
/*		echo '<pre>';
 		print_r($import_fields);
 		die();*/


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

			$user->set('username', $ifield['email']);
			$user->set('active', (isset($ifield['active'])) ? 1 : 0  );
			$user->set('password', $temp_password);
			$user->set('class_key', $ifield['class_key']);
			$user->set('primary_group', $ifield['primary_group']);
			$user->set('sudo', (isset($ifield['sudo'])) ? 1 : 0  );

			$profile->set('email', $ifield['email']);
			$profile->set('internalKey', 0);
			$profile->set('fullname', $ifield['fullname']);
			$profile->set('phone', $ifield['phone']);
			$profile->set('mobilephone', $ifield['mobilephone']);
			$profile->set('blocked', (isset($ifield['blocked'])) ? 1 : 0  );
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
			$user->addOne($profile, 'Profile');

			/* save user */
			if (!$user->save()) {
				$modx->log(modX::LOG_LEVEL_ERROR, '[User Creation] Could not create user: '.$user->get('id').' with username: '.$user->get('username'));
			} else {
				if (isset($_POST['email_notification'])) {
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
				$modx->log(modX::LOG_LEVEL_ERROR, '[User Creation] Could not add newly created user to default "Clients" User Group!  User id '.$user->get('id').' with username: '.$user->get('username'));
			}

			$user_data = array(
				'username' => $ifield['email'],
				'added_msg' => $user_added,
				'email_msg' => $email_sent
			);
			array_push($data['result'], $user_data);
		}

		return load_view('import_result.php', $data);
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
		$data['file_path'] = $_SESSION['xlsimporter']['file_path'];
		$xls_fields = get('xls_fields', $_POST, array());
		$modx_fields = array_keys($this->modx_fields);
		$data['modx_defaults'] = $this->modx_fields;
		
		$data['mapped_fields'] = array();

		// set $_SESSION['xlsimporter']['prevmap'] for previous mapping
		$_SESSION['xlsimporter']['mapping'] = $xls_fields;

	
		// Get the array of xls fields that map to each MODx 
		// First we initialize the fields...
		foreach ($modx_fields as $mfield) {
			$pre_map[$mfield] = array();
		}
	
		// Then we gather all the XLS fields that were mapped to each MODx field.
		foreach ($xls_fields as $xfield_key => $xfield_val) {
			$pre_map[$xfield_val][] = $xfield_key;
		}

		$data['mapped_fields'] = $pre_map;

		return load_view('preview_map.php', $data);
	}


	/**
	 * $_POST contains keys for all MODX user fields.  The values in each spot of the array
	 * are number(s) (comma-separated), corresponding to the source column.  E.g.
	 * if the fullname is meant to be constructed from the first and second columns in the
	 * uploaded XLS file, then $_POST['fullname'] = '1,2';
	 *
	 * @return $mapped_data array
	 */
	public function preview_import() {
		global $modx;
		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
		$data['file_path'] = $_SESSION['xlsimporter']['file_path'];
		$data['email_msg'] = $modx->getOption('xls.emailMessage');
		$xls_fields = $this->read_file($data['file_path']);
		$header_row = $xls_fields[1];
		$posted_data = $_POST;
		// Here are the column mappings
/*		echo '<pre>';
		print_r($_SESSION['xlsimporter']['columns']);*/
	

		// remove the header fields
		unset($xls_fields[1]);

		$data['mapped_data'] = array();

		foreach ($xls_fields as $field) {

			$modx_data = array(); // for stuff that goes to the modx table

			foreach ($posted_data as $key => $value) {

				foreach ($value as $c) {
					$i = isset($_SESSION['xlsimporter']['columns'][$c]) ? $_SESSION['xlsimporter']['columns'][$c] : $c ;
					/*echo $_SESSION['xlsimporter']['columns'][$c];
					die();*/
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
						if ($key == 'extended') {
							// concatenate the mapped extended value and convert it to array
							$modx_data[$key]  .= isset($field[$i]) ? '||' . $field[$i]  : '||'."";
						} else {
							$modx_data[$key] .= isset($field[$i]) ? $field[$i] : $separator;
						}
					}
					else {
						$modx_data[$key] = isset($field[$i]) ? $field[$i] :  $c ;

					}
				}
			}


			if(isset($posted_data['extended'])) {
				if(isset($modx_data['extended'])) {
					// explode the $modx_data['extended'] to array
					$ext_val = explode('||', $modx_data['extended']);			
					
					// combined the xtended value and key
					$ext = array_combine($posted_data['extended'], $ext_val);
								
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

		return load_view('preview_data.php', $data);
	}


	/**
	 * This CMP controller function fires right after the user has uploaded a file OR after
	 * they are coming back to edit the file mappings.  It moves the file on uploads/ dir
	 * Or directs the user on the upload form again if no file was entered
	 *
	 * @return string $data HTML data for displaying in our MODX CMP
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

		
	
		// Did the user submit a form with a new file?
		if (isset($_FILES['uploaded_file']['name']) ) {

			if($this->__checked_target_path()) {
				$file_path = $this->target_path . basename( $_FILES['uploaded_file']['name']);
			} else {
				// Direct back on the uplaod form if no file entered
				$data['upload_msg'] = $modx->lexicon('upload_msg_error');
				return load_view('upload_form.php', $data);
			}
			
			if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path)) {
				$_SESSION['xlsimporter']['file_path'] = $file_path;
				$data['upload_msg'] = $modx->lexicon('upload_msg_success');
			} else {

				// Direct back on the uplaod form if no file entered
				$data['upload_msg'] = $modx->lexicon('upload_msg_error');
				return load_view('upload_form.php', $data);
			}
		}
		// Or are we coming back to this page to re-edit the mappings? 
		elseif (isset($_SESSION['xlsimporter']['file_path'])) {
			$file_path = $_SESSION['xlsimporter']['file_path'];
			$data['upload_msg'] = $modx->lexicon('upload_msg_success');
		}
		// Direct back on the uplaod form if no file entered
		else {
			$data['upload_msg'] = $modx->lexicon('upload_msg_error');
			return load_view('upload_form.php', $data);
		}

		// Get the fields on the xls fiel header row
		$xls_fields = array_slice( $this->read_file($file_path) , 0, 1);
		$xls_fields = $xls_fields[0];
		$data['xls_fields'] = $xls_fields;
		
		// Get available options for each field. This will re-select previous mappings (if available)
		// or it will take a best-guess as to the mapping.
		// We also stash column number mappings for later reference. REMEMBER: column numbers start with 1.
		$c = 1;
		foreach ($xls_fields as $f) {
			$_SESSION['xlsimporter']['columns'][$f] = $c;
			$selection = '';
			// Restore previous selection
			if (isset($_SESSION['xlsimporter']['mapping'][$f])) {
				$selection = $_SESSION['xlsimporter']['mapping'][$f];
			}
			// Or we take our best guess...
			else {
				$selection = $this->_guess_modx_fieldname($f);
			}
			$data['options'][$f] = $this->_get_mapping_options($data['modx_fields'],$selection);
			$c++;
		} 

		return load_view('map_fields.php', $data);
	}


	/**
	 * read_file method gets the content of the xls file
	 *
	 *
	 * @param string  $file
	 * @return array $all_fields
	 */
	public function read_file($file) {
		$excel = new Spreadsheet_Excel_Reader();
		$excel->read($file);
		$all_fields = $excel->sheets[0]['cells'];
		return $all_fields;
	}

	/**
	 * send_modx_email
	 *
	 * @param strign  email
	 * @param string  temp password
	 * @param string  fullname
	 * if user was added run this method
	 * @param unknown $email
	 * @param unknown $temp_password
	 * @param unknown $fullname
	 * @param unknown $email_msg
	 * @return unknown
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


	/**
	 * This controller function displays the upload form
	 *
	 * @return unknown
	 */
	public function upload_form() {
		// See http://rtfm.modx.com/display/revolution20/Adding+CSS+and+JS+to+Your+Pages+Through+Snippets
		global $modx;
		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
		$data['format'] = $modx->lexicon('xls_format');
		$data['choose'] = $modx->lexicon('choose');
		$data['upload'] = $modx->lexicon('xls_upload');

		return load_view('upload_form.php', $data);
	}

}


/*EOF*/