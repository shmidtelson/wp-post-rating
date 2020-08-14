<?php
declare(strict_types=1);

namespace WPR\Dto;

class SettingDto
{
    public $position = 'shortcode';
    public $starsMainColor = '#fdd835';
    public $starsSecondColor = '#fbc02d';
    public $starsTextColor = '#000';
    public $starsTextBackgroundColor = '#fff';

    public function loadData(array $data)
    {
        $this->setPosition($data['position']);
        $this->setStarsMainColor($data['starsMainColor']);
        $this->setStarsSecondColor($data['starsSecondColor']);
        $this->setStarsTextColor($data['starsTextColor']);
        $this->setStarsTextBackgroundColor($data['starsTextBackgroundColor']);
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position): void
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getStarsMainColor()
    {
        return $this->starsMainColor;
    }

    /**
     * @param mixed $starsMainColor
     */
    public function setStarsMainColor($starsMainColor): void
    {
        $this->starsMainColor = $starsMainColor;
    }

    /**
     * @return mixed
     */
    public function getStarsSecondColor()
    {
        return $this->starsSecondColor;
    }

    /**
     * @param mixed $starsSecondColor
     */
    public function setStarsSecondColor($starsSecondColor): void
    {
        $this->starsSecondColor = $starsSecondColor;
    }

    /**
     * @return mixed
     */
    public function getStarsTextColor()
    {
        return $this->starsTextColor;
    }

    /**
     * @param mixed $starsTextColor
     */
    public function setStarsTextColor($starsTextColor): void
    {
        $this->starsTextColor = $starsTextColor;
    }

    /**
     * @return mixed
     */
    public function getStarsTextBackgroundColor()
    {
        return $this->starsTextBackgroundColor;
    }

    /**
     * @param mixed $starsTextBackgroundColor
     */
    public function setStarsTextBackgroundColor($starsTextBackgroundColor): void
    {
        $this->starsTextBackgroundColor = $starsTextBackgroundColor;
    }
}