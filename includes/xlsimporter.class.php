<?php/**
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
		$modx->regClientCSS(MODX_ASSETS_URL.'components/xls_user_import/css/mgr.css');
		$modx->regClientScript('https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		$modx->regClientScript(MODX_ASSETS_URL.'components/xls_user_import/js/script.js');
		
		$data = array();
		$data['title'] = $modx->lexicon('xls_user_import');
		$data['format'] = $modx->lexicon('xls_format');
		$data['choose'] = $modx->lexicon('choose');
		$data['upload'] = $modx->lexicon('xls_upload');
		
		return load_view('upload_form.php',$data);
	}
	
	
	public function other_page() {
		return load_view('other_page.php',$data);
	}
}


/*EOF*/