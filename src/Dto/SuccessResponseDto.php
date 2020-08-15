<?php

namespace WPR\Dto;

class SuccessResponseDto
{
    public $status = 'ok';
    public $data;

    public function __construct(array $data)
    {
        $this->setData($data);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }
}
