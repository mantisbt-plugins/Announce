<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

access_ensure_global_level( plugin_config_get( 'manage_threshold' ) );

$t_message_list = gpc_get_int_array( 'message_list', array() );

if( count( $t_message_list ) < 1 ) {
	print_header_redirect( plugin_page( 'list', true ) );
}

$t_messages = AnnounceMessage::load_by_id( $t_message_list, true );
$t_messages = AnnounceMessage::clean( $t_messages, AnnounceMessage::TARGET_FORM );

layout_page_header( plugin_lang_get( 'edit_title' ) );
layout_page_begin();
?>

<script type="text/javascript" src="<?php echo plugin_file( 'list_action.js' ) ?>"></script>

<div class="col-md-12 col-xs-12">
<div class="space-10"></div>

<div class="form-container">
<form action="<?php echo plugin_page( 'list_action_update' ) ?>" method="post">

	<?php echo form_security_field( 'plugin_Announce_list_action_update' ); ?>

	<div class="widget-box widget-color-blue2">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-file-o"></i>
				<?php echo plugin_lang_get( 'edit_title' ); ?>
			</h4>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="table-responsive">

					<div class="widget-toolbox padding-8 clearfix">
						<a class="btn btn-xs btn-primary btn-white btn-round"
						   href="<?php echo plugin_page( 'list' ); ?>">
							<?php echo lang_get( 'back_link' ); ?>
						</a>
					</div>

					<table class="table table-striped table-bordered table-condensed">
						<tbody>
<?php
$t_first = true;
foreach( $t_messages as $t_message_id => $t_message ) {
	$t_context_count = count( $t_message->contexts );
	if( !$t_first ) {
?>
							<tr class="spacer"><td></td></tr>
<?php } ?>
							<tr>
								<td class="center announce_list_<?php echo $t_message_id ?>"
									rowspan="<?php echo 3 + $t_context_count ?>">
									<input type="checkbox" name="message_list[]"
										   value="<?php echo $t_message_id ?>"
										   checked="checked"/>
								</td>
								<td class="category" colspan="2"><?php echo plugin_lang_get( 'title' ) ?></td>
								<td colspan="4">
									<input name="title_<?php echo $t_message_id ?>"
										   type="text" size="50"
										   value="<?php echo $t_message->title ?>"
									/>
									<div id="warning_last_context_<?php echo $t_message_id ?>"
										 class="alert alert-warning warning-last-context">
										<?php echo plugin_lang_get( 'delete_last_context' ); ?>
									</div>
								</td>
							</tr>

							<tr>
								<td class="category" colspan="2"><?php echo plugin_lang_get( 'message' ) ?></td>
								<td colspan="4">
									<textarea name="message_<?php echo $t_message_id ?>"
											  cols="70" rows="4"><?php
										echo $t_message->message
									?></textarea>
								</td>
							</tr>

							<tr class="row-category">
								<td class="category center">
									<a class="announce_add_context" href="#" data-message-id="<?php echo $t_message_id ?>">
										<img src="<?php echo plugin_file("add.png") ?>" alt="+" border="0"/>
									</a>
								</td>
								<td class="category"><?php echo plugin_lang_get( 'location' ) ?></td>
								<td class="category"><?php echo plugin_lang_get( 'project' ) ?></td>
								<td class="category"><?php echo plugin_lang_get( 'access' ) ?></td>
								<td class="category"><?php echo plugin_lang_get( 'ttl' ) ?></td>
								<td class="category"><?php echo plugin_lang_get( 'dismissable' ) ?></td>
							</tr>

<?php
	foreach( $t_message->contexts as $t_context_id => $t_context ) {
?>
							<tr class="row-context">
								<td class="center">
									<a class="announce_delete_context" href="#"
									   data-message-id="<?php echo $t_message_id ?>"
									   data-context-id="<?php echo $t_context_id ?>"
									>
										<img src="<?php echo plugin_file('delete.png' ) ?>"
											 alt="-" border="0"/>
									</a>
									<input type="hidden" name="context_delete_<?php echo $t_context->id ?>" value="0"/>
								</td>
								<td class="center">
									<select name="location_<?php echo $t_context->id ?>">
										<?php Announce::print_location_option_list( $t_context->location ) ?>
									</select>
								</td>
								<td class="center">
									<select name="project_<?php echo $t_context->id ?>">
										<?php print_project_option_list( $t_context->project_id ) ?>
									</select>
								</td>
								<td class="center">
									<select name="access_<?php echo $t_context->id ?>">
										<?php print_enum_string_option_list( 'access_levels', $t_context->access ) ?>
									</select>
								</td>
								<td class="center">
									<input name="ttl_<?php echo $t_context->id ?>" class="ttl"
										   title="<?php echo plugin_lang_get( 'ttl_help' ) ?>"
										   type="number" value="<?php echo $t_context->ttl ?>" min="0"
									/>
								</td>
								<td class="center">
									<input type="checkbox" name="dismissable_<?php echo $t_context->id ?>"
										<?php check_checked( (bool)$t_context->dismissable ) ?>/>
								</td>
							</tr>
<?php
	}

	$t_first = false;
}
?>
						</tbody>

						<tfoot class="widget-toolbox">
							<tr>
								<td class="center">
									<input type="checkbox" class="announce_select_all" checked="checked"/>
								</td>
								<td colspan="6">
									<button name="update" type="submit"
											class="btn btn-primary btn-white btn-round">
										<?php echo plugin_lang_get( 'action_edit' ) ?>
									</button>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</form>
</div>
</div>

<?php
layout_page_end();
