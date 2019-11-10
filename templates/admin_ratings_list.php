<div class="wrap">
    <a href="?page=wpr-settings" class="page-title-action"><?php _e('Settings', $this->config->PLUGIN_NAME); ?></a>
    <h1 class="wp-heading-inline">
        <?php _e('Stars rating list', $this->config->PLUGIN_NAME); ?>
    </h1>
    <div id="wpr-wp-ratings-list-table">
        <div id="wpr-post-body">
            <form id="wpr-list-form" method="post">
                <?php $this->user_list_table->display(); ?>
            </form>
        </div>
    </div>
</div>