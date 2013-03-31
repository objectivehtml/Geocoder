<h3>Geocoder Instance</h3>

<table class="mainTable padTable" cellpadding="0" cellspacing="0" border="0">
	<thead>
		<tr>
			<th>Channel Name(s)</th>
			<th>Geocode Field(s)</th>
			<th>Map</th>
			<th>Latitude</th>
			<th>Longitude</th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	<?php if($settings->num_rows() > 0): ?>
		<?php foreach($settings->result() as $setting): ?>
			<?php $geocode_fields = json_decode($setting->geocode_fields); ?>
			<tr>
				<td><?php echo $setting->channel_names?></td>
				<td>
					<?php foreach($geocode_fields as $index => $field): ?>
						<?php echo $field->field_name?> <br>
					<?php endforeach; ?>
				</td>
				<td><?php echo $setting->gmap_field_name?></td>
				<td><?php echo $setting->latitude_field_name?></td>
				<td><?php echo $setting->longitude_field_name?></td>
				<td><a href="<?php echo $url . '&method=edit_setting&id='.$setting->id?>">Edit</a></td>
				<td><a href="<?php echo $url . '&method=delete_setting&id='.$setting->id?>">Delete</a></td>
			</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<tr><td colspan="7">There are no geocoder instances at this time. <a href="<?php echo $new_setting_url?>">New Geocoder Instance</a></td></tr>
	<?php endif; ?>
	</tbody>
</table>