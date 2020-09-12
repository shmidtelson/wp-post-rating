<?php

declare(strict_types=1);

namespace WPR\Service;

use DateTime;
use WPR\Dto\ErrorResponseDto;
use WPR\Dto\SuccessResponseDto;
use WPR\Exception\ValidationException;
use WPR\Abstractions\Abstracts\AbstractService;

class AjaxService extends AbstractService
{
    public function getRatingService(): RatingService
    {
        return $this->container->get(RatingService::class);
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

        $latestVote = $this->getRatingService()->getUserLatestVoteByPostId($postId);

        $action = $this->saveVote($latestVote, $vote, $postId);

        echo json_encode(new SuccessResponseDto([
            'avg' => $this->getRatingService()->getAvgRating($postId, 0),
            'total' => $this->getRatingService()->getTotalVotesByPostId($postId),
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
                $this->getRatingService()->save($vote, $postId, $latestVote[0]->id);

                return 'updated';
            }
        }

        $this->getRatingService()->save($vote, $postId);

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
