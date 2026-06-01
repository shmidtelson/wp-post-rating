<?php

declare(strict_types=1);

namespace WPR\Views;

use WPR\Service\RatingService;
use WPR\Service\SettingService;
use WPR\Service\WordpressFunctionsService;
use WPR\Template\TemplateRenderer;

class RatingView
{
    public function __construct(
        private readonly WordpressFunctionsService $wordpressService,
        private readonly RatingService $ratingService,
        private readonly SchemaOrgView $schemaView,
        private readonly TemplateRenderer $templates,
        private readonly SettingService $settingService,
    ) {
    }

    public function renderStars(): string
    {
        $id = $this->wordpressService->getCurrentPostID();
        $settings = $this->settingService->getSetting();

        return $this->templates->render('star-rating', [
            'postId' => $id,
            'avgRating' => (string) $this->ratingService->getAvgRating($id),
            'total' => $this->ratingService->getTotalVotesByPostId($id),
            'jsonMarkup' => $this->schemaView->getJSONLD(),
            'starsColor' => $settings->getStarsMainColor(),
            'textColor' => $settings->getStarsTextColor(),
            'backgroundColor' => $settings->getStarsTextBackgroundColor(),
        ]);
    }

    public function getRatingAvg()
    {
        return $this->ratingService->getAvgRating($this->wordpressService->getCurrentPostID());
    }

    public function getRatingTotal()
    {
        return $this->ratingService->getTotalVotesByPostId($this->wordpressService->getCurrentPostID());
    }
}
