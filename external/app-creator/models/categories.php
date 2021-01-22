<?php
/**
 * @package     app-creator
 * @author      App Creator Team
 * @copyright   2013-2015 App Creator
 * @version     1.1.0
 */
class App_Creator_Categories extends App_Creator_Default {

    public function getDatas() {
        return $this->getCategories(0, 1);
    }

    public function getCategories($cat, $level) {

        $next = get_categories('hide_empty=true&orderby=name&order=ASC&parent=' . $cat);

        $categories = array();
        if ($next) {
            foreach ($next as $category) {

                $datas = array(
                    'id' => $category->term_id,
                    'title' => $category->name,
                    'description' => $category->description,
                    'count_posts' => $category->count,
                    'level' => $level
                );

                if($level == 0) $categories[$category->term_id]['category'] = $datas;
                else $categories[$category->term_id] = $datas;

                $posts = $this->getPosts($category->term_id);

                $subcategories = $this->getCategories($category->term_id, $level+1);
                if(!empty($posts)) {
                    $categories[$category->term_id]['post_ids'] = array();
                    foreach($posts as $post) {
                        $categories[$category->term_id]['post_ids'][] = $post['id'];
                    }
                }
                if(!empty($subcategories)) $categories[$category->term_id]['children'] = $subcategories;
            }
        }

        return $categories;

    }

}
