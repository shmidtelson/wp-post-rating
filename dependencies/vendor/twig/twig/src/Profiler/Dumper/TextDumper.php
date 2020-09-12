<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\Profiler\Dumper;

use WPR\Vendor\Twig\Profiler\Profile;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class TextDumper extends \WPR\Vendor\Twig\Profiler\Dumper\BaseDumper
{
    protected function formatTemplate(\WPR\Vendor\Twig\Profiler\Profile $profile, $prefix) : string
    {
        return \sprintf('%s└ %s', $prefix, $profile->getTemplate());
    }
    protected function formatNonTemplate(\WPR\Vendor\Twig\Profiler\Profile $profile, $prefix) : string
    {
        return \sprintf('%s└ %s::%s(%s)', $prefix, $profile->getTemplate(), $profile->getType(), $profile->getName());
    }
    protected function formatTime(\WPR\Vendor\Twig\Profiler\Profile $profile, $percent) : string
    {
        return \sprintf('%.2fms/%.0f%%', $profile->getDuration() * 1000, $percent);
    }
}
