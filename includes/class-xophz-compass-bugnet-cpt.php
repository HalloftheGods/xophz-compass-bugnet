<?php

/**
 * Register the Custom Post Type for Bug Net
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Xophz_Compass_Bugnet
 * @subpackage Xophz_Compass_Bugnet/includes
 */

/**
 * Register the Custom Post Type for Bug Net.
 *
 * @package    Xophz_Compass_Bugnet
 * @subpackage Xophz_Compass_Bugnet/includes
 * @author     Xopher Pollard <email@example.com>
 */
class Xophz_Compass_Bugnet_CPT {

	/**
	 * The name of the custom post type.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cpt_name    The name of the custom post type.
	 */
	private $cpt_name = 'compass_bug';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Register the Custom Post Type.
	 *
	 * @since    1.0.0
	 */
	public function register_cpt() {

		$labels = array(
			'name'                  => _x( 'Bugs', 'Post Type General Name', 'xophz-compass-bugnet' ),
			'singular_name'         => _x( 'Bug', 'Post Type Singular Name', 'xophz-compass-bugnet' ),
			'menu_name'             => __( 'Bug Net', 'xophz-compass-bugnet' ),
			'name_admin_bar'        => __( 'Bug', 'xophz-compass-bugnet' ),
			'archives'              => __( 'Bug Archives', 'xophz-compass-bugnet' ),
			'attributes'            => __( 'Bug Attributes', 'xophz-compass-bugnet' ),
			'parent_item_colon'     => __( 'Parent Bug:', 'xophz-compass-bugnet' ),
			'all_items'             => __( 'All Bugs', 'xophz-compass-bugnet' ),
			'add_new_item'          => __( 'Add New Bug', 'xophz-compass-bugnet' ),
			'add_new'               => __( 'Add New', 'xophz-compass-bugnet' ),
			'new_item'              => __( 'New Bug', 'xophz-compass-bugnet' ),
			'edit_item'             => __( 'Edit Bug', 'xophz-compass-bugnet' ),
			'update_item'           => __( 'Update Bug', 'xophz-compass-bugnet' ),
			'view_item'             => __( 'View Bug', 'xophz-compass-bugnet' ),
			'view_items'            => __( 'View Bugs', 'xophz-compass-bugnet' ),
			'search_items'          => __( 'Search Bugs', 'xophz-compass-bugnet' ),
			'not_found'             => __( 'Not found', 'xophz-compass-bugnet' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'xophz-compass-bugnet' ),
			'featured_image'        => __( 'Featured Image', 'xophz-compass-bugnet' ),
			'set_featured_image'    => __( 'Set featured image', 'xophz-compass-bugnet' ),
			'remove_featured_image' => __( 'Remove featured image', 'xophz-compass-bugnet' ),
			'use_featured_image'    => __( 'Use as featured image', 'xophz-compass-bugnet' ),
			'insert_into_item'      => __( 'Insert into bug', 'xophz-compass-bugnet' ),
			'uploaded_to_this_item' => __( 'Uploaded to this bug', 'xophz-compass-bugnet' ),
			'items_list'            => __( 'Bugs list', 'xophz-compass-bugnet' ),
			'items_list_navigation' => __( 'Bugs list navigation', 'xophz-compass-bugnet' ),
			'filter_items_list'     => __( 'Filter bugs list', 'xophz-compass-bugnet' ),
		);
		$args = array(
			'label'                 => __( 'Bug', 'xophz-compass-bugnet' ),
			'description'           => __( 'Bugs and Issues for the Bug Net system', 'xophz-compass-bugnet' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'author', 'custom-fields', 'comments' ),
			'taxonomies'            => array(),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 6,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
			'show_in_rest'          => true,
			'menu_icon'             => plugins_url( '../icon.svg', __FILE__ ),
		);
		register_post_type( $this->cpt_name, $args );

	}

	public function disable_gutenberg( $use_block_editor, $post_type ) {
		$isBugPost = $post_type === $this->cpt_name;
		return $isBugPost ? false : $use_block_editor;
	}

