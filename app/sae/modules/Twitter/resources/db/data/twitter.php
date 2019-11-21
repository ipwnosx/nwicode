<?php
$name = "Twitter";
$category = "social";

# Install icons
$icons = array(
    '/twitter/twitter1-flat.png'
);

$result = Nwicode_Feature::installIcons($name, $icons);

# Install the Feature
$data = array(
    'library_id'    => $result["library_id"],
    'icon_id'       => $result["icon_id"],
    'code'          => "twitter",
    'name'          => $name,
    'model'         => "Twitter_Model_Twitter",
    'desktop_uri'   => "twitter/application_twitter/",
    'mobile_uri'    => "twitter/mobile_twitter_list/",
    'only_once'     => 0,
    'is_ajax'       => 1,
    'position'      => 210
);

$option = Nwicode_Feature::install($category, $data, array('code'));
Nwicode_Feature::installAcl($option);

# Icons Flat
$icons = array(
    '/twitter/twitter1-flat.png',
    '/twitter/twitter2-flat.png',
    '/twitter/twitter3-flat.png',
);

Nwicode_Feature::installIcons("{$name}-flat", $icons);