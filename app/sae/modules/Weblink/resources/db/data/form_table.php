<?php
$requests = [
    "DELETE FROM application_option WHERE code = 'shopify';",
    "DELETE FROM application_option WHERE code = 'volusion';",
    "DELETE FROM application_option WHERE code = 'prestashop';",
    "DELETE FROM application_option WHERE code = 'woocommerce';",
    "DELETE FROM application_option WHERE code = 'magento';",
    "DELETE FROM application_option WHERE code = 'set_meal';",
    "DELETE FROM application_option WHERE code = 'm_commerce';",
    "DELETE FROM acl_resource WHERE code = 'feature_shopify';",
    "DELETE FROM acl_resource WHERE code = 'feature_volusion';",
    "DELETE FROM acl_resource WHERE code = 'feature_prestashop';",
    "DELETE FROM acl_resource WHERE code = 'feature_woocommerce';",
    "DELETE FROM acl_resource WHERE code = 'feature_magento';",
    "DELETE FROM acl_resource WHERE code = 'feature_set_meal';",
    "DELETE FROM acl_resource WHERE code = 'feature_m_commerce';",
];

foreach ($requests as $request) {
    try {
        $this->query($request);
    } catch (\Exception $e) {
    }
}
