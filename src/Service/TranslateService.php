<?php
declare( strict_types=1 );

namespace WPR\Service;


class TranslateService extends AbstractService
{
	public function loadPluginTextDomain()
	{
		$locale = apply_filters( 'plugin_locale', get_locale(), $this->config->PLUGIN_NAME );
		if ( $loaded = load_textdomain( $this->config->PLUGIN_NAME,
			trailingslashit( WP_LANG_DIR ) . $this->config->PLUGIN_NAME . DIRECTORY_SEPARATOR . $this->config::PLUGIN_NAME . '-' . $locale . '.mo' ) ) {
			return $loaded;
		}

		return load_plugin_textdomain($this->config->PLUGIN_NAME, false, $this->config->getPluginPath() . '/languages/' );
	}
}