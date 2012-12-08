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
			$('table.'+m_field+'-tbl').append('<tr style="cursor: move;"><td class="field-sort">' 
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


			<?php 

				foreach ($data['mapped_fields'] as $modx_field => $xls_fields) : 
				$count_xfields = count($xls_fields);
			?>
			<?php if($modx_field != "") : ?>
				<li class="<?php  echo $modx_field . 'List'; ?> well">

					<h2><?php  echo $modx_field; ?></h2>

						<table class="sortable <?php  echo $modx_field . '-tbl'; ?>">

							<?php foreach ($xls_fields as $xfield) : ?>
									
					        	<tr>
					        		<td class="field-sort">
					        			<?php echo $xfield; ?>
					        			<input name="<?php  echo $modx_field .'[]'; ?>" type="hidden" value="<?php echo $xfield; ?>">
					        		</td>
					        	</tr>
					       	<?php endforeach; ?>

					      	<?php if($count_xfields >= 2 ) : ?>
					      		<?php 	if($modx_field != 'extended') : ?>
						       		<div class="add-separators">
						       			<span style="font-weight:normal;color: #92999A;">Add Separators: </span>
						       			<a href="#" data-separator="SPACE" class="separator"><span class="label label-warning">Space</span></a>
						       		    <a href="#" data-separator="COMMA" class="separator"><span class="label label-warning">Comma</span></a>
						       		    <a href="#" data-separator="DASH" class="separator"><span class="label label-warning">Dash</span></a>
						       		    <a href="#" data-separator="COLON" class="separator"><span class="label label-warning">Colon</span></a>
						       		</div>
					       		<?php endif; ?>
					       	<?php endif; ?>
					       
					    </table>

					    	<?php if( $modx_field != 'extended' && $count_xfields == 0 ) : ?>
					       	
					       		<?php  

					       			foreach ($data['modx_defaults'] as  $mfield => $modx_default) {

					       				if($modx_field == $mfield) {
					       					
					       					switch ($modx_default['type']) {
					       						case 'text':
					       							echo '<input name="'.  $mfield . '[]" type="'.  $modx_default['type'] .'" value="'.  $modx_default['value'] .'">';
					       							break;
					       						case 'checkbox':
					       							$checked = $modx_default['value'] == true ? 'checked' : '';
					       							echo '<input name="'.  $mfield . '[]" type="'.  $modx_default['type'] .'" value="true" ' . $checked .' >';
					       							break;
					       						case 'date':
					       							echo '<input name="'.  $mfield . '[]" type="'.  $modx_default['type'] .'" value="'.  $modx_default['value'] .'">';
					       							break;
					       						case 'textarea':
					       							echo '<textarea name="'. $mfield .'[]" rows="4" cols="50"></textarea>';
					       							break;
					       						case 'select' :
					       							$select_vals = explode(',', $modx_default['value']);
					       							$select = "<select name='{$mfield}[]'>";
					       							foreach ($select_vals as $opt) {
					       								$select .= "<option value='{$opt}'>{$opt}</option>";
					       							}
					       							$select .="</select>";
					       							echo $select;
					       							break;
					       					}
					       					
					       				}
					       			}
					       		?>
					       	<?php endif; ?>


					    </li>
					  <?php endif; ?>

			<?php endforeach; ?>
				
		</ul>
		<a href="<?php print $data['cmp_url']; ?>&p=process_upload"  class="btn">Back to Mapping</a>
		<input type="submit" id="submit" class="btn btn-custom" value="Submit Mapping">
	</form>
	

	

</div>

</div>
