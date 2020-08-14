<?php
declare(strict_types=1);

namespace WPR\Service;

use DI\Container;

abstract class AbstractService implements ServiceInterface
{
	protected $config;

	public function __construct()
	{
		$this->config = (new Container())->get(ConfigService::class);
	}
}