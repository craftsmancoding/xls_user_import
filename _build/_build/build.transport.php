<?php
$tstart = explode(' ', microtime());
$tstart = $tstart[1] + $tstart[0];
set_time_limit(0);

/* define package names */
define('PKG_NAME','XLS User Importer');
define('PKG_NAME_LOWER','xls_user_import');
define('PKG_VERSION','1.0');
define('PKG_RELEASE','pl');

// Change the path to the core/config
require_once(dirname(dirname(dirname(__FILE__))).'/core/config/config.inc.php');




require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
 
$modx= new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
 
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');



$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'xls_user_import',
    'parent' => '0',
    'controller' => 'index',
),'',true,true);

$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'xls_user_import',
    'parent' => 'components',
    'description' => 'xls_user_import.menu_desc',
    'action' => 1,
    'menuindex' => '0',
    'params' => '',
    'handler' => '',
),'',true,true);

$menu->addOne($action);

$vehicle= $builder->createVehicle($menu,array (
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'text',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'Action' => array (
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
        ),
    ),
));
$builder->putVehicle($vehicle);

//------------------------------------------------------------------------------
//! System Settings
//------------------------------------------------------------------------------

$setting_val ='Dear [[+fullname]],

A new account has been created for you at [[++site_name]]! 

Your username is [[+username]].

Your new password is [[+password]].You can login at [[++site_url]].';


$setting = $modx->newObject('modSystemSetting');
$setting->fromArray(array(
    'key' => 'xls.emailMessage',
    'value' => $setting_val,
    'xtype' => 'textarea',
    'namespace' => PKG_NAME_LOWER,
    'area' => 'default',
    'name' => 'XLS Email Message',
),'',true,true);


$vehicle= $builder->createVehicle($setting,array (
     xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,  
));

$builder->putVehicle($vehicle);

//------------------------------------------------------------------------------
//! DOCS
// CORE COMPONENTS
// Copy over related files
//------------------------------------------------------------------------------
$vehicle->resolve('file',array(
    'source' => MODX_BASE_PATH . PKG_NAME_LOWER . '/core/components/' . PKG_NAME_LOWER,
    'target' => "return MODX_CORE_PATH . 'components/';",
));

//------------------------------------------------------------------------------
//! DOCS
// ASSETS COMPONENTS
// Copy over related files
//------------------------------------------------------------------------------
$vehicle->resolve('file',array(
    'source' => MODX_BASE_PATH . PKG_NAME_LOWER . '/assets/components/' . PKG_NAME_LOWER,
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));



$builder->putVehicle($vehicle);



/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();
 
$tend= explode(" ", microtime());
$tend= $tend[1] + $tend[0];
$totalTime= sprintf("%2.4f s",($tend - $tstart));
$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");
exit ();