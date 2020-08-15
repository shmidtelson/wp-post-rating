<?php

declare(strict_types=1);

namespace WPR\Dto;

class ErrorResponseDto
{
    public $status = 'error';
    public $message;

    public function __construct(string $message)
    {
        $this->setMessage($message);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }
}
