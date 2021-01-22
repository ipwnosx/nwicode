<?php
/**
 * @package     app-creator
 * @author      App Creator Team
 * @copyright   2013-2015 App Creator
 * @version     1.1.0
 */
class App_Creator_Default {

    public function getPosts($cat_id = '') {

        global $post, $wp_query;

        unset($wp_query->query['cat']);
        if (!empty($cat_id))
            $query['cat'] = $cat_id;

        $query['posts_per_page'] = 100;

        query_posts($query);

        $output = array();
        while (have_posts()) {

            the_post();

            if ($post->post_status != 'publish') continue;

            $category_ids = $this->_getCategoryIds($post->ID);

            $content = get_the_content();
            $content = apply_filters('the_content', $content);
            $content = str_replace(']]>', ']]&gt;', $content);

            $featured_image = null;

            if( has_post_thumbnail( $post->ID ) ) {
                $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
                if(is_array($featured_image) AND !empty($featured_image[0])) {
                    $featured_image = $featured_image[0];
                }
            }

            if(!empty($category_ids)) {
                $datas = array(
                    'id' => $post->ID,
                    'title' => $post->post_title,
                    'featured_image' => $featured_image,
                    'description' => $content,
                    'short_description' => strip_tags(apply_filters('the_excerpt', get_the_excerpt())),
                    'date' => $post->post_date,
                );

                $datas['category_ids'] = $category_ids;

                $output[$post->ID] = $datas;
            }
        }

        return $output;
    }

    protected function _getCategoryIds($post_id) {

        $category_ids = array();
        if ($categories = get_the_category($post_id)) {
            foreach ($categories as $category) {
//                if ($category->term_id != 1 AND $category->slug != 'uncategorized') {
                    $category_ids[] = $category->term_id;
//                }
            }
        }

        return $category_ids;
    }

}
