<?php
// WebView module un-installer!
$name = 'WebView';

// Clean-up library icons!
Nwicode_Feature::removeIcons($name);
Nwicode_Feature::removeIcons($name . '-flat');

// Clean-up Layouts!
$layout_data = [1];
$slug = 'webview';

Nwicode_Feature::removeLayouts($option->getId(), $slug, $layout_data);

// Clean-up Option(s)/Feature(s)!
$code = 'webview';
Nwicode_Feature::uninstallFeature($code);

// Clean-up DB be really carefull with this!
$tables = [
    'webview'
];
Nwicode_Feature::dropTables($tables);

// Clean-up module!
Nwicode_Feature::uninstallModule($name);