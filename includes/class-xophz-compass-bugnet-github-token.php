<?php
/**
 * The GitHub access token for Bug-Catching Net, held in the WordPress Connectors API.
 *
 * The token is the `github_token` connector (Settings > Connectors). Bug-Catching Net keeps
 * no token store of its own. get() follows core's precedence (environment variable, PHP
 * constant, then the option), reading every name from the connector's own `authentication`
 * definition. The value is for server-side use only: never echo it, log it, or return it from
 * a REST route.
 *
 * @package    Xophz_Compass_Bugnet
 * @subpackage Xophz_Compass_Bugnet/includes
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Xophz_Compass_Bugnet_GitHub_Token {

	const CONNECTOR_ID = 'github_token';

	/** The option the pre-Connectors settings page stored an encrypted token in. */
	const LEGACY_OPTION = 'xophz_compass_bugnet_github_token';

	/**
	 * Register the connector on `wp_connectors_init`, unless another plugin already has.
	 * The names match Hookshot's registration so either plugin can be the one that wins.
	 */
	public static function register( $registry ) {
		$can_register = is_object( $registry ) && method_exists( $registry, 'register' );
		if ( ! $can_register || wp_is_connector_registered( self::CONNECTOR_ID ) ) {
			return;
		}

		$registry->register(
			self::CONNECTOR_ID,
			array(
				'name'           => 'GitHub Token',
				'description'    => 'GitHub access token with repo scope. Bug-Catching Net uses it to create and sync GitHub issues; Hookshot uses it to download private release assets.',
				'type'           => 'api_key',
				'authentication' => array(
					'method'          => 'api_key',
					'setting_name'    => 'compass_github_token',
					'constant_name'   => 'GITHUB_TOKEN',
					'env_var_name'    => 'GITHUB_TOKEN',
					'credentials_url' => 'https://github.com/settings/personal-access-tokens',
				),
			)
		);
	}

	/**
	 * One of the connector's declared `authentication` names, or '' when it is unregistered.
	 */
	private static function declared_name( string $field ): string {
		if ( ! function_exists( 'wp_get_connector' ) || ! wp_is_connector_registered( self::CONNECTOR_ID ) ) {
			return '';
		}

		$connector = wp_get_connector( self::CONNECTOR_ID );
		$auth      = is_array( $connector ) ? ( $connector['authentication'] ?? array() ) : array();

		return (string) ( $auth[ $field ] ?? '' );
	}

	/**
	 * The token, or '' when the connector is unregistered or has no value.
	 */
	public static function get(): string {
		$env_name      = self::declared_name( 'env_var_name' );
		$constant_name = self::declared_name( 'constant_name' );
		$setting_name  = self::declared_name( 'setting_name' );

		$candidates = array(
			'' !== $env_name ? getenv( $env_name ) : '',
			'' !== $constant_name && defined( $constant_name ) ? constant( $constant_name ) : '',
			'' !== $setting_name ? get_option( $setting_name, '' ) : '',
		);

		foreach ( $candidates as $candidate ) {
			$value = is_scalar( $candidate ) ? trim( (string) $candidate ) : '';
			if ( '' !== $value ) {
				return $value;
			}
		}

		return '';
	}

	public static function is_configured(): bool {
		return '' !== self::get();
	}

	public static function connectors_url(): string {
		return admin_url( 'options-connectors.php' );
	}

	/**
	 * One-time move of the token the old settings page stored into the connector.
	 *
	 * The old value was AES-encrypted with SECURE_AUTH_KEY. It fills the connector's option only
	 * when the connector has no value yet, and the old option is deleted either way. A value that
	 * cannot be decrypted is dropped; enter the token again at Settings > Connectors.
	 */
	public static function migrate_legacy_token(): void {
		$legacy = get_option( self::LEGACY_OPTION, '' );
		if ( ! is_string( $legacy ) || '' === $legacy ) {
			return;
		}

		// Keep the old value until the connector exists to receive it.
		$setting_name = self::declared_name( 'setting_name' );
		if ( '' === $setting_name ) {
			return;
		}

		if ( ! self::is_configured() ) {
			$token = self::decrypt_legacy( $legacy );
			if ( '' !== $token ) {
				update_option( $setting_name, $token );
			}
		}

		delete_option( self::LEGACY_OPTION );
	}

	private static function decrypt_legacy( string $stored ): string {
		$raw = base64_decode( $stored, true );
		if ( false === $raw || false === strpos( $raw, '::' ) ) {
			return $stored; // Stored before encryption existed.
		}

		if ( ! defined( 'SECURE_AUTH_KEY' ) ) {
			return '';
		}

		list( $encrypted, $iv ) = explode( '::', $raw, 2 );
		$key       = substr( hash( 'sha256', SECURE_AUTH_KEY ), 0, 32 );
		$decrypted = openssl_decrypt( $encrypted, 'aes-256-cbc', $key, 0, $iv );

		return false === $decrypted ? '' : trim( $decrypted );
	}
}
