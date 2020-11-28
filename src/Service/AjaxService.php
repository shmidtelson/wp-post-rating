<?php

declare(strict_types=1);

namespace WPR\Service;

use DateTime;
use WPR\Dto\ErrorResponseDto;
use WPR\Dto\SuccessResponseDto;
use WPR\Exception\ValidationException;

class AjaxService
{
    /**
     * @var RatingService
     */
    private $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    public function actionVote(): void
    {
        check_ajax_referer(ConfigService::PLUGIN_NONCE_KEY, 'nonce');

        $postId = intval(sanitize_text_field($_POST['post_id']));
        $vote = intval(sanitize_text_field($_POST['vote']));

        try {
            $this->validateData($postId, $vote);
        } catch (ValidationException $e) {
            echo json_encode(new ErrorResponseDto($e->getMessage()));
            wp_die();
        }

        $latestVote = $this->ratingService->getUserLatestVoteByPostId($postId);

        $action = $this->saveVote($latestVote, $vote, $postId);

        echo json_encode(new SuccessResponseDto([
            'avg' => $this->ratingService->getAvgRating($postId, 0),
            'total' => $this->ratingService->getTotalVotesByPostId($postId),
            'action' => $action,
        ]));
        wp_die();
    }

    private function saveVote(array $latestVote, int $vote, int $postId): string
    {
        if (count($latestVote)) {
            $now = new DateTime();
            $date = new DateTime($latestVote[0]->created_at);
            $date_limit = $date->modify(ConfigService::PLUGIN_VOTE_INTERVAL);

            if ($now < $date_limit) {
                $this->ratingService->save($vote, $postId, $latestVote[0]->id);

                return 'updated';
            }
        }

        $this->ratingService->save($vote, $postId);

        return'created';
    }

    /**
     * @param int $postId
     * @param int $vote
     *
     * @throws ValidationException
     */
    private function validateData(int $postId, int $vote): void
    {
        if (!($postId > 0)) {
            throw new ValidationException('Post_id mush more than 0');
        }

        if (!($vote > 0 && $vote < 6)) {
            throw new ValidationException('Vote mush more than 1 and less then 5');
        }
    }
}
