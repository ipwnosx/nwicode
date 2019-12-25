<?php
$requests = [
    "DELETE FROM application_option WHERE code = 'shopify';",
    "DELETE FROM application_option WHERE code = 'volusion';",
    "DELETE FROM application_option WHERE code = 'prestashop';",
    "DELETE FROM application_option WHERE code = 'woocommerce';",
    "DELETE FROM application_option WHERE code = 'magento';",
    "DELETE FROM acl_resource WHERE code = 'feature_shopify';",
    "DELETE FROM acl_resource WHERE code = 'feature_volusion';",
    "DELETE FROM acl_resource WHERE code = 'feature_prestashop';",
    "DELETE FROM acl_resource WHERE code = 'feature_woocommerce';",
    "DELETE FROM acl_resource WHERE code = 'feature_magento';",
];

foreach ($requests as $request) {
    try {
        $this->query($request);
    } catch (\Exception $e) {
    }
}