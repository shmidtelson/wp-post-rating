<?php

declare(strict_types=1);

namespace WPR\Views;

use WPR\Service\RatingService;
use WPR\Service\WordpressFunctionsService;

class RatingView extends AbstractView
{
    /**
     * @var RatingService
     */
    private $service;

    /**
     * @var SchemaOrgView
     */
    private $viewSchemaOrg;
    /**
     * @var WordpressFunctionsService
     */
    private $wordpressFunctionsService;

    public function __construct(
        RatingService $service,
        SchemaOrgView $viewSchemaOrg,
        WordpressFunctionsService $wordpressFunctionsService
    )
    {
        parent::__construct();
        $this->service = $service;
        $this->viewSchemaOrg = $viewSchemaOrg;
        $this->wordpressFunctionsService = $wordpressFunctionsService;
    }

    public function renderStars()
    {
        $id = $this->wordpressFunctionsService->getCurrentPostID();

        return $this->twig->render('star-rating.twig', [
            'postId' => $id,
            'title' => \get_the_title($id),
            'avgRating' => $this->service->getAvgRating($id),
            'total' => $this->service->getTotalVotesByPostId($id),
            'jsonMarkup' => $this->viewSchemaOrg->getJSONLD(),
        ]);
    }

    /**
     * @return float|int|mixed
     */
    public function getRatingAvg()
    {
        return $this->service->getAvgRating($this->wordpressFunctionsService->getCurrentPostID());
    }

    /**
     * @return int
     */
    public function getRatingTotal()
    {
        return $this->service->getTotalVotesByPostId($this->wordpressFunctionsService->getCurrentPostID());
    }
}
