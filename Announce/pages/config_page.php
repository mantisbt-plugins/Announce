<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

access_ensure_global_level(config_get("manage_plugin_threshold"));

html_page_top(plugin_lang_get("plugin_title"));
print_manage_menu();
?>

<br/>
<div class="form-container width60">
<form action="<?php echo plugin_page("config") ?>" method="post">
	<fieldset>
		<legend><?php echo plugin_lang_get("config_title") ?></legend>

		<?php echo form_security_field("plugin_Announce_config") ?>

		<table>
			<tbody>
				<tr>
					<th class="category">
						<?php echo plugin_lang_get( 'config_manage_threshold' ) ?>
					</th>
					<td>
						<select name="manage_threshold">
							<?php print_enum_string_option_list( 'access_levels', plugin_config_get( 'manage_threshold' ) ) ?>
						</select>
					</td>
				</tr>
			</tbody>


			<tfoot>
				<tr>
					<td class="center" colspan="2">
						<input type="submit" value="<?php echo plugin_lang_get("action_update") ?>"/>
					</td>
				</tr>

			</tfoot>
		</table>
	</fieldset>
</form>
</div>

<?php
html_page_bottom();
