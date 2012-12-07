<div id="modx-panel-workspace" class="x-plain container">
	<h2><?php print $data['title']; ?></h2>


<div class="well">
	<div class="alert alert-success"><?php print $data['upload_msg']; ?></div>
	
	<form class="form-horizontal" method="post" action="<?php print $data['cmp_url']; ?>&p=preview_map">

		 <legend><span class="xls_fields_head">XLS Fields</span><span class="xls_fields_head">Modx Fields</span></legend>



		<?php 
			// initialize $x_fld_count to 0
			$x_fld_count = 0;
			foreach ($data['xls_fields'] as $xfield) : 
			// increament $x_fld_count first loop set to 1
			// $x_fld_count determines the column number of the field from xls file
			// it will be used on reading data on xls class
			$x_fld_count++;
		?>
				 <div class="control-group">
				    <label class="control-label" for="xls_field_<?php echo $x_fld_count; ?>"><?php echo ucfirst($xfield); ?></label>
				    <div class="controls">
				     <select id="xls_field_<?php echo $x_fld_count; ?>" name="xls_fields[<?php echo $xfield; ?>][<?php echo $x_fld_count;  ?>]">
						    <option value=""></option>
						    <?php foreach ($data['modx_fields'] as $mfield) : ?>
						    	<option value="<?php echo $mfield; ?>" <?php echo auto_detect_field($xfield, $mfield); ?>><?php echo $mfield;  ?></option>
						  	<?php endforeach; ?>
						</select>
				    </div>

				  </div>
		<?php endforeach; ?>

	 	<input type="hidden"  name="file_path" value="<?php echo $data['file_path'] ?>">

		<input type="submit" id="submit" class="btn btn-custom" value="Preview Mapping">
	</form>
</div>

</div>
