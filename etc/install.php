<?php
register_activation_hook( FILE_XFORUM, function( ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'xforum_log';

    // xforum_log
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOH;
        dbDelta($q);
    }

    // xforum_api_login
    $table = $wpdb->prefix . 'xforum_api_login';
    if ($wpdb->get_var("show tables like '$table'") != $table) {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $q = <<<EOQ
CREATE TABLE `$table` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `domain` varchar(64) NOT NULL,
  `code` varchar(255) NOT NULL,
  `stamp` int(10) UNSIGNED NOT NULL,
  `ip` char(15) NOT NULL,
  `uid` varchar(255) NOT NULL,
  `email` varchar(128) NOT NULL,
  `nickname` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY(`id`),
  KEY `user_domain` (`user_id`, `domain`),
  KEY `code_domain` (`code`, `domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
EOQ;
        dbDelta($q);
    }
} );

