<div id="modx-panel-workspace" class="x-plain container">
	<h2><?php print $data['title']; ?></h2>


<div class="well">
	<form enctype="multipart/form-data" method="POST" action="">
		  <legend><?php print $data['upload']; ?></legend>
		  	<p>
			  <small><?php print $data['format']; ?></small>
			</p><br>
			
			<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
			<input type="file" id="file">

      		<div class="dummyfile">
		      <input id="filename" type="text" class="input disabled" name="filename" readonly>
		      <a id="fileselectbutton" class="btn"><?php print $data['choose']; ?></a>
      		</div>
      		<input type="submit"class="btn btn-custom" value="Upload File" />

		</form>
</div>

	<a href="<?php print $data['cmp_url']; ?>&p=other_page">Other Page</a>

</div>