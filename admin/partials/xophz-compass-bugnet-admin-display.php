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
					<input type="text" id="github_owner_input" name="xophz_compass_bugnet_github_owner" value="<?php echo esc_attr( get_option('xophz_compass_bugnet_github_owner') ); ?>" class="regular-text" placeholder="e.g., HalloftheGods" />
					<p class="description">The organization or user that owns the repository.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">GitHub Repository Name</th>
				<td>
					<input type="text" id="github_repo_input" name="xophz_compass_bugnet_github_repo" value="<?php echo esc_attr( get_option('xophz_compass_bugnet_github_repo') ); ?>" class="regular-text" placeholder="e.g., Xophz-COMPASS" />
					<p class="description">The name of the repository.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Verify Repository</th>
				<td>
					<p class="description">
						<?php 
							$owner = get_option('xophz_compass_bugnet_github_owner') ?: 'owner';
							$repo = get_option('xophz_compass_bugnet_github_repo') ?: 'repo';
						?>
						<a id="github_verify_link" href="https://github.com/<?php echo esc_attr( $owner ); ?>/<?php echo esc_attr( $repo ); ?>" target="_blank">
							Verify connection to https://github.com/<?php echo esc_attr( $owner ); ?>/<?php echo esc_attr( $repo ); ?>
						</a>
					</p>
					<script>
						document.addEventListener('DOMContentLoaded', function() {
							const ownerInput = document.getElementById('github_owner_input');
							const repoInput = document.getElementById('github_repo_input');
							const verifyLink = document.getElementById('github_verify_link');
							
							function updateLink() {
								const owner = ownerInput.value || 'owner';
								const repo = repoInput.value || 'repo';
								const url = `https://github.com/${owner}/${repo}`;
								verifyLink.href = url;
								verifyLink.textContent = `Verify connection to ${url}`;
							}
							
							if (ownerInput && repoInput && verifyLink) {
								ownerInput.addEventListener('input', updateLink);
								repoInput.addEventListener('input', updateLink);
							}
						});
					</script>
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
		<div style="display: flex; gap: 10px; align-items: center; margin-top: 20px;">
			<?php submit_button('', 'primary', 'submit', false); ?>
			<button type="button" id="github_test_connection" class="button button-secondary">Test Connection</button>
			<span id="github_test_result" style="font-weight: 500; display: inline-block;"></span>
		</div>
	</form>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const testBtn = document.getElementById('github_test_connection');
			const testResult = document.getElementById('github_test_result');
			const nonce = '<?php echo esc_js( wp_create_nonce("wp_rest") ); ?>';

			if (testBtn) {
				testBtn.addEventListener('click', function(e) {
					e.preventDefault();
					
					testBtn.disabled = true;
					testResult.style.color = '';
					testResult.textContent = 'Testing connection...';
					
					fetch('/wp-json/bugnet/v1/test-github', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-WP-Nonce': nonce
						}
					})
					.then(response => response.json())
					.then(data => {
						if (data.success) {
							testResult.style.color = 'green';
							testResult.textContent = '✅ ' + data.message;
						} else {
							testResult.style.color = 'red';
							testResult.textContent = '❌ ' + (data.message || 'Unknown error');
						}
					})
					.catch(error => {
						testResult.style.color = 'red';
						testResult.textContent = '❌ Request failed: ' + error.message;
					})
					.finally(() => {
						testBtn.disabled = false;
					});
				});
			}
		});
	</script>
</div>
