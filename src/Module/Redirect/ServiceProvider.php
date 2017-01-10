<?php # -*- coding: utf-8 -*-

namespace Inpsyde\MultilingualPress\Module\Redirect;

use Inpsyde\MultilingualPress\Common\Admin\SitesListTableColumn;
use Inpsyde\MultilingualPress\Common\Nonce\WPNonce;
use Inpsyde\MultilingualPress\Common\Setting\User\SecureUserSettingUpdater;
use Inpsyde\MultilingualPress\Common\Setting\User\UserSetting;
use Inpsyde\MultilingualPress\Module\ActivationAwareModuleServiceProvider;
use Inpsyde\MultilingualPress\Module\ActivationAwareness;
use Inpsyde\MultilingualPress\Module\Module;
use Inpsyde\MultilingualPress\Module\ModuleManager;
use Inpsyde\MultilingualPress\Service\Container;

/**
 * Module service provider.
 *
 * @package Inpsyde\MultilingualPress\Module\Redirect
 * @since   3.0.0
 */
final class ServiceProvider implements ActivationAwareModuleServiceProvider {

	use ActivationAwareness;

	/**
	 * Registers the provided services on the given container.
	 *
	 * @since 3.0.0
	 *
	 * @param Container $container Container object.
	 *
	 * @return void
	 */
	public function register( Container $container ) {

		$container['multilingualpress.redirect_filter'] = function ( Container $container ) {

			return new RedirectFilter(
				$container['multilingualpress.redirect_settings_repository']
			);
		};

		$container['multilingualpress.redirect_settings_repository'] = function () {

			return new TypeSafeSettingsRepository();
		};
	}

	/**
	 * Bootstraps the registered services.
	 *
	 * @since 3.0.0
	 *
	 * @param Container $container Container object.
	 *
	 * @return void
	 */
	public function bootstrap( Container $container ) {

		$this->on_activation( function () use ( $container ) {

			$repository = $container['multilingualpress.redirect_settings_repository'];

			// This nonce is not accessible via the container because it is used no matter what by static parties.
			$nonce = new WPNonce( 'save_redirect_user_setting' );

			( new UserSetting(
				new RedirectUserSetting( SettingsRepository::META_KEY_USER, $nonce, $repository ),
				new SecureUserSettingUpdater( SettingsRepository::META_KEY_USER, $nonce )
			) )->register();

			if ( is_admin() ) {
				if ( is_network_admin() ) {
					( new SitesListTableColumn(
						'multilingualpress.redirect',
						__( 'Redirect', 'multilingual-press' ),
						function ( $id, $site_id ) {

							// TODO: Don't hard-code option name, use repository or class constant.
							return get_blog_option( $site_id, 'inpsyde_multilingual_redirect' )
								? '<span class="dashicons dashicons-yes"></span>'
								: '';
						}
					) )->register();
				}
			} else {
				$container['multilingualpress.redirect_filter']->enable();
			}
		} );
	}

	/**
	 * Registers the module at the module manager.
	 *
	 * @since 3.0.0
	 *
	 * @param ModuleManager $module_manager Module manager object.
	 *
	 * @return bool Whether or not the module was registerd successfully AND has been activated.
	 */
	public function register_module( ModuleManager $module_manager ) {

		return $module_manager->register_module( new Module( 'redirect', [
			'description' => __( 'Redirect visitors according to browser language settings.', 'multilingual-press' ),
			'name'        => __( 'Redirect', 'multilingual-press' ),
			'active'      => false,
		] ) );
	}
}
