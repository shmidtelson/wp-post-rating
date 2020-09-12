<?php

declare(strict_types=1);

namespace WPR\Views;

use WPR\Service\RatingService;
use WPR\Service\SettingService;
use WPR\Service\TwigEnvironmentService;
use WPR\Service\WordpressFunctionsService;

class SchemaOrgView
{
    /**
     * @var TwigEnvironmentService
     */
    private $twigService;

    /**
     * @var SettingService
     */
    private $settingService;

    /**
     * @var RatingService
     */
    private $ratingService;

    /**
     * @var WordpressFunctionsService
     */
    private $wordpressService;

    public function __construct(
        TwigEnvironmentService $twigService,
        SettingService $settingService,
        RatingService $ratingService,
        WordpressFunctionsService $wordpressService
    ) {
        $this->twigService = $twigService;
        $this->settingService = $settingService;
        $this->ratingService = $ratingService;
        $this->wordpressService = $wordpressService;
    }

    public function getJSONLD(): string
    {
        $settingsEntity = $this->settingService->getSetting();

        if ($settingsEntity->isSchemaEnable()) {
            $postId = $this->wordpressService->getCurrentPostID();

            return $this->twigService->getTwig()->render('star-rating-schema.twig', [
                'title' => get_the_title($postId),
                'thumbnail' => get_the_post_thumbnail_url($postId),
                'ratingCount' => $this->ratingService->getTotalVotesByPostId($postId),
                'ratingAvg' => $this->ratingService->getAvgRating($postId),
            ]);
        }

        return '';
    }
}
