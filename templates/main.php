<?php
$id = get_the_ID();
$avg = $this->database->get_avg_rating($id, 0);
$total = $this->database->get_total_votes($id)
?>
<div class="wpr-wrapp" id="wpr-widget-<?php echo $id ?>" itemscope itemtype="http://schema.org/WebPage">
    <div class="wpr-rating" data-id="<?php echo $id ?>" itemprop="aggregateRating" itemscope
         itemtype="http://schema.org/AggregateRating">
        <?php for ($i = 5; $i > 0; $i--): ?>
            <span class="icon-star<?php echo ($i == $avg) ? ' checked' : '' ?>" data-value="<?php echo $i ?>"
                  title="<?=sprintf(__('Vote %s', $this->config->PLUGIN_NAME), $i)?> "></span>
        <?php endfor ?>
        <meta itemprop="worstRating" content="1">
        <meta itemprop="bestRating" content="5">
        <meta itemprop="reviewCount" content="<?php echo $total ?>">
        <meta itemprop="ratingValue" content="<?php echo $avg; ?>">
    </div>
    <div class="wpr-rating-loader wpr-hide">
        <i class="icon-spin4 animate-spin"></i>
    </div>
    <div class="wpr-info-container">
        <?php if ($total): ?>
            <span><?php _e('Votes', $this->config->PLUGIN_NAME) ?>&nbsp;</span>
            <span class="wpr-total">(<?php echo $total ?>)</span>
        <?php else: ?>
            <span><?php _e('Vote', $this->config->PLUGIN_NAME) ?></span>
            <span class="wpr-total"></span>
        <?php endif; ?>
    </div>
</div>