	/**
	 * Add Meta Boxes
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'compass_bug_settings',
			'Bug Details',
			array( $this, 'render_meta_box' ),
			$this->cpt_name,
			'normal',
			'high'
		);
	}

	/**
	 * Render Meta Box content
	 */
	public function render_meta_box( $post ) {
		// retrieve the metadata values if they exist
		$status = get_post_meta( $post->ID, 'bug_status', true ) ?: 'new';
		$priority = get_post_meta( $post->ID, 'bug_priority', true ) ?: 'medium';
		$environment = get_post_meta( $post->ID, 'bug_environment', true );
		$url = get_post_meta( $post->ID, 'bug_url', true );
		$plugin = get_post_meta( $post->ID, 'bug_plugin', true );
		$emoji = get_post_meta( $post->ID, 'bug_emoji', true );
		$bug_type = get_post_meta( $post->ID, 'bug_type', true );

		// Nonce field for security
		wp_nonce_field( 'xophz_compass_bugnet_save_meta_box', 'xophz_compass_bugnet_meta_box_nonce' );

		?>
		<p>
			<label for="bug_status" style="display:block; font-weight:bold;">Status</label>
			<select id="bug_status" name="bug_status" style="width:100%;">
				<option value="new" <?php selected( $status, 'new' ); ?>>New</option>
				<option value="in-progress" <?php selected( $status, 'in-progress' ); ?>>In Progress</option>
				<option value="resolved" <?php selected( $status, 'resolved' ); ?>>Resolved</option>
				<option value="closed" <?php selected( $status, 'closed' ); ?>>Closed</option>
			</select>
		</p>
		<p>
			<label for="bug_priority" style="display:block; font-weight:bold;">Priority</label>
			<select id="bug_priority" name="bug_priority" style="width:100%;">
				<option value="low" <?php selected( $priority, 'low' ); ?>>Low</option>
				<option value="medium" <?php selected( $priority, 'medium' ); ?>>Medium</option>
				<option value="high" <?php selected( $priority, 'high' ); ?>>High</option>
				<option value="critical" <?php selected( $priority, 'critical' ); ?>>Critical</option>
			</select>
		</p>
		<p>
			<label for="bug_environment" style="display:block; font-weight:bold;">Environment Info</label>
			<input type="text" id="bug_environment" name="bug_environment" value="<?php echo esc_attr( $environment ); ?>" style="width:100%;" placeholder="e.g., Chrome 120, MacOS, Vue 3">
			<span class="description">Information about the browser, OS, and app context.</span>
		</p>
		<p>
			<label for="bug_url" style="display:block; font-weight:bold;">URL / Route</label>
			<input type="text" id="bug_url" name="bug_url" value="<?php echo esc_attr( $url ); ?>" style="width:100%;" placeholder="e.g., http://compass/#/bugnet">
			<span class="description">The URL where the error occurred.</span>
		</p>
		<p>
			<label for="bug_plugin" style="display:block; font-weight:bold;">Plugin</label>
			<input type="text" id="bug_plugin" name="bug_plugin" value="<?php echo esc_attr( $plugin ); ?>" style="width:100%;" placeholder="e.g., bugnet">
			<span class="description">The active plugin when the error happened.</span>
		</p>
		<p>
			<label for="bug_emoji" style="display:block; font-weight:bold;">Bug Emoji</label>
			<input type="text" id="bug_emoji" name="bug_emoji" value="<?php echo esc_attr( $emoji ); ?>" style="width:100%;" placeholder="e.g., 🦋">
			<span class="description">The caught bug emoji for gamification.</span>
		</p>
		<p>
			<label for="bug_type" style="display:block; font-weight:bold;">Bug Type (Species)</label>
			<input type="text" id="bug_type" name="bug_type" value="<?php echo esc_attr( $bug_type ); ?>" style="width:100%;" placeholder="e.g., butterfly">
			<span class="description">The species identifier for this bug.</span>
		</p>
		<?php
	}

