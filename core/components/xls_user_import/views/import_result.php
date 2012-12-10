<div id="modx-panel-workspace" class="x-plain container">
	<h2><?php print $data['title']; ?></h2>

	<div class="well">
	
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Username</th>
					<th>User Added</th>
					<th>Email Sent</th>
					
				</tr>
			</thead>
			<tbody>

				<?php foreach ($data['result'] as $user) : ?>
					<tr>
						<td><?php echo $user['username']; ?></td>
						<td><div style="text-align:center;" class="<?php echo $user['added_msg'] == 'success' ? 'btn-success' : 'btn-danger' ?>"><?php echo $user['added_msg']; ?></div></td>
						<td><div style="text-align:center;" class="<?php echo $user['email_msg'] == 'success' ? 'btn-success' : 'btn-danger' ?>"><?php echo $user['email_msg']; ?></div></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

</div>