<?php
declare(strict_types=1);

namespace WPR\Views;

use WPR\Service\ConfigService;
use WPR\Service\RatingService;

class RatingView extends AbstractView
{
    /**
     * @var RatingService
     */
    private $service;

    public function __construct(RatingService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function renderStars()
    {
        $id = get_the_ID();

        return $this->twig->render('star-rating.twig', [
            'postId' => $id,
            'title' => get_the_title($id),
            'avgRating' => $this->service->getAvgRating($id),
            'total' => $this->service->getTotalVotesByPostId($id),
            'pluginName' => ConfigService::PLUGIN_NAME,
        ]);
    }

    /**
     * @return float|int|mixed
     */
    public function getRatingAvg()
    {
        return $this->service->getAvgRating(get_the_ID());
    }

    /**
     * @return int
     */
    public function getRatingTotal()
    {
        return $this->service->getTotalVotesByPostId(get_the_ID());
    }
}
