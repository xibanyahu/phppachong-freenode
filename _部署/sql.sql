CREATE TABLE `box_country` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `box_option` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `k` varchar(150) DEFAULT NULL,
  `v` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_k` (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `crawl_freenode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  `group` varchar(50) DEFAULT NULL,
  `url_base` varchar(255) DEFAULT NULL,
  `url_node` varchar(255) DEFAULT NULL,
  `url_file` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`url_file`)),
  `file_real` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`file_real`)),
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `dog_option` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `dog_name` varchar(255) DEFAULT NULL,
  `k` varchar(255) DEFAULT NULL,
  `v` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_this_name_k` (`dog_name`,`k`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `dove_site` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `www` varchar(120) DEFAULT NULL,
  `api_path` varchar(100) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `class` varchar(100) DEFAULT NULL,
  `dove_checked` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `freenode_pool` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content`)),
  `content_custom` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content_custom`)),
  `content_v2ray` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content_v2ray`)),
  `content_clash` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `option` (
  `k` varchar(255) NOT NULL,
  `v` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`k`),
  UNIQUE KEY `k` (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `site_map` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(120) DEFAULT NULL,
  `code_short` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `url_front` varchar(255) DEFAULT NULL,
  `group_code` varchar(120) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `api_path` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`api_path`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ship` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(80) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `www` varchar(255) DEFAULT NULL,
  `api_huodong` varchar(255) DEFAULT NULL,
  `good` text DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `site_map` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(120) DEFAULT NULL,
  `code_short` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `url_front` varchar(255) DEFAULT NULL,
  `group_code` varchar(120) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`config`)),
  `api_path` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`api_path`)),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `site_path_map` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) DEFAULT NULL,
  `site_group_code` varchar(120) DEFAULT NULL,
  `path_group_code` varchar(120) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_group_code` (`site_group_code`,`path_group_code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `telegram_key` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  `key` varchar(100) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