	/**
	 * Save Meta Box
	 */
	public function save_meta_box( $post_id ) {
		// Check nonce
		if ( ! isset( $_POST['xophz_compass_bugnet_meta_box_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['xophz_compass_bugnet_meta_box_nonce'], 'xophz_compass_bugnet_save_meta_box' ) ) {
			return;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Save fields
		if ( isset( $_POST['bug_status'] ) ) {
			update_post_meta( $post_id, 'bug_status', sanitize_text_field( $_POST['bug_status'] ) );
		}
		if ( isset( $_POST['bug_priority'] ) ) {
			update_post_meta( $post_id, 'bug_priority', sanitize_text_field( $_POST['bug_priority'] ) );
		}
		if ( isset( $_POST['bug_environment'] ) ) {
			update_post_meta( $post_id, 'bug_environment', sanitize_text_field( $_POST['bug_environment'] ) );
		}
		if ( isset( $_POST['bug_url'] ) ) {
			update_post_meta( $post_id, 'bug_url', sanitize_url( $_POST['bug_url'] ) );
		}
		if ( isset( $_POST['bug_plugin'] ) ) {
			update_post_meta( $post_id, 'bug_plugin', sanitize_text_field( $_POST['bug_plugin'] ) );
		}
		if ( isset( $_POST['bug_emoji'] ) ) {
			update_post_meta( $post_id, 'bug_emoji', sanitize_text_field( $_POST['bug_emoji'] ) );
		}
		if ( isset( $_POST['bug_type'] ) ) {
			update_post_meta( $post_id, 'bug_type', sanitize_text_field( $_POST['bug_type'] ) );
		}
	}

	/**
	 * Register REST API Fields
	 * This makes custom meta available directly in the JSON response
	 */
	public function register_rest_fields() {
		$fields = array( 'bug_status', 'bug_priority', 'bug_environment', 'bug_url', 'bug_plugin', 'bug_emoji', 'bug_type', 'bug_species_name' );

		foreach ( $fields as $field ) {
			register_rest_field(
				$this->cpt_name,
				$field,
				array(
					'get_callback'    => array( $this, 'get_rest_meta_value' ),
					'update_callback' => array( $this, 'update_rest_meta_value' ),
					'schema'          => null,
				)
			);
		}
	}

	public function get_rest_meta_value( $object, $field_name, $request ) {
		return get_post_meta( $object['id'], $field_name, true );
	}

	public function update_rest_meta_value( $value, $object, $field_name ) {
		return update_post_meta( $object->ID, $field_name, $value );
	}

	/**
	 * Trigger GitHub Sync on REST API Insert
	 */
	public function sync_to_github_rest( $post, $request, $creating ) {
		if ( $post->post_status === 'publish' || $post->post_status === 'trash' ) {
			$this->do_github_sync( $post->ID );
		}
	}

	/**
	 * Trigger GitHub Sync on Admin Save
	 */
	public function sync_to_github_admin( $post_id, $post, $update ) {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return; // Handled by sync_to_github_rest after meta is saved
		}
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( $post->post_status === 'publish' ) {
			$this->do_github_sync( $post_id );
		}
	}

	public static function decrypt_token( $encrypted_token ) {
		if ( empty( $encrypted_token ) ) {
			return '';
		}
		
		$raw_key = defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : 'default_fallback_key_xophz_bugnet';
		$key = substr( hash( 'sha256', $raw_key ), 0, 32 );
		$data = base64_decode( $encrypted_token );
		
		if ( strpos( $data, '::' ) === false ) {
			return $encrypted_token; // Fallback for old unencrypted tokens or broken strings
		}
		
		list( $encrypted_data, $iv ) = explode( '::', $data, 2 );
		$decrypted = openssl_decrypt( $encrypted_data, 'aes-256-cbc', $key, 0, $iv );
		return $decrypted !== false ? $decrypted : $encrypted_token;
	}

	/**
	 * Performs the actual GitHub API Request
	 */
	public function do_github_sync( $post_id ) {
		$github_id = get_post_meta( $post_id, 'bug_github_issue_id', true );
		$is_update = ! empty( $github_id );

		$owner = get_option( 'xophz_compass_bugnet_github_owner' );
		$global_repo  = get_option( 'xophz_compass_bugnet_github_repo' );
		$encrypted_token = get_option( 'xophz_compass_bugnet_github_token' );
		$token = self::decrypt_token( $encrypted_token );

		$plugin_repo = get_post_meta( $post_id, 'bug_plugin', true );
		$repo = ! empty( $plugin_repo ) ? $plugin_repo : $global_repo;

		if ( empty( $owner ) || empty( $repo ) || empty( $token ) ) {
			return new WP_Error( 'not_configured', 'GitHub sync is not configured (missing owner, repo, or token).', array( 'status' => 400 ) );
		}

		$post = get_post( $post_id );

		// Prepare payload
		$priority    = get_post_meta( $post_id, 'bug_priority', true ) ?: 'medium';
		$environment = get_post_meta( $post_id, 'bug_environment', true ) ?: 'N/A';
		$url         = get_post_meta( $post_id, 'bug_url', true ) ?: 'N/A';
		$plugin      = get_post_meta( $post_id, 'bug_plugin', true ) ?: 'N/A';
		$bug_type    = get_post_meta( $post_id, 'bug_type', true ) ?: 'unknown';
		
		$emoji_map = array(
			'microbe'     => '🦠',
			'lady-bug'    => '🐞',
			'butterfly'   => '🦋',
			'ant'         => '🐜',
			'cricket'     => '🦗',
			'fly'         => '🪰',
			'mosquito'    => '🦟',
			'beetle'      => '🪲',
			'snail'       => '🐌',
			'caterpillar' => '🐛',
			'cockroach'   => '🪳',
			'spider'      => '🕷️',
			'worm'        => '🪱',
			'bee'         => '🐝',
			'scorpion'    => '🦂',
		);
		$bug_emoji = get_post_meta( $post_id, 'bug_emoji', true );
		if ( empty( $bug_emoji ) ) {
			$bug_emoji = isset( $emoji_map[ $bug_type ] ) ? $emoji_map[ $bug_type ] : '🐛';
		}

		$bug_name    = get_post_meta( $post_id, 'bug_species_name', true ) ?: ucfirst( $bug_type );

		$bug_status  = get_post_meta( $post_id, 'bug_status', true ) ?: 'new';

		$body = $post->post_content . "\n\n" .
				"**Species:** " . $bug_emoji . " " . $bug_name . "\n" .
				"**Priority:** " . esc_html( $priority ) . "\n" .
				"**Environment:** " . esc_html( $environment ) . "\n" .
				"**URL/Route:** " . esc_url( $url ) . "\n" .
				"**Plugin:** " . esc_html( $plugin ) . "\n" .
				"**Reporter:** " . esc_html( get_the_author_meta( 'display_name', $post->post_author ) );

		$labels = array( 'bug', 'priority: ' . $priority );
		if ( ! empty( $bug_name ) && $bug_name !== 'Unknown' ) {
			$labels[] = $bug_emoji . ' ' . $bug_name;
		}
		if ( ! empty( $plugin_repo ) && $plugin_repo !== $global_repo ) {
			$labels[] = 'plugin: ' . $plugin_repo;
		}

		$title = $post->post_title;
		if ( strpos( $title, $bug_emoji ) !== 0 ) {
			$title = $bug_emoji . ' ' . $title;
		}

		$payload = array(
			'title'  => $title,
			'body'   => $body,
			'labels' => $labels,
			'state'  => ( in_array( $bug_status, array('resolved', 'closed', 'trash') ) || $post->post_status === 'trash' ) ? 'closed' : 'open'
		);

		if ( $is_update ) {
			$api_url = "https://api.github.com/repos/{$owner}/{$repo}/issues/{$github_id}";
			$method = 'PATCH';
		} else {
			$api_url = "https://api.github.com/repos/{$owner}/{$repo}/issues";
			$method = 'POST';
		}

		$response = wp_remote_request( $api_url, array(
			'method'      => $method,
			'headers' => array(
				'Authorization' => 'Bearer ' . $token,
				'Accept'        => 'application/vnd.github.v3+json',
				'Content-Type'  => 'application/json',
				'User-Agent'    => 'Xophz-COMPASS/1.0',
			),
			'body'        => wp_json_encode( $payload ),
			'data_format' => 'body',
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 201 === $response_code || 200 === $response_code ) {
			$res_body = wp_remote_retrieve_body( $response );
			$data = json_decode( $res_body );
			if ( ! $is_update && isset( $data->number ) ) {
				update_post_meta( $post_id, 'bug_github_issue_id', $data->number );
				update_post_meta( $post_id, 'bug_github_issue_url', $data->html_url );
			}
			if ( isset( $data->title ) && $data->title !== $post->post_title ) {
				global $wpdb;
				$wpdb->update(
					$wpdb->posts,
					array( 'post_title' => $data->title ),
					array( 'ID' => $post_id ),
					array( '%s' ),
					array( '%d' )
				);
			}
			return true;
		} else {
			$res_body = wp_remote_retrieve_body( $response );
			$error_msg = "GitHub API Error: HTTP {$response_code}. Body: " . print_r( $res_body, true );
			error_log( "GitHub Sync Failed for Post {$post_id}. Status: {$response_code}. Body: " . print_r( $res_body, true ) );
			return new WP_Error( 'github_api_error', $error_msg, array( 'status' => 500 ) );
		}
		
		return new WP_Error( 'unknown_error', 'Unknown error occurred during GitHub sync.', array( 'status' => 500 ) );
	}

	/**
	 * Register REST API Endpoints
	 */
	public function register_webhook_route() {
		register_rest_route( 'bugnet/v1', '/github-webhook', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'handle_github_webhook' ),
			'permission_callback' => '__return_true', // GitHub sends no WP auth, verify via payload if needed
		) );
		
		register_rest_route( 'bugnet/v1', '/sync/(?P<id>\d+)', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'handle_manual_sync' ),
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		) );

