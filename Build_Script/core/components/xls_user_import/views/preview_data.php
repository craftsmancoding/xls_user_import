<script type="text/javascript">
	$(document).ready(function() {
		$(".inline").colorbox({inline:true, width:"300px"});
	});
	
</script>

<div id="modx-panel-workspace" class="x-plain container">
	<h2><?php print $data['title']; ?></h2>
	<div class="well">

		<div class="alert alert-success">
     
	      <strong>Data Preview!</strong> Please Click on each email to see the whole data.
	    </div>

		<form class="preview" method="post" action="<?php print $data['cmp_url']; ?>&p=import_users">

		 <div class="accordion" id="accordion2"> 
			<?php foreach($data['mapped_data'] as $key => $users) :?>
				
		            <div class="accordion-group">  
		              <div class="accordion-heading">  
		                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#<?php echo $key; ?>">  
		                 Username: <span class="username_field"><?php echo $users['email']; ?> </span>
		                </a>  
		              </div>  
		              <div id="<?php echo $key; ?>" class="accordion-body collapse">  
		                <div class="accordion-inner">  
		                 
		                 		<table class="preview-tbl table table-bordered">
									<thead>
										<tr>
											<th>Fields</th>
											<th>Value</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach($users as $field => $user) :?>
										<tr>

											<td><?php echo $field; ?></td>
											<td><div><?php echo $user; ?></div></td>
										</tr>
										<?php endforeach; ?>
											
									</tbody>
								</table>

		                </div>  
		              </div>  
		            </div>  
            	
			<?php endforeach; ?>
          </div>  
          <div class="well">

          	<legend>Email Notification Controls</legend>
          	 	<p>
				    <label class="control-label" for="inputEmail">Email Message</label>
				    <textarea name="email_msg" id="email_msg" class="input-xxlarge" rows="10"><?php echo  ltrim($data['email_msg']); ?></textarea>
			  	</p>
			  	<p>
	                <label class="checkbox">
	                  <input type="checkbox" name="email_notification" value="1"> Send email to each user with new password?
	                </label>
                <p>
              </div>

          </div>	
        
		<input type="hidden" name="import_fields" value="<?php echo htmlentities(json_encode($data['mapped_data'])); ?>">
		<?php if(isset($data['mapped_data'][0]['extended'])) : ?>
			<a class='inline btn' href="#inline_content">Edit Extended Fields</a>
		<?php endif; ?>

		<input type="submit" class="btn btn-custom" value="Import Users">
		<a href="<?php print $data['cmp_url']; ?>&p=process_upload"  class="btn">Back to Mapping</a>
		</form>
		
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
					<input type="hidden" name="mapped_data" value="<?php echo htmlentities(json_encode($data['mapped_data'])); ?>">
					<input type="hidden" name="file_path" value="<?php echo basename($data['file_path']); ?>">
				    <button type="submit" class="btn btn-custom">Submit</button>
				  </fieldset>
				</form>
			</div>
		</div>

		

	</div>

</div>