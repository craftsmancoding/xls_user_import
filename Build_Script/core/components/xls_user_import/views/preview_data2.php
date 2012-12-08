<script type="text/javascript">
	$(document).ready(function() {
		$(".inline").colorbox({inline:true, width:"300px"});
		$("#accordion").collapse();
	});
	
</script>

<div id="modx-panel-workspace" class="x-plain container">
	<h2><?php print $data['title']; ?></h2>

	<div class="well clearfix">

		<form class="preview" method="post" action="<?php print $data['cmp_url']; ?>&p=import_users">

			<div class="accordion" id="accordion2">
                <div class="accordion-group">
                  <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                      Collapsible Group Item #1
                    </a>
                  </div>
                  <div id="collapseOne" class="accordion-body collapse" style="height: 0px;">
                    <div class="accordion-inner">
                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                    </div>
                  </div>
                </div>
                <div class="accordion-group">
                  <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                      Collapsible Group Item #2
                    </a>
                  </div>
                  <div id="collapseTwo" class="accordion-body in collapse" style="height: auto;">
                    <div class="accordion-inner">
                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                    </div>
                  </div>
                </div>
                <div class="accordion-group">
                  <div class="accordion-heading">
                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
                      Collapsible Group Item #3
                    </a>
                  </div>
                  <div id="collapseThree" class="accordion-body collapse" style="height: 0px;">
                    <div class="accordion-inner">
                      Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                    </div>
                  </div>
                </div>
              </div>

		
		<table class="preview-tbl table table-bordered">
			<thead>
				<tr>
					<th>Fields</th>
					<th>Value</th>
				</tr>
			</thead>
			<tbody>

				<!-- <input type="hidden" name="import_fields" value="<?php //echo htmlentities(json_encode($data['mapped_data'])); ?>"> -->
				<?php foreach($data['mapped_data'] as $users) :?>
					
						<?php foreach($users as $field => $user) :?>
						<tr>

							<td><?php echo $field; ?></td>
							<td><div><?php echo $user; ?></div></td>
						</tr>
						<?php endforeach; ?>
					
				<?php endforeach; ?>
			</tbody>
		</table>
		<input type="hidden" = name="import_fields" value="<?php echo htmlentities(json_encode($data['mapped_data'])); ?>">
		<input type="submit" class="btn btn-custom" value="Import Users">
		<a href="<?php print $data['cmp_url']; ?>&p=process_upload&file=<?php echo basename($data['file_path']); ?>"  class="btn">Back to Mapping</a>
		</form>
		<a class='inline' href="#inline_content">Edit Extended Fields</a>
		<div style='display:none'>
			<div id='inline_content' style='padding:10px; background:#fff;'>
				<?php 
					$extended = json_decode($data['mapped_data'][0]['extended'],true);

				?>
			<form class="extended-form" method="post" action="<?php print $data['cmp_url']; ?>&p=edit_extended">
				  <fieldset>
				    <legend>Edit Extended FIelds</legend>
				    <span class="help-block">Replace the Existing Field Key</span><br><br>
				    <?php foreach ($extended as $xfield => $value) : ?>
				    	<p><input type="text" name ="<?php echo $xfield; ?>" placeholder="<?php echo $xfield; ?>"></p>
					<?php endforeach; ?>
					<input type="text" name="mapped_data" value="<?php echo htmlentities(json_encode($data['mapped_data'])); ?>">
					<input type="hidden" name="file_path" value="<?php echo basename($data['file_path']); ?>">
				    <button type="submit" class="btn">Submit</button>
				  </fieldset>
				</form>
			</div>
		</div>

	</div>

</div>