		register_rest_route( 'bugnet/v1', '/test-github', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'handle_test_github' ),
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		) );

		register_rest_route( 'bugnet/v1', '/repos', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'handle_get_repos' ),
			'permission_callback' => function () {
				return is_user_logged_in();
			},
		) );
	}

	/**
	 * Handle manual sync request from the frontend
	 */
	public function handle_manual_sync( WP_REST_Request $request ) {
		$post_id = $request->get_param( 'id' );
		$result = $this->do_github_sync( $post_id );
		
		if ( is_wp_error( $result ) ) {
			return new WP_REST_Response( array(
				'success' => false,
				'message' => $result->get_error_message(),
			), $result->get_error_data()['status'] ?? 500 );
		}
		
		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Successfully synced to GitHub.',
			'github_issue_id' => get_post_meta( $post_id, 'bug_github_issue_id', true ),
			'github_issue_url' => get_post_meta( $post_id, 'bug_github_issue_url', true )
		), 200 );
	}

	/**
	 * Handle GitHub Connection Test
	 */
	public function handle_test_github( WP_REST_Request $request ) {
		$owner = get_option( 'xophz_compass_bugnet_github_owner' );
		$repo = get_option( 'xophz_compass_bugnet_github_repo' );
		$encrypted_token = get_option( 'xophz_compass_bugnet_github_token' );

		if ( ! $owner || ! $repo || ! $encrypted_token ) {
			return new WP_REST_Response( array(
				'success' => false,
				'message' => 'Missing GitHub Owner, Repo, or Token.'
			), 400 );
		}

		$parts = explode( '::', base64_decode( $encrypted_token ) );
		if ( count( $parts ) !== 2 ) {
			return new WP_REST_Response( array(
				'success' => false,
				'message' => 'Invalid token format.'
			), 400 );
		}

		$raw_key = defined( 'SECURE_AUTH_KEY' ) ? SECURE_AUTH_KEY : 'default_fallback_key_xophz_bugnet';
		$key = substr( hash( 'sha256', $raw_key ), 0, 32 );
		$token = openssl_decrypt( $parts[0], 'aes-256-cbc', $key, 0, $parts[1] );

		$api_url = "https://api.github.com/repos/{$owner}/{$repo}";
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $token,
				'Accept'        => 'application/vnd.github.v3+json',
				'User-Agent'    => 'WordPress/Xophz-COMPASS-Bugnet',
			),
			'timeout' => 15,
		);

		$response = wp_remote_get( $api_url, $args );

		if ( is_wp_error( $response ) ) {
			return new WP_REST_Response( array(
				'success' => false,
				'message' => 'HTTP request failed: ' . $response->get_error_message()
			), 500 );
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ) );

		if ( $response_code === 200 ) {
			return new WP_REST_Response( array(
				'success' => true,
				'message' => 'Connection successful! Verified access to ' . $body->full_name . ' (Permissions: ' . (isset($body->permissions->push) && $body->permissions->push ? 'Write' : 'Read-only') . ')'
			), 200 );
		} else {
			return new WP_REST_Response( array(
				'success' => false,
				'message' => 'GitHub API Error ' . $response_code . ': ' . ($body->message ?? 'Unknown Error')
			), 400 );
		}
	}

	/**
	 * Fetch installed COMPASS plugins locally
	 */
	public function handle_get_repos( WP_REST_Request $request ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		
		$installed_plugins = get_plugins();
		$repos = array();
		
		foreach ( $installed_plugins as $plugin_file => $plugin_data ) {
			$slug = dirname( $plugin_file );
			if ( strpos( $slug, 'xophz-compass' ) === 0 ) {
				$name = $plugin_data['Name'];
				// Clean up the name
				$name = str_replace( 'Xophz COMPASS - ', '', $name );
				$name = str_replace( 'Xophz COMPASS', 'COMPASS Core Suite', $name );
				$name = str_replace( 'Xophz ', '', $name );
				
				$repos[] = array(
					'name'        => $name,
					'value'       => $slug,
					'description' => $plugin_data['Description']
				);
			}
		}

		return new WP_REST_Response( $repos, 200 );
	}

	/**
	 * Handle incoming GitHub Webhook
	 */
	public function handle_github_webhook( WP_REST_Request $request ) {
		$event = $request->get_header( 'x_github_event' );
		$payload = json_decode( $request->get_body() );

		if ( ! $payload || ! isset( $payload->issue ) ) {
			return new WP_REST_Response( 'Invalid payload', 400 );
		}

		$issue_id = $payload->issue->number;

		// Find the corresponding compass_bug post
		$args = array(
			'post_type'  => $this->cpt_name,
			'meta_key'   => 'bug_github_issue_id',
			'meta_value' => $issue_id,
			'posts_per_page' => 1,
			'post_status' => 'any'
		);
		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return new WP_REST_Response( 'Issue not found', 404 );
		}

		$post_id = $query->posts[0]->ID;

		// Handle Issue Status Changes
		if ( $event === 'issues' && isset( $payload->action ) ) {
			if ( $payload->action === 'closed' ) {
				update_post_meta( $post_id, 'bug_status', 'resolved' );
			} elseif ( $payload->action === 'reopened' ) {
				update_post_meta( $post_id, 'bug_status', 'in-progress' );
			}

			if ( in_array( $payload->action, array( 'edited', 'opened', 'reopened' ) ) && isset( $payload->issue->title ) ) {
				$post_title = get_the_title( $post_id );
				if ( $post_title !== $payload->issue->title ) {
					global $wpdb;
					$wpdb->update(
						$wpdb->posts,
						array( 'post_title' => $payload->issue->title ),
						array( 'ID' => $post_id ),
						array( '%s' ),
						array( '%d' )
					);
				}
			}
		}

		// Handle Issue Comments
		if ( $event === 'issue_comment' && isset( $payload->action ) ) {
			if ( $payload->action === 'created' ) {
				$comment_content = $payload->comment->body;
				$author_name = $payload->comment->user->login;
				
				// Optional: map GitHub users to WP users if possible
				$commentdata = array(
					'comment_post_ID'      => $post_id,
					'comment_author'       => 'GitHub: ' . $author_name,
					'comment_author_email' => 'github@example.com',
					'comment_content'      => $comment_content,
					'comment_type'         => 'comment',
					'comment_parent'       => 0,
					'user_id'              => 0,
					'comment_approved'     => 1,
				);
				wp_insert_comment( $commentdata );
			}
		}

		return new WP_REST_Response( 'Webhook processed', 200 );
	}

	public function sync_comment_to_github( $comment_id, $comment_approved ) {
		if ( $comment_approved !== 1 && $comment_approved !== '1' ) {
			return;
		}

		$comment = get_comment( $comment_id );
		if ( ! $comment ) {
			return;
		}

		$this->do_github_comment_sync( $comment );
	}

	public function sync_comment_to_github_rest( $comment, $request, $creating ) {
		if ( ! $creating ) {
			return;
		}
		$approved = $comment->comment_approved;
		if ( $approved == 1 || $approved === '1' ) {
			$this->do_github_comment_sync( $comment );
		}
	}

	private function do_github_comment_sync( $comment ) {
		$post_id   = $comment->comment_post_ID;
		$post_type = get_post_type( $post_id );
		$isNotBugPost = $post_type !== $this->cpt_name;
		if ( $isNotBugPost ) {
			return;
		}

		$isFromGitHub = strpos( $comment->comment_author, 'GitHub:' ) === 0;
		if ( $isFromGitHub ) {
			return;
		}

		$github_issue_id = get_post_meta( $post_id, 'bug_github_issue_id', true );
		$hasNoGitHubLink = empty( $github_issue_id );
		if ( $hasNoGitHubLink ) {
			return;
		}

		$owner = get_option( 'xophz_compass_bugnet_github_owner' );
		$repo  = get_option( 'xophz_compass_bugnet_github_repo' );
		$plugin_repo = get_post_meta( $post_id, 'bug_plugin', true );
		if ( ! empty( $plugin_repo ) ) {
			$repo = $plugin_repo;
		}

		$encrypted_token = get_option( 'xophz_compass_bugnet_github_token' );
		$token = self::decrypt_token( $encrypted_token );

		$isMissingConfig = empty( $owner ) || empty( $repo ) || empty( $token );
		if ( $isMissingConfig ) {
			return;
		}

		$user_id      = $comment->user_id;
		$display_name = ! empty( $user_id ) ? get_the_author_meta( 'display_name', $user_id ) : $comment->comment_author;
		$email        = ! empty( $user_id ) ? get_the_author_meta( 'user_email', $user_id ) : $comment->comment_author_email;
		$avatar_url   = get_avatar_url( $email, array( 'size' => 50 ) );
		$content      = $comment->comment_content;
		$quote_block  = "";

		if ( ! empty( $comment->comment_parent ) ) {
			$parent_comment = get_comment( $comment->comment_parent );
			if ( $parent_comment ) {
				$parent_author = str_replace( 'GitHub: ', '', $parent_comment->comment_author );
				$parent_text   = $parent_comment->comment_content;
				
				$quote_block = ">> **Replying to {$parent_author}:**\n"
					. ">> *" . str_replace( "\n", "*\n>> *", $parent_text ) . "*\n\n";
			}
		}

		$site_domain  = wp_parse_url( home_url(), PHP_URL_HOST );

		$body = "<img src=\"{$avatar_url}\" style=\"vertical-align:middle;\" align=\"right\" height=\"100\"/> "
			. "**{$display_name}** commented - via My Compass @ {$site_domain}\n\n"
			. $quote_block
			. "> " . str_replace( "\n", "\n> ", $content );

		$api_url = "https://api.github.com/repos/{$owner}/{$repo}/issues/{$github_issue_id}/comments";

		wp_remote_post( $api_url, array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $token,
				'Accept'        => 'application/vnd.github.v3+json',
				'Content-Type'  => 'application/json',
				'User-Agent'    => 'Xophz-COMPASS/1.0',
			),
			'body'        => wp_json_encode( array( 'body' => $body ) ),
			'data_format' => 'body',
		) );
	}
}
