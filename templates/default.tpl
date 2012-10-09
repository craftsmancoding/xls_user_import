<link rel="stylesheet" type="text/css" href="../assets/components/xls_user_import/css/mgr.css" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="../assets/components/xls_user_import/js/script.js"></script>


<div id="modx-panel-workspace" class="x-plain container">
	<h2>XLS Userss Importer</h2>


<div class="well">
	<form enctype="multipart/form-data" method="POST" action="upload.php">
		  <legend>Upload XLS file</legend>
		  	<p>
			  <small>The file must be version 97-2003 for it to work properly.</small>
			</p><br>
			
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			<input type="file" id="file">

      		<div class="dummyfile">
		      <input id="filename" type="text" class="input disabled" name="filename" readonly>
		      <a id="fileselectbutton" class="btn">Choose</a>
      		</div>
      		<input type="submit"class="btn btn-custom" value="Upload File" />

		</form>
</div>

</div>