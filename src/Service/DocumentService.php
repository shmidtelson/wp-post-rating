<?php
declare(strict_types=1);

namespace WPR\Service;


class DocumentService extends AbstractService
{
	public function addNonceToHead()
	{
		echo '<meta name="_wpr_nonce" content="' . wp_create_nonce($this->config::PLUGIN_NONCE_KEY) . '" />';
	}
}