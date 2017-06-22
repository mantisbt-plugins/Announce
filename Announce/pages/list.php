<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

access_ensure_global_level(plugin_config_get("manage_threshold"));
$admin = access_has_global_level(config_get("manage_plugin_threshold"));

$messages = AnnounceMessage::clean(AnnounceMessage::load_all(true), "view");
$locations = Announce::locations();

html_page_top(plugin_lang_get("list_title"));
print_manage_menu();
?>

<script type="text/javascript" src="<?php echo plugin_file("list_action.js") ?>"></script>

<br/>

<div class="form-container">
<form action="<?php echo plugin_page("list_action") ?>" method="post">
	<fieldset>
		<legend><?php echo plugin_lang_get("list_title") ?></legend>
		<?php if ($admin): ?>
			<div class="floatright">
				<?php print_bracket_link(plugin_page("config_page"), plugin_lang_get("config")); ?>
			</div>
		<?php endif; ?>

		<?php echo form_security_field("plugin_Announce_list_action") ?>

		<table>
			<thead>
				<tr class="row-category">
					<th width="4%"></th>
					<th><?php echo plugin_lang_get("title") ?></th>
					<th><?php echo plugin_lang_get("message") ?></th>
					<th><?php echo plugin_lang_get("project") ?></th>
					<th><?php echo plugin_lang_get("location") ?></th>
				</tr>
			</thead>

			<tbody>
<?php
	foreach($messages as $message_id => $message) {
		$context_count = count($message->contexts);
		$odd_rows = $context_count & 1;
		$rowspan = max( $context_count, 1 ) + 1 - $odd_rows;
		$first = true;
?>
				<tr>
					<td class="center" rowspan="<?php echo $rowspan ?>">
						<input type="checkbox" name="message_list[]" value="<?php echo $message_id ?>"/>
					</td>
					<td rowspan="<?php echo $rowspan ?>"><?php echo $message->title ?></td>
					<td rowspan="<?php echo $rowspan ?>"><?php echo $message->message ?></td>
<?php
		if ($context_count > 0) {
			$first = true;
			foreach( $message->contexts as $context ) {
				if( !$first ) {
					echo '<tr>';
				}
				?>
				<td class="center"><?php echo string_display_line( project_get_name( $context->project_id
					)
					) ?></td>
				<td class="center"><?php echo $locations[$context->location] ?></td>
				<?php
				$first = false;
			}

			# Add a dummy row to ensure proper color alternation
			if( !$odd_rows ) {
				echo '<tr><td colspan="2"></td></tr>';
			}
		} else {
?>
						<td colspan="2"></td>
<?php
		}
?>
					</tr>
<?php
	}
?>
			</tbody>

			<tfoot>
					<tr>
						<td class="center"><input class="announce_select_all" type="checkbox"/></td>
						<td colspan="2">
							<select class="announce_select_action" name="action">
								<option value="edit"><?php echo plugin_lang_get("action_edit") ?></option>
								<option value="delete"><?php echo plugin_lang_get("action_delete") ?></option>
							</select>
							<input class="announce_select_submit" type="submit" value="<?php echo plugin_lang_get("action_go") ?>"/>
						</td>
					</tr>
			</tfoot>

		</table>
	</fieldset>
</form>
</div>

<br/>
<div class="form-container">
<form action="<?php echo plugin_page("create") ?>" method="post">
	<fieldset>
		<legend><?php echo plugin_lang_get("list_title") ?></legend>

		<?php echo form_security_field("plugin_Announce_create") ?>

		<table>
			<tbody>
				<tr>
					<th class="category"><?php echo plugin_lang_get("title") ?></th>
					<td><input name="title"/></td>
				</tr>

				<tr>
					<th class="category"><?php echo plugin_lang_get("message") ?></th>
					<td><textarea name="message" cols="70" rows="4"></textarea></td>
				</tr>

				<tr>
					<td class="category"><?php echo plugin_lang_get("location") ?></td>
					<td>
						<select name="location">
							<?php Announce::print_location_option_list(count($locations) == 1 ? end($locations) : null) ?>
						</select>
					</td>
				</tr>

				<tr>
					<td class="category"><?php echo plugin_lang_get("project") ?></td>
					<td><select name="project_id"><?php print_project_option_list() ?></select></td>
				</tr>

				<tr>
					<td class="category">
						<?php echo plugin_lang_get("access") ?>
						<br>
						<span class="small"><?php echo plugin_lang_get("access_help") ?></span>
					</td>
					<td>
						<select name="access">
							<?php print_enum_string_option_list("access_levels", VIEWER ) ?>
						</select>
					</td>
				</tr>

				<tr>
					<td class="category">
						<?php echo plugin_lang_get("ttl") ?>
						<br>
						<span class="small"><?php echo plugin_lang_get("ttl_help") ?></span>
					</td>
					<td>
						<input name="ttl" size="8" value="0"/>
					</td>
				</tr>

				<tr>
					<td class="category"><?php echo plugin_lang_get("dismissable") ?></td>
					<td><input type="checkbox" name="dismissable" checked="checked"/></td>
				</tr>
			</tbody>

			<tfoot>
				<tr>
					<td class="center" colspan="2">
						<input type="submit" value="<?php echo plugin_lang_get("action_create") ?>"/>
					</td>
				</tr>
			</tfoot>

		</table>
	</fieldset>
</form>
</div>

<?php
html_page_bottom();
