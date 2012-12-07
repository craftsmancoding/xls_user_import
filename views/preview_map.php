<script type="text/javascript">
	$(document).ready(function() {
		// applied Dragn and drop plugin to sort
		$(".sortable").tableDnD();
	});
</script>

<script type="text/javascript">
	// Add Separators
	$(document).ready(function() {
		$("a.separator").on('click', function(e){
			//get the li class ie usernameList
			var sep_class = $(this).parents('li').attr('class')
			sep_class = sep_class.split("List");
			var m_field = sep_class[0];
			// get data separator value
			var separator = $(this).data('separator');
			$('table.'+m_field+'-tbl').append('<tr style="cursor: move;"><td class="alert alert-info">' 
				+ separator  
				+ '<button type="button" class="close" data-dismiss="alert">×</button>'
				+ '<input name="'+ m_field +'[]" type="hidden" value="'+ separator +'">' 
				+ '<td><tr>').tableDnD();
			e.preventDefault();

		});
	});
</script>



<div id="modx-panel-workspace" class="x-plain container">
	<h2><?php print $data['title']; ?></h2>

<div class="well">

	<div class="alert alert-success">
     
      <strong>Well done!</strong> You can refine the mapped data on this page.
    </div>

	<form class="form-horizontal" method="post" action="<?php print $data['cmp_url']; ?>&p=preview_import">
		<ul class="modx-fields">

			<?php foreach ($data['mapped_fields'] as $modx_field => $xls_fields) : ?>
				<li class="<?php  echo $modx_field . 'List'; ?> well">

					<h2><?php  echo $modx_field; ?></h2>

						<table class="sortable <?php  echo $modx_field . '-tbl'; ?>">

							<?php $xfields = explode(',', $xls_fields) ;?>	
							<?php foreach ($xfields as $xfield) : ?>
								<?php $xfield_val = explode('|', substr($xfield, 1, -1)); ?>
					        	<tr id="<?php echo isset($xfield_val[0]) ? $xfield_val[0] :''; ?>">
					        		<td class="alert alert-info">
					        			<?php echo isset($xfield_val[1]) ? $xfield_val[1] :''; ?>
					        			<input name="<?php  echo $modx_field .'[]'; ?>" type="hidden" value="<?php echo isset($xfield_val[0]) ? $xfield_val[0] :''; ?>">
					        		</td>
					        	</tr>
					       	<?php endforeach; ?>

					       	<?php 
						       	if($modx_field != 'extended') :
						       		if($xls_fields != "" &&  count($xfields) >= 3) : 
					       	?>
					       		<div class="add-separators">
					       			<span style="font-weight:normal;color: #92999A;">Add Separators: </span>
					       			<a href="#" data-separator="SPACE" class="separator"><span class="label label-warning">Space</span></a>
					       		    <a href="#" data-separator="COMMA" class="separator"><span class="label label-warning">Comma</span></a>
					       		    <a href="#" data-separator="DASH" class="separator"><span class="label label-warning">Dash</span></a>
					       		    <a href="#" data-separator="COLON" class="separator"><span class="label label-warning">Colon</span></a>
					       		</div>
					       	<?php 
						       		endif; 
						       	endif; 
					       	?>
					    </table>

					    	<?php if($xls_fields == "") : ?>
					       	
					       		<?php  
					       			foreach ($data['modx_defaults'] as  $modx_default) {
					       				$this_default = explode(',', $modx_default);
					       				if($modx_field == $this_default[0]) {
					       					switch ($this_default[1]) {
					       						case 'text':
					       							echo '<input name="'.  $this_default[0] . '[]" type="'.  $this_default[1] .'" value="'.  $this_default[2] .'">';
					       							break;
					       						case 'checkbox':
					       							$checked = $this_default[2] == 1 ? 'checked' : '';
					       							echo '<input name="'.  $this_default[0] . '[]" type="'.  $this_default[1] .'" value="true" ' . $checked .' >';
					       							break;
					       						case 'date':
					       							echo '<input name="'.  $this_default[0] . '[]" type="'.  $this_default[1] .'" value="'.  $this_default[2] .'">';
					       							break;
					       						case 'textarea':
					       							echo '<textarea name="'. $this_default[0] .'[]" rows="4" cols="50"></textarea>';
					       							break;
					       					}
					       					
					       				}
					       			}
					       		?>
					       	<?php endif; ?>
				</li>

			<?php endforeach; ?>
		</ul>
		<input type="hidden"  name="file_path" value="<?php echo $data['file_path'] ?>">
		<input type="submit" id="submit" class="btn btn-custom" value="Submit Mapping">
	</form>
	

	

</div>

</div>
