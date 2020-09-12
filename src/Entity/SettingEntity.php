<?php

declare(strict_types=1);

namespace WPR\Entity;

class SettingEntity
{
    public $position = 'shortcode';
    public $starsMainColor = '#fdd835';
    public $starsSecondColor = '#fbc02d';
    public $starsTextColor = '#000';
    public $starsTextBackgroundColor = '#fff';
    public $schemaEnable = true;

    public function loadData(array $data)
    {
        $this->setPosition($data['position']);
        $this->setStarsMainColor($data['starsMainColor']);
        $this->setStarsSecondColor($data['starsSecondColor']);
        $this->setStarsTextColor($data['starsTextColor']);
        $this->setStarsTextBackgroundColor($data['starsTextBackgroundColor']);
        $this->setSchemaEnable($data['schemaEnable']);
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

    /**
     * @return bool
     */
    public function isSchemaEnable(): bool
    {
        return $this->schemaEnable;
    }

    /**
     * @param bool $schemaEnable
     */
    public function setSchemaEnable(bool $schemaEnable): void
    {
        $this->schemaEnable = $schemaEnable;
    }
}
