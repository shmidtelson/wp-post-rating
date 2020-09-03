<?php

declare(strict_types=1);

namespace WPR\Service;

class WordpressFunctionsService
{
    public function getCurrentPostID(): int
    {
        global $post;

        if (property_exists($post, 'ID')) {
            return intval($post->ID);
        }

        return 1;
    }
}