<?php

namespace WPR_Plugin;

class WPR_Widget extends \WP_Widget
{
    public $min_posts_count = 1;
    public $max_posts_count = 10;

    function __construct($config)
    {
        $this->config = $config;

        parent::__construct(
            'wpr_widget',
            __('List posts by rating', $config->PLUGIN_NAME),
            ['description' => __('You may order displayed posts', $config->PLUGIN_NAME),]
        );
    }

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);

        $posts = $this->get_posts((object)[
            'count' => $instance['count_posts'],
            'orderby' => $instance['orderby'],
            'sort' => $instance['sort'],
        ]);
//        echo '<pre>';
//var_dump($instance);
//        echo '</pre>';
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        echo $this->render_posts($posts, $instance['hwrap'], $instance['pwrap'], $instance['date_format']);

        echo $args['after_widget'];
    }

    public function form($instance)
    {

        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', $this->config->PLUGIN_NAME);
        }

        if (isset($instance['count_posts'])) {
            $count_posts = $instance['count_posts'];
        } else {
            $count_posts = 1;
        }

        if (isset($instance['orderby'])) {
            $orderby = $instance['orderby'];
        } else {
            $orderby = 'date';
        }

        if (isset($instance['sort'])) {
            $sort = $instance['sort'];
        } else {
            $sort = 'asc';
        }

        if (isset($instance['hwrap'])) {
            $hwrap = $instance['hwrap'];
        } else {
            $hwrap = '<h2>list posts:</h2><ul>[posts]</ul>';
        }

        if (isset($instance['pwrap'])) {
            $pwrap = $instance['pwrap'];
        } else {
            $pwrap = '<li>[author_name] voted [stars] for [post_title] at [date]</li>';
        }

        if (isset($instance['date_format'])) {
            $date_format = $instance['date_format'];
        } else {
            $date_format = 'd F, Y';
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', $this->config->PLUGIN_NAME); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('count_posts'); ?>"><?php _e('Count of posts', $this->config->PLUGIN_NAME); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('count_posts'); ?>"
                   name="<?php echo $this->get_field_name('count_posts'); ?>" type="number"
                   value="<?php echo esc_attr($count_posts); ?>"
                   min="<?php echo $this->min_posts_count ?>"
                   max="<?php echo $this->max_posts_count ?>"
                   step="1"
            />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Order by', $this->config->PLUGIN_NAME); ?></label>
            <select name="<?php echo $this->get_field_name('orderby'); ?>"
                    id="<?php echo $this->get_field_id('orderby'); ?>">
                <option value="date" <?= ($orderby == 'date') ? 'selected' : '' ?>>Date</option>
            </select>

            <label for="<?php echo $this->get_field_id('sort'); ?>"><?php _e('Sort by', $this->config->PLUGIN_NAME); ?></label>
            <select name="<?php echo $this->get_field_name('sort'); ?>"
                    id="<?php echo $this->get_field_id('sort'); ?>">
                <option value="asc" <?= ($sort == 'asc') ? 'selected' : '' ?>>ASC</option>
                <option value="desc" <?= ($sort == 'desc') ? 'selected' : '' ?>>DESC</option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('hwrap'); ?>"><?php _e('Wrapper posts', $this->config->PLUGIN_NAME); ?></label>
            <textarea
                    class="widefat"
                    id="<?php echo $this->get_field_id('hwrap'); ?>"
                    name="<?php echo $this->get_field_name('hwrap'); ?>"
            ><?php echo $hwrap; ?></textarea>
            <small>
                <?php _e('Wrapper for all posts, supports shortcodes: ', $this->config->PLUGIN_NAME) ?>
                <code>[posts]</code>
            </small>
        </p>


        <p>
            <label for="<?php echo $this->get_field_id('pwrap'); ?>"><?php _e('Wrapper one post', $this->config->PLUGIN_NAME); ?></label>
            <textarea
                    class="widefat"
                    id="<?php echo $this->get_field_id('pwrap'); ?>"
                    name="<?php echo $this->get_field_name('pwrap'); ?>"
            ><?php echo $pwrap; ?></textarea>
            <small>
                <?php _e('Wrapper for one post, supports shortcodes: ', $this->config->PLUGIN_NAME) ?> <code>[date]
                    [stars] [post_title] [author_name]</code>
            </small>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('date_format'); ?>"><?php _e('Date format', $this->config->PLUGIN_NAME); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('date_format'); ?>"
                   name="<?php echo $this->get_field_name('date_format'); ?>" type="text"
                   value="<?php echo esc_attr($date_format); ?>"/>
            <small><?php _e('Dates formats is here <a href="https://codex.wordpress.org/Formatting_Date_and_Time">https://codex.wordpress.org/Formatting_Date_and_Time</a>', $this->config->PLUGIN_NAME) ?></small>
        </p>

        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['count_posts'] = (!empty($new_instance['count_posts'])) ? strip_tags($new_instance['count_posts']) : '';
        $instance['orderby'] = (!empty($new_instance['orderby'])) ? strip_tags($new_instance['orderby']) : '';
        $instance['sort'] = (!empty($new_instance['sort'])) ? strip_tags($new_instance['sort']) : '';
        $instance['hwrap'] = (!empty($new_instance['hwrap'])) ? $new_instance['hwrap'] : '';
        $instance['pwrap'] = (!empty($new_instance['pwrap'])) ? $new_instance['pwrap'] : '';
        $instance['date_format'] = (!empty($new_instance['date_format'])) ? strip_tags($new_instance['date_format']) : '';

        return $instance;
    }

    public function get_posts($obj)
    {
        global $wpdb;

        $results = $wpdb->get_results("
        SELECT post_id, user_id, created_at, vote
        FROM {$this->config->PLUGIN_FULL_TABLE_NAME}
        {$this->queryGetOrder($obj->orderby, $obj->sort)}
        {$this->queryGetLimit($obj->count)}
", ARRAY_A);

        return $results;
    }

    public function queryGetLimit($limit)
    {
        if ($limit > 10) $limit = $this->max_posts_count;
        if ($limit < 1) $limit = $this->min_posts_count;
        return "LIMIT 0, {$limit}";
    }

    public function queryGetOrder($orderby = 'date', $sort = 'asc')
    {
        if ($orderby == 'date') $orderby = 'created_at';
        if ($sort == 'asc') $sort = 'ASC';
        if ($sort == 'desc') $sort = 'DESC';
        return "ORDER BY {$orderby} {$sort}";
    }

    public function render_posts($posts, $html_wrapper, $post_wrapper, $date_format)
    {
        $html = '';

        foreach ($posts as $post) {
            $w = $post_wrapper;

            $date = date_i18n($date_format, strtotime($post['created_at']));
            $stars =
                '<small class="wpr_rating_list">'
                . str_repeat('<span class="icon-star"></span>', $post['vote'])
                . '</small>';

            $post_name = "<a href=" . get_the_permalink($post['post_id']) . ">" . get_the_title($post['post_id']) . "</a>";
            $user = get_user_by('ID', $post['user_id']);
            $user_name = ($user) ? $user->display_name : __('Guest', $this->config->PLUGIN_NAME);

            $w = str_replace('[date]', $date, $w);
            $w = str_replace('[stars]', $stars, $w);
            $w = str_replace('[post_title]', $post_name, $w);
            $w = str_replace('[author_name]', $user_name, $w);

            $html .= $w;
        }

        $html = str_replace('[posts]', $html, $html_wrapper);

        return $html;
    }

}