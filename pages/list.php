<?php

# Copyright (c) 2010 John Reese
# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

access_ensure_global_level(plugin_config_get("manage_threshold"));
$admin = access_has_global_level(config_get("manage_plugin_threshold"));

$messages = AnnounceMessage::clean(
	AnnounceMessage::load_all( true ),
	AnnounceMessage::TARGET_VIEW
);

layout_page_header( plugin_lang_get( 'plugin_title' ) );
layout_page_begin();
print_manage_menu( plugin_page( 'list' ) );
?>

<script type="text/javascript" src="<?php echo plugin_file("list_action.js") ?>"></script>

<div class="col-md-12 col-xs-12">
<div class="space-10"></div>

<div class="form-container">
<form action="<?php echo plugin_page("list_action") ?>" method="post">
	<div class="widget-box widget-color-blue2">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-file-o"></i>
				<?php echo plugin_lang_get( 'list_title' ); ?>
			</h4>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="table-responsive">
<?php
	if ($admin) {
?>
					<div class="widget-toolbox padding-8 clearfix">
						<a class="btn btn-xs btn-primary btn-white btn-round"
						   href="<?php echo plugin_page( 'config_page' ); ?>">
							<?php echo plugin_lang_get( 'config' ); ?>
						</a>
					</div>
	<?php } ?>

				</div>

				<table class="table table-striped table-bordered table-condensed">

<?php
	if( count( $messages ) > 0 ) {
?>

					<thead>
						<tr class="row-category">
							<th width="4%"></th>
							<th><?php echo plugin_lang_get("title") ?></th>
							<th><?php echo plugin_lang_get("message") ?></th>
							<th><?php echo plugin_lang_get("project") ?></th>
							<th><?php echo plugin_lang_get("location") ?></th>
						</tr>
					</thead>
<?php } ?>

					<tbody>
<?php
	foreach($messages as $message_id => $message) {
		$context_count = count($message->contexts);
		$odd_rows = $context_count & 1;
		$rowspan = max( $context_count, 0 ) + 1 - $odd_rows;
		$first = true;
?>
						<tr>
							<td class="center" rowspan="<?php echo $rowspan ?>">
								<input type="checkbox" name="message_list[]"
									   value="<?php echo $message_id ?>"/>
							</td>
							<td rowspan="<?php echo $rowspan ?>"><?php echo $message->title ?></td>
							<td rowspan="<?php echo $rowspan ?>" class="announcement-msg">
								<?php echo $message->message ?>
							</td>
<?php
		if ($context_count > 0) {
			$first = true;
			foreach( $message->contexts as $context ) {
				if( !$first ) {
					echo '<tr>';
				}
				?>
							<td class="center">
								<?php echo string_display_line( project_get_name( $context->project_id ) ) ?>
							</td>
							<td class="center">
								<?php echo Announce::getLocation( $context->location, true ) ?>
							</td>
				<?php
				$first = false;
			}

			# Add a dummy row to ensure proper color alternation
			if( !$odd_rows ) {
				echo '<tr><td colspan="2"></td></tr>';
			}
		} else {
?>
						<td colspan="2" class="center">
							<?php echo plugin_lang_get( 'no_context' ); ?>
						</td>
<?php
		}
?>
						</tr>
<?php
	}

	if( count( $messages ) > 0 ) {
?>
					</tbody>

					<tfoot class="widget-toolbox">
							<tr>
								<td class="center"><input class="announce_select_all" type="checkbox"/></td>
								<td colspan="4">
									<button name="action" value="edit" type="submit"
											class="btn btn-primary btn-white btn-round">
										<?php echo plugin_lang_get( "action_edit" ) ?>
									</button>

									<?php echo form_security_field( 'plugin_Announce_list_action_delete' ) ?>
									<button name="action" value="delete" type="submit"
											formaction="<?php echo plugin_page( 'list_action_delete' ) ?>"
											class="btn btn-primary btn-white btn-round">
										<?php echo plugin_lang_get( "action_delete" ) ?>
									</button>
								</td>
							</tr>
					</tfoot>
<?php } ?>

				</table>
			</div>
		</div>
	</div>
</form>
</div>

<br/>

<div class="form-container">
<form action="<?php echo plugin_page("create") ?>" method="post">

	<?php echo form_security_field("plugin_Announce_create") ?>

	<div class="widget-box widget-color-blue2">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-file-o"></i>
				<?php echo plugin_lang_get( 'create_title' ); ?>
			</h4>
		</div>

		<div class="widget-body">
			<div class="widget-main no-padding">
				<div class="table-responsive">

					<table class="table table-striped table-bordered table-condensed">
						<tbody>
							<tr>
								<th class="category"><?php echo plugin_lang_get("title") ?></th>
								<td><input name="title" type="text" size="50"/></td>
							</tr>

							<tr>
								<th class="category"><?php echo plugin_lang_get("message") ?></th>
								<td><textarea name="message" cols="70" rows="4"></textarea></td>
							</tr>

							<tr>
								<td class="category"><?php echo plugin_lang_get("location") ?></td>
								<td>
									<select name="location">
										<?php Announce::print_location_option_list() ?>
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
									<input name="ttl" class="ttl" type="number" value="0" min="0"/>
								</td>
							</tr>

							<tr>
								<td class="category"><?php echo plugin_lang_get("dismissable") ?></td>
								<td><input type="checkbox" name="dismissable" checked="checked"/></td>
							</tr>
						</tbody>

					</table>
				</div>
			</div>

			<div class="widget-toolbox padding-8 clearfix">
				<input class="btn btn-primary btn-white btn-round"
					   type="submit"
					   value="<?php echo plugin_lang_get("action_create") ?>"/>
			</div>

		</div>
	</div>
</form>
</div>

</div>

<?php
layout_page_end();
