<?php

namespace WPR\Views;


class RatingView extends AbstractView
{
    public function renderRating()
    {
        return $this->twig->render('star-rating.twig', ['star'=>'STTTTARRR']);
    }
}