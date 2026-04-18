<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    Xophz_Compass_Bugnet
 * @subpackage Xophz_Compass_Bugnet/admin/partials
 */
?>

<div class="wrap">
	<h1>Bug-Catching Net: GitHub Sync Settings</h1>
	<p>Configure the connection to your GitHub repository to automatically create and track issues when bugs are reported via the YouMeOS Spark.</p>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'xophz_compass_bugnet_github_settings' );
		do_settings_sections( 'xophz_compass_bugnet_github_settings' );
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">GitHub Repository Owner</th>
				<td>
					<input type="text" name="xophz_compass_bugnet_github_owner" value="<?php echo esc_attr( get_option('xophz_compass_bugnet_github_owner') ); ?>" class="regular-text" placeholder="e.g., HalloftheGods" />
					<p class="description">The organization or user that owns the repository.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">GitHub Repository Name</th>
				<td>
					<input type="text" name="xophz_compass_bugnet_github_repo" value="<?php echo esc_attr( get_option('xophz_compass_bugnet_github_repo') ); ?>" class="regular-text" placeholder="e.g., Xophz-COMPASS" />
					<p class="description">The name of the repository.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">GitHub Personal Access Token (PAT)</th>
				<td>
					<?php 
						$token = get_option('xophz_compass_bugnet_github_token');
						$masked_token = !empty($token) ? str_repeat('*', 16) : '';
					?>
					<input type="password" name="xophz_compass_bugnet_github_token" value="<?php echo esc_attr( $masked_token ); ?>" class="regular-text" />
					<p class="description">Requires <strong>repo</strong> scope to create issues and webhooks.</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
