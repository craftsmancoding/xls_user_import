<?php

require_once dirname(dirname(__FILE__)).'/core/config/config.inc.php';

require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modx();
$modx->initialize('mgr');

//set message 
$user_added = 'FAILED';
$profile_added = 'FAILED';
$email_sent = 'FAILED';

require_once $modx->getOption('login.core_path',null,$modx->getOption('core_path').'components/login/').'model/login/login.class.php';


/*			 $firstname = isset($val[1]) ? $val[1] : '';
          $lastname = isset($val[2]) ? $val[2] : '';
          $address = $address1 . ' ' . $address2 . ' ' . $address3;
          $city = isset($val[6]) ? $val[6] : '';
          $state = isset($val[7]) ? $val[7] : '';
          $zip = isset($val[8]) ? $val[8] : '';
          $country = isset($val[9]) ? $val[9] : '';
          $email = isset($val[10]) ? $val[10] : '';
          $phone = isset($val[11]) ? $val[11] : '';
*/
// Get us some settings;

$reply_to_email = 'daniel@craftsmancoding.com';
$email_chunk = 'emailChunk';
$client_user_group_id = 123; // Id of a User Group    

$Login = new Login($modx);
$user = $modx->newObject('modUser');
$profile = $modx->newObject('modUserProfile');

$temp_password = $Login->generatePassword();

$user->set('username',$email);
$user->set('active',1);
$user->set('password', $temp_password);

$profile->set('email', $email);
$profile->set('internalKey',0);
$profile->set('fullname', $firstname . ' ' . $lastname);
$profile->set('phone', $phone);
$profile->set('address', $address);
$profile->set('country', $country);
$profile->set('city', $city);
$profile->set('state', $state);
$profile->set('zip', $zip);
$profile->set('extended', $extended_json);
$user->addOne($profile,'Profile');

/* save user */
if (!$user->save()) {
    $modx->log(modX::LOG_LEVEL_ERROR,'[User Creation] Could not create user: '.$user->get('id').' with username: '.$user->get('username'));
} else {
	$user_added = 'SUCCESS';
	$profile_added = 'SUCCESS';
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

$name = $email;
$subject = 'New user created';

// This function sends an email geared to solicit an activation clickback
// $Login->sendEmail($email,$name,$subject);
// so instead, we do it manually:

$hash = array();
$hash['url'] = $modx->makeUrl(53); // location of login page
$hash['username'] = $name;
$hash['password'] = $temp_password;

$message = $modx->getChunk($email_chunk, $hash);

$modx->getService('mail', 'mail.modPHPMailer');


$modx->mail->set(modMail::MAIL_BODY, $message);
$modx->mail->set(modMail::MAIL_FROM, $reply_to_email);
$modx->mail->set(modMail::MAIL_FROM_NAME, 'Craftsmancoding');
$modx->mail->set(modMail::MAIL_SENDER, $reply_to_email);
$modx->mail->set(modMail::MAIL_SUBJECT, 'New Username and Password from Craftsmancoding');
$modx->mail->address('to', $email, $firstname);
$modx->mail->address('reply-to', $reply_to_email);
$modx->mail->setHTML(true);
$sent = $modx->mail->send();

if ($sent) {
    $email_sent = 'SUCCESS';
} else {
    $email_sent = 'FAILED';
}


$modx->mail->reset();    

?>