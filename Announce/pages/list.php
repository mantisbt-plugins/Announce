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
<form action="<?php echo plugin_page("list_action") ?>" method="post">
<?php echo form_security_field("plugin_Announce_list_action") ?>
<table class="width100" align="center">

<tr>
<td class="form-title" colspan="3"><?php echo plugin_lang_get("list_title") ?></td>
<td class="right" colspan="2"><?php if ($admin) { print_bracket_link(plugin_page("config_page"), plugin_lang_get("config")); } ?></td>
</tr>

<tr class="row-category">
<td width="4%"></td>
<td><?php echo plugin_lang_get("title") ?></td>
<td><?php echo plugin_lang_get("message") ?></td>
<td><?php echo plugin_lang_get("project") ?></td>
<td><?php echo plugin_lang_get("location") ?></td>
</tr>

<?php foreach($messages as $message_id => $message): ?>
<tr <?php echo ($color = helper_alternate_class()) ?>>
<?php
	$context_count = count($message->contexts);
	$rowspan = $context_count > 0 ? $context_count : 1;
	$first = true;
?>
<td class="center" rowspan="<?php echo $rowspan ?>"><input type="checkbox" name="message_list[]" value="<?php echo $message_id ?>"/></td>
<td rowspan="<?php echo $rowspan ?>"><?php echo $message->title ?></td>
<td rowspan="<?php echo $rowspan ?>"><?php echo $message->message ?></td>
<?php if ($context_count > 0): $first = true; foreach($message->contexts as $context): ?>
<?php if (!$first): ?>
<tr <?php echo $color ?>>
<?php endif ?>
<td class="center"><?php echo string_display_line(project_get_name($context->project_id)) ?></td>
<td class="center"><?php echo $locations[$context->location] ?></td>
<?php $first = false; endforeach; else: ?>
<td colspan="2"></td>
<?php endif ?>
</tr>
<?php endforeach ?>

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

</table>
</form>

<br/>
<form action="<?php echo plugin_page("create") ?>" method="post">
<?php echo form_security_field("plugin_Announce_create") ?>
<table class="width75" align="center">

<tr>
<td class="form-title" colspan="2"><?php echo plugin_lang_get("create_title") ?></td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo plugin_lang_get("title") ?></td>
<td><input name="title"/></td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo plugin_lang_get("message") ?></td>
<td><textarea name="message" cols="70" rows="4"></textarea></td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo plugin_lang_get("location") ?></td>
<td><select name="location"><?php Announce::print_location_option_list() ?></select></td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo plugin_lang_get("project") ?></td>
<td><select name="project_id"><?php print_project_option_list() ?></select></td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo plugin_lang_get("access") ?></td>
<td><select name="access"><?php print_enum_string_option_list("access_levels", VIEWER ) ?></select>
<span class="small"><?php echo plugin_lang_get("access_help") ?></span></td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo plugin_lang_get("ttl") ?></td>
<td><label class="announcement_ttl"><input name="ttl" size="8" value="0"/>
<span class="small"><?php echo plugin_lang_get("ttl_help") ?></span></label></td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo plugin_lang_get("dismissable") ?></td>
<td><input type="checkbox" name="dismissable" checked="checked"/></td>
</tr>

<tr>
<td class="center" colspan="2"><input type="submit" value="<?php echo plugin_lang_get("action_create") ?>"/></td>
</tr>

</table>
</form>

<?php
html_page_bottom();

