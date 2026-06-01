<?php

declare(strict_types=1);

namespace WPR\Views;

use WPR\Service\RatingService;
use WPR\Service\SettingService;
use WPR\Service\WordpressFunctionsService;
use WPR\Template\TemplateRenderer;

class SchemaOrgView
{
    public function __construct(
        private readonly TemplateRenderer $templates,
        private readonly SettingService $settingService,
        private readonly RatingService $ratingService,
        private readonly WordpressFunctionsService $wordpressService,
    ) {
    }

    public function getJSONLD(): string
    {
        $settingsEntity = $this->settingService->getSetting();

        if (! $settingsEntity->isSchemaEnable()) {
            return '';
        }

        $postId = $this->wordpressService->getCurrentPostID();

        return $this->templates->render('star-rating-schema', [
            'title' => get_the_title($postId),
            'thumbnail' => get_the_post_thumbnail_url($postId),
            'ratingCount' => $this->ratingService->getTotalVotesByPostId($postId),
            'ratingAvg' => (string) $this->ratingService->getAvgRating($postId),
        ]);
    }
}
