<?php

declare(strict_types=1);

namespace WPR\Views;

use WPR\Service\RatingService;
use WPR\Service\WordpressFunctionsService;
use WPR\Abstractions\Abstracts\AbstractView;
use WPR\Abstractions\Traits\GetSettingsServiceTrait;

class SchemaOrgView extends AbstractView
{
    use GetSettingsServiceTrait;

    public function getJSONLD(): string
    {
        $settingsEntity = $this->getSettings()->getSetting();

        if ($settingsEntity->isSchemaEnable()) {
            $postId = $this->container->get(WordpressFunctionsService::class)->getCurrentPostID();

            return $this->twig->getTwig()->render('star-rating-schema.twig', [
                'title' => get_the_title($postId),
                'thumbnail' => get_the_post_thumbnail_url($postId),
                'ratingCount' => $this->container->get(RatingService::class)->getTotalVotesByPostId($postId),
                'ratingAvg' => $this->container->get(RatingService::class)->getAvgRating($postId),
            ]);
        }

        return '';
    }
}
