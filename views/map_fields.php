<div id="modx-panel-workspace" class="x-plain container">
	<h2><?php print $data['title']; ?></h2>


<div class="well">
	<div class="alert alert-success"><?php print $data['upload_msg']; ?></div>
	
	<form class="form-horizontal" method="post" action="<?php print $data['cmp_url']; ?>&p=preview_map">

		 <legend><span class="xls_fields_head">XLS Fields</span><span class="xls_fields_head">Modx Fields</span></legend>



		<?php 		
		// Used to generate unique CSS ids		

		$i = 0;
		foreach ($data['xls_fields'] as $xls_field) :
		?>
			<div class="control-group">
				<label class="control-label" for="xls_field_<?php print $i; ?>"><?php print ucfirst($xls_field); ?></label>
				<div class="controls">
					<select id="xls_field_<?php print $i; ?>" name="xls_fields[<?php print $xls_field; ?>]">
						<?php print $data['options'][$xls_field]; ?>
					</select>
				</div>
			</div>
		<?php 
			$i++;
		endforeach; 
		?>

		<input type="submit" id="submit" class="btn btn-custom" value="Preview Mapping">
	</form>
</div>

</div>
