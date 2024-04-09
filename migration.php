<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/DotEnv.php';

(new DotEnv())->load();

$dbConnection = Database::getConnection();

$statement = 'CREATE TABLE IF NOT EXISTS `post_notifications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';

$dbConnection->exec($statement);
