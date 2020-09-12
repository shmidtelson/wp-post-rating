<?php

declare(strict_types=1);

namespace WPR\Views;

use WPR\Service\RatingService;
use WPR\Service\WordpressFunctionsService;
use WPR\Abstractions\Abstracts\AbstractView;

class RatingView extends AbstractView
{
    public function renderStars()
    {
        $id = $this->container->get(WordpressFunctionsService::class)->getCurrentPostID();

        return $this->twig->getTwig()->render('star-rating.twig', [
            'postId' => $id,
            'title' => \get_the_title($id),
            'avgRating' => $this->container->get(RatingService::class)->getAvgRating($id),
            'total' => $this->container->get(RatingService::class)->getTotalVotesByPostId($id),
            'jsonMarkup' => $this->container->get(SchemaOrgView::class)->getJSONLD(),
        ]);
    }

    /**
     * @return float|int|mixed
     */
    public function getRatingAvg()
    {
        return $this->container->get(RatingService::class)->getAvgRating($this->container->get(WordpressFunctionsService::class)->getCurrentPostID());
    }

    /**
     * @return int
     */
    public function getRatingTotal()
    {
        return $this->container->get(RatingService::class)->getTotalVotesByPostId($this->container->get(WordpressFunctionsService::class)->getCurrentPostID());
    }
}
