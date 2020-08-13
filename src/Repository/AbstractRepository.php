<?php
declare(strict_types=1);

namespace WPR\Repository;

use WPR\Service\ConfigService;

abstract class AbstractRepository {
	protected $config;

	public function __construct(ConfigService $config)
	{
		$this->config = $config;
	}
}