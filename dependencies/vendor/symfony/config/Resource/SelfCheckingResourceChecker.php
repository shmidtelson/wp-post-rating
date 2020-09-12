<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Symfony\Component\Config\Resource;

use WPR\Vendor\Symfony\Component\Config\ResourceCheckerInterface;
/**
 * Resource checker for instances of SelfCheckingResourceInterface.
 *
 * As these resources perform the actual check themselves, we can provide
 * this class as a standard way of validating them.
 *
 * @author Matthias Pigulla <mp@webfactory.de>
 */
class SelfCheckingResourceChecker implements \WPR\Vendor\Symfony\Component\Config\ResourceCheckerInterface
{
    public function supports(\WPR\Vendor\Symfony\Component\Config\Resource\ResourceInterface $metadata)
    {
        return $metadata instanceof \WPR\Vendor\Symfony\Component\Config\Resource\SelfCheckingResourceInterface;
    }
    public function isFresh(\WPR\Vendor\Symfony\Component\Config\Resource\ResourceInterface $resource, int $timestamp)
    {
        /* @var SelfCheckingResourceInterface $resource */
        return $resource->isFresh($timestamp);
    }
}
