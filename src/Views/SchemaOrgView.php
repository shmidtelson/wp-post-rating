<?php

declare(strict_types=1);

namespace WPR\Views;

use WPR\Service\RatingService;
use WPR\Service\SettingService;
use WPR\Service\WordpressFunctionsService;

class SchemaOrgView extends AbstractView
{
    /**
     * @var SettingService
     */
    private $settingService;

    /**
     * @var RatingService
     */
    private $serviceRating;
    /**
     * @var WordpressFunctionsService
     */
    private $wordpressFunctionsService;

    public function __construct(
        SettingService $settingService,
        RatingService $serviceRating,
        WordpressFunctionsService $wordpressFunctionsService
    ) {
        parent::__construct();
        $this->settingService = $settingService;
        $this->serviceRating = $serviceRating;
        $this->wordpressFunctionsService = $wordpressFunctionsService;
    }

    public function getJSONLD(): string
    {
        $settingsEntity = $this->settingService->getSetting();

        if ($settingsEntity->isSchemaEnable()) {
            $postId = $this->wordpressFunctionsService->getCurrentPostID();

            return $this->twig->render('star-rating-schema.twig', [
                'title' => get_the_title($postId),
                'thumbnail' => get_the_post_thumbnail_url($postId),
                'ratingCount' => $this->serviceRating->getTotalVotesByPostId($postId),
                'ratingAvg' => $this->serviceRating->getAvgRating($postId),
            ]);
        }

        return '';
    }
}
