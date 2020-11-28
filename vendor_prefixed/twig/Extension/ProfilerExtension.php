<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\Extension;

use WPR_Vendor\Twig\Profiler\NodeVisitor\ProfilerNodeVisitor;
use WPR_Vendor\Twig\Profiler\Profile;
class ProfilerExtension extends \WPR_Vendor\Twig\Extension\AbstractExtension
{
    private $actives = [];
    public function __construct(\WPR_Vendor\Twig\Profiler\Profile $profile)
    {
        $this->actives[] = $profile;
    }
    /**
     * @return void
     */
    public function enter(\WPR_Vendor\Twig\Profiler\Profile $profile)
    {
        $this->actives[0]->addProfile($profile);
        \array_unshift($this->actives, $profile);
    }
    /**
     * @return void
     */
    public function leave(\WPR_Vendor\Twig\Profiler\Profile $profile)
    {
        $profile->leave();
        \array_shift($this->actives);
        if (1 === \count($this->actives)) {
            $this->actives[0]->leave();
        }
    }
    public function getNodeVisitors() : array
    {
        return [new \WPR_Vendor\Twig\Profiler\NodeVisitor\ProfilerNodeVisitor(static::class)];
    }
}
