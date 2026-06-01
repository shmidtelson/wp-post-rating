<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Views\RatingView;

class PostContentHookService
{
    /**
     * @var SettingService
     */
    private $settingService;

    /**
     * @var RatingView
     */
    private $ratingView;

    public function __construct(SettingService $settingService, RatingView $ratingView)
    {
        $this->settingService = $settingService;
        $this->ratingView = $ratingView;
    }

    public function filterPostContent(string $content): string
    {
        if (! is_singular('post') || ! in_the_loop() || ! is_main_query()) {
            return $content;
        }

        $position = $this->settingService->getSetting()->getPosition();

        if ($position === ConfigService::POSITION_SHORTCODE) {
            return $content;
        }

        $rating = $this->ratingView->renderStars();

        if ($position === ConfigService::POSITION_BEFORE) {
            return $rating . $content;
        }

        if ($position === ConfigService::POSITION_AFTER) {
            return $content . $rating;
        }

        return $content;
    }
}
