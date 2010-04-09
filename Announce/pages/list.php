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
<table class="width60" align="center">

<tr>
<td class="form-title" colspan="2"><?php echo plugin_lang_get("create_title") ?></td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo plugin_lang_get("title") ?></td>
<td><input name="title"/></td>
</tr>

<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo plugin_lang_get("message") ?></td>
<td><textarea name="message" cols="80" rows="4"></textarea></td>
</tr>

<tr>
<td class="center" colspan="2"><input type="submit" value="<?php echo plugin_lang_get("action_create") ?>"/></td>
</tr>

</table>
</form>

<?php
html_page_bottom();

