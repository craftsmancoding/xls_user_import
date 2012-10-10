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
				<td></td>
			</tr>
			<tr>
				<td></td>

			</tr>
			<tr>
				<td></td>
			</tr>
		</tbody>
	</table>
	<!-- here will be new table loaded -->
	<span id="load_content"/>
</div>

</div>

</div>