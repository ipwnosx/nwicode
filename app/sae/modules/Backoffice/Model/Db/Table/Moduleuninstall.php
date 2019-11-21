<?php

class Backoffice_Model_Db_Table_Moduleuninstall extends Core_Model_Db_Table
{
    
    protected $_name = "module";
    
    protected $_is_installed = true;
    
    public function __construct($options = array()) {
        parent::__construct($options);
        try {
            $this->_db->describeTable($this->_name);
        }  catch(Exception $e) {
            $this->_is_installed = false;
        }
        return $this;
    }


    public function getModuleCode($name = null)
    {
        $module_code="";
        $query_option = "SELECT `option_id`,`code` FROM `application_option` WHERE `name`='$name'";
        $res_option = $this->_db->fetchAll($query_option);
        return $res_option;
    }
    
    public function clearScrap($module_option_id,$module_name)
    {
        $res="";
        $res[]=$this->_db->delete('application_option_value', [
            'option_id = ?' => $module_option_id,
        ]);
        $res[]=$this->_db->delete('application_option_layout', [
            'option_id = ?' => $module_option_id,
        ]);
        $res[]=$this->_db->delete('application_option', [
            'option_id = ?' => $module_option_id,
        ]);
        $res[]=$this->_db->delete('module', [
            'name = ?' => "$module_name",
        ]);
        $query_option = "SELECT resource_id FROM `acl_resource` WHERE `label`='$module_name'";
        $res_option = $this->_db->fetchAll($query_option);
        $resource_id = $res_option[0]['resource_id'];
        $res[]=$this->_db->delete('acl_resource_role', [
            'resource_id = ?' => $resource_id,
        ]);
        $res[]=$this->_db->delete('acl_resource', [
            'label = ?' => "$module_name",
        ]);
        return $res;
    }
    
    public function tablesDeletion($tables)
    {
        
        $CORE_TABLES = [
        'acl_resource',
        'acl_resource_role',
        'acl_role',
        'admin',
        'api_key',
        'api_provider',
        'api_user',
        'application',
        'application_acl_option',
        'application_admin',
        'application_device',
        'application_layout_homepage',
        'application_option',
        'application_option_category',
        'application_option_layout',
        'application_option_preview',
        'application_option_preview_language',
        'application_option_value',
        'application_tc',
        'backoffice_notification',
        'backoffice_user',
        'booking',
        'booking_store',
        'catalog_category',
        'catalog_product',
        'catalog_product_folder_category',
        'catalog_product_format',
        'catalog_product_group',
        'catalog_product_group_option',
        'catalog_product_group_option_value',
        'catalog_product_group_value',
        'cms_application_block',
        'cms_application_page',
        'cms_application_page_block',
        'cms_application_page_block_address',
        'cms_application_page_block_button',
        'cms_application_page_block_file',
        'cms_application_page_block_image',
        'cms_application_page_block_image_library',
        'cms_application_page_block_slider',
        'cms_application_page_block_text',
        'cms_application_page_block_video',
        'cms_application_page_block_video_link',
        'cms_application_page_block_video_podcast',
        'cms_application_page_block_video_youtube',
        'comment',
        'comment_answer',
        'comment_like',
        'comment_radius',
        'contact',
        'customer',
        'customer_address',
        'customer_social',
        'customer_social_post',
        'event',
        'event_custom',
        'folder',
        'folder_category',
        'form',
        'form_field',
        'form_section',
        'inbox_reply',
        'inbox_message',
        'inbox_customer_message',
        'log',
        'loyalty_card',
        'loyalty_card_customer',
        'loyalty_card_customer_log',
        'loyalty_card_password',
        'maps',
        'mcommerce',
        'mcommerce_cart',
        'mcommerce_cart_line',
        'mcommerce_delivery_method',
        'mcommerce_order',
        'mcommerce_order_line',
        'mcommerce_payment_method',
        'mcommerce_store',
        'mcommerce_store_delivery_method',
        'mcommerce_store_payment_method',
        'mcommerce_store_payment_method_paypal',
        'mcommerce_store_payment_method_stripe',
        'mcommerce_store_printer',
        'mcommerce_store_tax',
        'mcommerce_tax',
        'media_gallery_image',
        'media_gallery_image_custom',
        'media_gallery_image_instagram',
        'media_gallery_image_picasa',
        'media_gallery_music',
        'media_gallery_music_album',
        'media_gallery_music_elements',
        'media_gallery_music_track',
        'media_gallery_video',
        'media_gallery_video_itunes',
        'media_gallery_video_vimeo',
        'media_gallery_video_youtube',
        'media_library',
        'media_library_image',
        'message_application',
        'message_application_file',
        'module',
        'padlock',
        'padlock_value',
        'promotion',
        'promotion_customer',
        'push_apns_devices',
        'push_certificate',
        'push_delivered_message',
        'push_gcm_devices',
        'push_messages',
        'push_message_global',
        'radio',
        'rss_feed',
        'sales_invoice',
        'sales_invoice_line',
        'sales_order',
        'sales_order_line',
        'session',
        'social_facebook',
        'socialgaming_game',
        'source_code',
        'subscription',
        'subscription_acl_resource',
        'subscription_application',
        'subscription_application_detail',
        'system_config',
        'tax',
        'template_block',
        'template_block_app',
        'template_block_white_label_editor',
        'template_category',
        'template_design',
        'template_design_block',
        'template_design_category',
        'template_design_content',
        'topic',
        'topic_category',
        'topic_category_message',
        'topic_subscription',
        'weather',
        'weblink',
        'weblink_link',
        'whitelabel_editor',
        'wordpress',
        'wordpress_category',
        'firebase_credential',
    ];
        $this->_db->query('SET foreign_key_checks = 0');
        $res="";
        foreach ($tables as $key => $table) {
            if (!in_array($table,$CORE_TABLES)) {
                $res[] = $this->_db->query("DROP TABLE IF EXISTS `$table`");
            }
        }
        $this->_db->query('SET foreign_key_checks = 1');
        
        return true;
    }
}
