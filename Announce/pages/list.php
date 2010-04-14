<?php

# Copyright (c) 2010 John Reese
# Licensed under the MIT license

access_ensure_global_level(plugin_config_get("manage_threshold"));
$admin = access_has_global_level(config_get("manage_plugin_threshold"));

$messages = AnnounceMessage::clean(AnnounceMessage::load_all(), "view");

html_page_top(plugin_lang_get("list_title"));
print_manage_menu();
?>

<br/>
<table class="width90" align="center">

<tr>
<td class="form-title" colspan="2"><?php echo plugin_lang_get("list_title") ?></td>
<td class="right" colspan="2"><?php if ($admin) { print_bracket_link(plugin_page("config_page"), plugin_lang_get("config")); } ?></td>
</tr>

<tr class="row-category">
<td></td>
<td><?php echo plugin_lang_get("title") ?></td>
<td><?php echo plugin_lang_get("message") ?></td>
<td><?php echo plugin_lang_get("timestamp") ?></td>
</tr>

<?php foreach($messages as $message_id => $message): ?>
<tr <?php echo helper_alternate_class() ?>>
<td></td>
<td><?php echo $message->title ?></td>
<td><?php echo $message->message ?></td>
<td><?php echo $message->timestamp ?></td>
</tr>
<?php endforeach ?>

</table>

<br/>
<form action="<?php echo plugin_page("create") ?>" method="post">
<?php echo form_security_field("plugin_Announce_create") ?>
<table class="width90" align="center">

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
<td><select name="location">
<option value=""><?php echo plugin_lang_get("select_one") ?></option>
<?php foreach(Announce::locations() as $loc => $locname): ?>
<option value="<?php echo $loc ?>"><?php echo $locname ?></option>
<?php endforeach ?>
</select></td>
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

