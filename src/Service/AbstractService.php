<?php
declare(strict_types=1);

namespace WPR\Service;

abstract class AbstractService
{
	protected $config;

	public function __construct(ConfigService $config)
	{
		$this->config = $config;
	}
}