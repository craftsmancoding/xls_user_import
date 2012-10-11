<div id="modx-panel-workspace" class="x-plain container">
	<h2><?php print $data['title']; ?></h2>

<div class="well">

	<p><small><?php print $data['upload_msg']; ?></small></p><br>
	<div id="drag">
	<table class="fields">
		<colgroup>
			<col width="100"/>
		</colgroup>
		<tbody>
			<?php foreach ($data['header_fields'] as $key => $value) : ?>
			<tr><td><div class="drag" data-fieldkey="<?php echo $key; ?>"><?php echo $value; ?></div></td></tr>
			<?php endforeach; ?>
			
		</tbody>
	</table>
	
	<table class="map">
		<colgroup>
			<col width="100"/>
		</colgroup>
		<tbody>
			<tr>

				<td id="fname_map"></td>
				
			</tr>
			<tr>
				<td id="lname_map"></td>
				

			</tr>
			<tr>
				<td id="age_map"></td>
				
			</tr>
		</tbody>
	</table>
	
	<form method="post" action="<?php print $data['cmp_url']; ?>&p=map_fields">
		<input type="text" id="filepath" name="filepath" value="<?php echo $data['file_path']; ?>">
		<input type="text" id="firstname" name="firstname" value="">
		<input type="text" id="lastname" name="lastname" value="">
		<input type="text" id="age" name="age" value="">
		<input type="submit" id="submit" value="Submit">
	</form>

</div>

</div>

</div>

<script type="text/javascript">
	jQuery(document).ready(function(){
  		$('#submit').on('click', function() {
  			setValue('fname_map','firstname');
  			setValue('lname_map','lastname');
  			setValue('age_map','age');  			
  		});

  		function setValue(map, fieldname) {
  			 var childElems = $('td#' + map ).children();
  			 var field = [];
  			 $.each(childElems, function() {
  			 	field.push($(this).data('fieldkey'));
			 });
			 $('#' + fieldname).val(field);
  		}
	});
</script>