<?php
register_activation_hook( FILE_XFORUM, function( ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'xforum_log';
    if ($wpdb->get_var("show tables like '{$table_name}'") != $table_name) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $q = <<<EOH
        CREATE TABLE IF NOT EXISTS `$table_name` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(64) CHARACTER SET utf8 DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `code` varchar(64) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `stamp` int(10) unsigned NOT NULL DEFAULT '0',
  `etc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `action_object` (`action`, `object_id`),
  KEY `object_action` (`object_id`, `action`),
  KEY `user_object_action` (`user_id`,`object_id`,`action`),
  KEY `user_action` (`user_id`,`action`),
  KEY `code` (`code`)
);
EOH;
        dbDelta($q);

    }
});
