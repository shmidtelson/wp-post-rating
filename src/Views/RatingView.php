<?php

declare(strict_types=1);

namespace WPR\Views;

use WPR\Service\RatingService;
use WPR\Service\TwigEnvironmentService;
use WPR\Service\WordpressFunctionsService;

class RatingView
{
    /**
     * @var WordpressFunctionsService
     */
    private $wordpressService;

    /**
     * @var RatingService
     */
    private $ratingService;

    /**
     * @var SchemaOrgView
     */
    private $schemaView;

    /**
     * @var TwigEnvironmentService
     */
    private $twigService;

    public function __construct(
        WordpressFunctionsService $wordpressService,
        RatingService $ratingService,
        SchemaOrgView $schemaView,
        TwigEnvironmentService $twigService
    ) {
        $this->wordpressService = $wordpressService;
        $this->ratingService = $ratingService;
        $this->schemaView = $schemaView;
        $this->twigService = $twigService;
    }

    public function renderStars()
    {
        $id = $this->wordpressService->getCurrentPostID();

        return $this->twigService->getTwig()->render('star-rating.twig', [
            'postId' => $id,
            'title' => get_the_title($id),
            'avgRating' => $this->ratingService->getAvgRating($id),
            'total' => $this->ratingService->getTotalVotesByPostId($id),
            'jsonMarkup' => $this->schemaView->getJSONLD(),
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
