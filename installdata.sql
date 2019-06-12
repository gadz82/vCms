
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table applicazioni
# ------------------------------------------------------------

DROP TABLE IF EXISTS `applicazioni`;

CREATE TABLE `applicazioni` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipologia_applicazione` tinyint(2) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `titolo` varchar(75) NOT NULL DEFAULT '',
  `codice` char(5) NOT NULL DEFAULT '',
  `href_lang` varchar(6) NOT NULL DEFAULT '',
  `descrizione` text DEFAULT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utente_admin` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_tipologia_applicazione` (`id_tipologia_applicazione`),
  KEY `id_tipologia_stato` (`id_tipologia_stato`),
  KEY `id_utente_admin` (`id_utente_admin`),
  KEY `codice` (`codice`),
  CONSTRAINT `applicazioni_ibfk_1` FOREIGN KEY (`id_tipologia_applicazione`) REFERENCES `tipologie_applicazione` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `applicazioni_ibfk_2` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_applicazione` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `applicazioni_ibfk_3` FOREIGN KEY (`id_utente_admin`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `applicazioni` WRITE;
/*!40000 ALTER TABLE `applicazioni` DISABLE KEYS */;

INSERT INTO `applicazioni` (`id`, `id_tipologia_applicazione`, `id_tipologia_stato`, `titolo`, `codice`, `href_lang`, `descrizione`, `data_creazione`, `data_aggiornamento`, `id_utente_admin`, `attivo`)
VALUES
	(1,1,1,'Sito Italiano','it','it','Sito Italiano','2017-04-26 17:35:01','2019-06-07 11:47:01',1,1);

/*!40000 ALTER TABLE `applicazioni` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table applicazioni_domini
# ------------------------------------------------------------

DROP TABLE IF EXISTS `applicazioni_domini`;

CREATE TABLE `applicazioni_domini` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_applicazione` tinyint(2) unsigned NOT NULL,
  `referer` varchar(150) NOT NULL DEFAULT '',
  `ip_autorizzati` char(255) DEFAULT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `referer` (`referer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table applicazioni_routes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `applicazioni_routes`;

CREATE TABLE `applicazioni_routes` (
  `id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `id_applicazione` tinyint(2) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `id_tipologia_route` tinyint(2) unsigned NOT NULL,
  `nome` varchar(150) DEFAULT NULL,
  `path` varchar(255) DEFAULT '',
  `params` text NOT NULL,
  `ordine` tinyint(5) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_applicazione` (`id_applicazione`),
  KEY `id_tipologia_route` (`id_tipologia_route`),
  KEY `id_tipologia_stato` (`id_tipologia_stato`),
  CONSTRAINT `applicazioni_routes_ibfk_1` FOREIGN KEY (`id_applicazione`) REFERENCES `applicazioni` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `applicazioni_routes_ibfk_3` FOREIGN KEY (`id_tipologia_route`) REFERENCES `tipologie_routes` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `applicazioni_routes_ibfk_4` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_applicazione_route` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `applicazioni_routes` WRITE;
/*!40000 ALTER TABLE `applicazioni_routes` DISABLE KEYS */;

INSERT INTO `applicazioni_routes` (`id`, `id_applicazione`, `id_tipologia_stato`, `id_tipologia_route`, `nome`, `path`, `params`, `ordine`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,1,1,1,'Home Page','/','{\"module\":\"site\",\"controller\":\"index\",\"action\":\"index\"}',1,'2019-06-06 11:05:19','2019-06-06 11:58:23',1),
	(2,1,1,1,'404','/404','{\"module\":\"site\",\"controller\":\"errors\",\"action\":\"show404\"}',2,'2019-06-06 11:39:42','2019-06-06 12:04:29',1),
	(3,1,1,1,'Pagina','/{post_slug:[a-z\\-]+}','{\"module\":\"site\",\"controller\":\"entity\",\"action\":\"read\",\"post_type_slug\":\"pagina\",\"params\":1}',3,'2019-06-06 11:40:35','2019-06-06 12:04:29',1),
	(4,1,1,1,'List Tipologia Post','/{post_type_slug:[a-z\\-]+}/','{\"module\":\"site\",\"controller\":\"list\",\"action\":\"list\",\"post_type_slug\":1}',4,'2019-06-06 11:42:45','2019-06-06 12:04:30',1),
	(5,1,1,1,'List Tipologia Post Filtrata','/{post_type_slug:[a-z\\-]+}/:action/:params','{\"module\":\"site\",\"controller\":\"list\",\"action\":2,\"post_type_slug\":1,\"params\":3}',5,'2019-06-06 11:44:56','2019-06-06 12:04:31',1),
	(6,1,1,1,'Dettaglio Post','/{post_type_slug:[a-z\\-]+}/{post_slug:[a-z0-9\\-]+}','{\"module\":\"site\",\"controller\":\"entity\",\"action\":\"read\",\"post_type_slug\":1,\"post_slug\":2}',6,'2019-06-06 11:47:53','2019-06-06 12:04:32',1),
	(7,1,1,1,'PDF Post','/{post_type_slug:[a-z\\-]+}/{post_slug:[a-z0-9\\-]+}.pdf','{\"module\":\"site\",\"controller\":\"pdf\",\"action\":\"read\",\"post_type_slug\":1,\"post_slug\":2}',7,'2019-06-06 11:48:48','2019-06-06 12:04:33',1),
	(8,1,1,1,'User Area','/user','{\"module\":\"site\",\"controller\":\"users\",\"action\":\"index\"}',8,'2019-06-06 11:49:36','2019-06-06 12:04:34',1),
	(9,1,1,1,'User Area Azione Specifica','/user/:action','{\"module\":\"site\",\"controller\":\"users\",\"action\":1,\"params\":2}',9,'2019-06-06 11:50:03','2019-06-06 12:04:35',1),
	(10,1,1,1,'Endpoint Ajax','/ajax/:action/:params','{\"module\":\"site\",\"controller\":\"ajax\",\"action\":1,\"params\":2}',10,'2019-06-06 11:50:43','2019-06-06 12:04:37',1),
	(11,1,1,1,'Form Request','/forms/:action/:params','{\"module\":\"site\",\"controller\":\"forms\",\"action\":1,\"params\":2}',11,'2019-06-06 11:54:51','2019-06-06 12:04:40',1),
	(12,1,1,1,'Rendering Media','/media/:action/:params','{\"module\":\"site\",\"controller\":\"media\",\"action\":1,\"params\":2}',12,'2019-06-06 11:55:18','2019-06-06 12:04:41',1),
	(13,1,1,1,'Sitemap','/sitemap.xml','{\"module\":\"site\",\"controller\":\"sitemap\",\"action\":\"index\"}',13,'2019-06-06 11:55:59','2019-06-06 12:04:41',1),
	(14,1,1,1,'Api Root','/api','{\"module\":\"api\",\"controller\":\"api\",\"action\":\"index\"}',14,'2019-06-12 09:52:08','2019-06-12 10:02:09',1),
	(15,1,1,1,'Api Controller','/api/:controller','{\"module\":\"api\",\"controller\":1,\"action\":\"index\"}',15,'2019-06-12 09:53:47','2019-06-12 10:02:11',1),
	(16,1,1,1,'Api Taxonomy services','/api/taxonomies/:action(/:params)','{\"module\":\"api\",\"controller\":\"taxonomy\",\"action\":1,\"params\":2}',16,'2019-06-12 09:54:42','2019-06-12 10:02:13',1),
	(17,1,1,1,'Listing Post Type','/api/entities/{post_type_slug:[a-z\\-]+}/','{\"module\":\"api\",\"controller\":\"list\",\"action\":\"fetch\",\"post_type_slug\":1}',17,'2019-06-12 09:55:36','2019-06-12 10:02:16',1),
	(18,1,1,1,'Listing Post Type with Filters','/api/entities/{post_type_slug:[a-z\\-]+}/:action/:params','{\"module\":\"api\",\"controller\":\"list\",\"action\":2,\"post_type_slug\":1,\"params\":3}',18,'2019-06-12 09:56:18','2019-06-12 10:02:19',1),
	(19,1,1,1,'Api Entity Detail','/api/entities/read/{post_type_slug:[a-z\\-]+}/{post_slug:[0-9{11}]+}','{\"module\":\"api\",\"controller\":\"entity\",\"action\":\"read\",\"post_type_slug\":1,\"params\":2}',19,'2019-06-12 09:57:13','2019-06-12 10:02:22',1);

/*!40000 ALTER TABLE `applicazioni_routes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table applicazioni_utenti
# ------------------------------------------------------------

DROP TABLE IF EXISTS `applicazioni_utenti`;

CREATE TABLE `applicazioni_utenti` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_applicazione` tinyint(2) unsigned NOT NULL,
  `id_utente_applicazione` tinyint(4) unsigned NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_applicazione` (`id_applicazione`),
  KEY `id_utente` (`id_utente_applicazione`),
  CONSTRAINT `applicazioni_utenti_ibfk_1` FOREIGN KEY (`id_applicazione`) REFERENCES `applicazioni` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `applicazioni_utenti_ibfk_2` FOREIGN KEY (`id_utente_applicazione`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table blocks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `blocks`;

CREATE TABLE `blocks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_applicazione` tinyint(2) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `id_tipologia_block` tinyint(2) unsigned NOT NULL,
  `id_block_tag` tinyint(2) unsigned NOT NULL,
  `titolo` varchar(150) NOT NULL DEFAULT '',
  `key` varchar(75) NOT NULL,
  `content` text NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `data_inizio_pubblicazione` datetime DEFAULT NULL,
  `data_fine_pubblicazione` datetime DEFAULT NULL,
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`,`id_applicazione`,`attivo`),
  KEY `fk_blocks_applicazioni` (`id_applicazione`),
  KEY `fk_blocks_utenti` (`id_utente`),
  KEY `fk_blocks_tipologie_stato_block` (`id_tipologia_stato`),
  KEY `fk_blocks_tipologie_block` (`id_tipologia_block`),
  KEY `id_block_tag` (`id_block_tag`),
  KEY `key_2` (`key`),
  CONSTRAINT `blocks_ibfk_1` FOREIGN KEY (`id_block_tag`) REFERENCES `blocks_tags` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_blocks_applicazioni` FOREIGN KEY (`id_applicazione`) REFERENCES `applicazioni` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_blocks_tipologie_block` FOREIGN KEY (`id_tipologia_block`) REFERENCES `tipologie_block` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_blocks_tipologie_stato_block` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_block` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_blocks_utenti` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

LOCK TABLES `blocks` WRITE;
/*!40000 ALTER TABLE `blocks` DISABLE KEYS */;

INSERT INTO `blocks` (`id`, `id_applicazione`, `id_tipologia_stato`, `id_tipologia_block`, `id_block_tag`, `titolo`, `key`, `content`, `data_creazione`, `data_aggiornamento`, `data_inizio_pubblicazione`, `data_fine_pubblicazione`, `id_utente`, `attivo`)
VALUES
	(1,1,1,3,4,'Menu','menu','[\r\n    {\r\n        \"title\": \"Home Page\",\r\n        \"class\": \"hidden-sm\",\r\n        \"href\": \"/\",\r\n        \"submenu\": null\r\n    }\r\n]','2019-02-07 12:11:39','2019-06-10 15:02:52','2019-02-07 12:11:39',NULL,1,1),
	(2,1,1,2,1,'Custom CSS','custom-css','body{\r\n  \r\n}','2019-03-11 16:47:28','2019-06-12 16:57:37','2019-03-11 16:47:28',NULL,1,1);

/*!40000 ALTER TABLE `blocks` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table blocks_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `blocks_history`;

CREATE TABLE `blocks_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_block` int(11) unsigned NOT NULL,
  `id_applicazione` tinyint(2) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `id_tipologia_block` tinyint(2) unsigned NOT NULL,
  `id_block_tag` tinyint(2) unsigned NOT NULL,
  `titolo` varchar(150) NOT NULL DEFAULT '',
  `key` varchar(75) NOT NULL,
  `content` text NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `data_inizio_pubblicazione` datetime DEFAULT NULL,
  `data_fine_pubblicazione` datetime DEFAULT NULL,
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`),
  KEY `key` (`key`),
  KEY `fk_blocks_applicazioni` (`id_applicazione`),
  KEY `fk_blocks_utenti` (`id_utente`),
  KEY `fk_blocks_tipologie_stato_block` (`id_tipologia_stato`),
  KEY `fk_blocks_tipologie_block` (`id_tipologia_block`),
  KEY `id_block_tag` (`id_block_tag`),
  KEY `id_block` (`id_block`),
  CONSTRAINT `blocks_history_ibfk_1` FOREIGN KEY (`id_block`) REFERENCES `blocks` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;



# Dump of table blocks_tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `blocks_tags`;

CREATE TABLE `blocks_tags` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` varchar(150) NOT NULL DEFAULT '',
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`),
  KEY `fk_blocks_utenti` (`id_utente`),
  CONSTRAINT `blocks_tags_ibfk_4` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

LOCK TABLES `blocks_tags` WRITE;
/*!40000 ALTER TABLE `blocks_tags` DISABLE KEYS */;

INSERT INTO `blocks_tags` (`id`, `descrizione`, `data_creazione`, `data_aggiornamento`, `id_utente`, `attivo`)
VALUES
	(1,'Non Categorizzato','2019-03-04 00:00:00','2019-06-12 16:58:12',1,1),
	(2,'Home','2019-03-04 15:30:33','2019-06-12 16:58:12',1,1),
	(3,'News','2019-03-04 16:05:01','2019-06-12 16:58:13',1,1),
	(4,'Menu','2019-03-11 13:04:43','2019-06-12 16:58:14',1,1);

/*!40000 ALTER TABLE `blocks_tags` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table files
# ------------------------------------------------------------

DROP TABLE IF EXISTS `files`;

CREATE TABLE `files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `original_filename` varchar(100) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `filename` varchar(100) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `filetype` char(20) COLLATE utf8_swedish_ci NOT NULL,
  `filesize` int(10) unsigned NOT NULL,
  `filepath` varchar(255) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `fileurl` varchar(255) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `priorita` tinyint(2) unsigned NOT NULL,
  `alt` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `private` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_tipologia_stato` (`id_tipologia_stato`),
  KEY `filename` (`filename`),
  CONSTRAINT `files_ibfk_1` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_file` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `files` WRITE;
/*!40000 ALTER TABLE `files` DISABLE KEYS */;

INSERT INTO `files` (`id`, `id_tipologia_stato`, `original_filename`, `filename`, `filetype`, `filesize`, `filepath`, `fileurl`, `priorita`, `alt`, `private`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,1,'pagina01.jpg','20190603_b3ca6c42c4290cb6454c0f4cc9a79011.jpg','image/jpeg',164850,'/public/files/','http://gustour.local/files/20190603_b3ca6c42c4290cb6454c0f4cc9a79011.jpg',1,'test',0,'2019-06-03 09:13:13','2019-06-03 10:39:44',0);

/*!40000 ALTER TABLE `files` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table files_sizes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `files_sizes`;

CREATE TABLE `files_sizes` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `key` char(50) NOT NULL,
  `max_width` int(11) unsigned NOT NULL,
  `max_height` int(11) unsigned NOT NULL,
  `crop` tinyint(1) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `files_sizes` WRITE;
/*!40000 ALTER TABLE `files_sizes` DISABLE KEYS */;

INSERT INTO `files_sizes` (`id`, `key`, `max_width`, `max_height`, `crop`, `attivo`)
VALUES
	(1,'small',400,300,1,1),
	(2,'medium',640,480,1,1),
	(3,'large',1024,768,1,1),
	(4,'16:9',1024,576,1,1),
	(5,'thumb_square',160,160,0,1);

/*!40000 ALTER TABLE `files_sizes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table files_users_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `files_users_groups`;

CREATE TABLE `files_users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_file` int(11) unsigned NOT NULL,
  `id_user_group` mediumint(4) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_post` (`id_file`),
  KEY `id_user_group` (`id_user_group`),
  KEY `attivo` (`attivo`),
  CONSTRAINT `files_users_groups_ibfk_2` FOREIGN KEY (`id_user_group`) REFERENCES `users_groups` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `files_users_groups_ibfk_3` FOREIGN KEY (`id`) REFERENCES `files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table filtri
# ------------------------------------------------------------

DROP TABLE IF EXISTS `filtri`;

CREATE TABLE `filtri` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_applicazione` tinyint(2) unsigned NOT NULL,
  `id_filtri_group` tinyint(2) unsigned NOT NULL,
  `id_tipologia_filtro` tinyint(2) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `id_filtro_parent` int(11) unsigned DEFAULT NULL,
  `key` varchar(175) NOT NULL,
  `one_to_one` tinyint(1) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `frontend_filter` tinyint(1) NOT NULL,
  `titolo` char(100) NOT NULL,
  `descrizione` text NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_filtri_filtri_group` (`id_filtri_group`),
  KEY `fk_filtri_tipologie_filtro` (`id_tipologia_filtro`),
  KEY `fk_filtri_tipologie_stato_filtro` (`id_tipologia_stato`),
  KEY `fk_filtri_utenti` (`id_utente`),
  KEY `fk_filtri_filtri` (`id_filtro_parent`),
  KEY `key` (`key`),
  KEY `fk_filtri_applicazioni` (`id_applicazione`),
  CONSTRAINT `fk_filtri_applicazioni` FOREIGN KEY (`id_applicazione`) REFERENCES `applicazioni` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_filtri_filtri` FOREIGN KEY (`id_filtro_parent`) REFERENCES `filtri` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_filtri_filtri_group` FOREIGN KEY (`id_filtri_group`) REFERENCES `filtri_group` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_filtri_tipologie_filtro` FOREIGN KEY (`id_tipologia_filtro`) REFERENCES `tipologie_filtro` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_filtri_tipologie_stato_filtro` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_filtro` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_filtri_utenti` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `filtri` WRITE;
/*!40000 ALTER TABLE `filtri` DISABLE KEYS */;

INSERT INTO `filtri` (`id`, `id_applicazione`, `id_filtri_group`, `id_tipologia_filtro`, `id_tipologia_stato`, `id_filtro_parent`, `key`, `one_to_one`, `required`, `frontend_filter`, `titolo`, `descrizione`, `data_creazione`, `data_aggiornamento`, `id_utente`, `attivo`)
VALUES
	(1,1,1,2,1,NULL,'categoria',1,1,1,'Categoria','Categoria News','2017-12-27 14:31:39','2019-06-12 16:58:42',1,1);

/*!40000 ALTER TABLE `filtri` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table filtri_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `filtri_group`;

CREATE TABLE `filtri_group` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` varchar(75) NOT NULL DEFAULT '',
  `priorita` tinyint(3) NOT NULL DEFAULT 10,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utente` tinyint(4) NOT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `filtri_group` WRITE;
/*!40000 ALTER TABLE `filtri_group` DISABLE KEYS */;

INSERT INTO `filtri_group` (`id`, `descrizione`, `priorita`, `data_creazione`, `data_aggiornamento`, `id_utente`, `attivo`)
VALUES
	(1,'Tassonomie News',1,'2017-12-27 14:30:42','2019-06-12 16:58:46',1,1);

/*!40000 ALTER TABLE `filtri_group` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table filtri_group_post_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `filtri_group_post_type`;

CREATE TABLE `filtri_group_post_type` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipologia_post` tinyint(2) unsigned NOT NULL,
  `id_filtri_group` tinyint(2) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_meta_group_post_type_tipologie_post` (`id_tipologia_post`),
  KEY `fk_meta_group_post_type_meta_group` (`id_filtri_group`),
  CONSTRAINT `fk_filtri_group_post_type_filtri_group` FOREIGN KEY (`id_filtri_group`) REFERENCES `filtri_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_filtri_group_post_type_tipologie_post` FOREIGN KEY (`id_tipologia_post`) REFERENCES `tipologie_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `filtri_group_post_type` WRITE;
/*!40000 ALTER TABLE `filtri_group_post_type` DISABLE KEYS */;

INSERT INTO `filtri_group_post_type` (`id`, `id_tipologia_post`, `id_filtri_group`, `attivo`)
VALUES
	(1,1,1,1);

/*!40000 ALTER TABLE `filtri_group_post_type` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table filtri_valori
# ------------------------------------------------------------

DROP TABLE IF EXISTS `filtri_valori`;

CREATE TABLE `filtri_valori` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_filtro` int(11) unsigned NOT NULL,
  `id_filtro_valore_parent` int(11) unsigned DEFAULT NULL,
  `valore` varchar(255) NOT NULL,
  `key` varchar(75) NOT NULL,
  `meta_title` varchar(125) DEFAULT NULL,
  `meta_description` varchar(275) DEFAULT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `FK_filtri_valori_filtri_valori` (`id_filtro_valore_parent`),
  KEY `FK_filtri_valori_filtri` (`id_filtro`),
  KEY `key` (`key`),
  KEY `numeric_key` (`meta_title`),
  KEY `valore` (`valore`),
  CONSTRAINT `FK_filtri_valori_filtri` FOREIGN KEY (`id_filtro`) REFERENCES `filtri` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_filtri_valori_filtri_valori` FOREIGN KEY (`id_filtro_valore_parent`) REFERENCES `filtri_valori` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `filtri_valori` WRITE;
/*!40000 ALTER TABLE `filtri_valori` DISABLE KEYS */;

INSERT INTO `filtri_valori` (`id`, `id_filtro`, `id_filtro_valore_parent`, `valore`, `key`, `meta_title`, `meta_description`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,1,NULL,'News','news','',NULL,'2017-12-27 14:32:47','2017-12-27 15:32:47',1);

/*!40000 ALTER TABLE `filtri_valori` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table flat_translations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `flat_translations`;

CREATE TABLE `flat_translations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_applicazione` tinyint(2) unsigned NOT NULL,
  `original_string` varchar(255) NOT NULL,
  `translation` varchar(255) NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `original_string` (`original_string`),
  KEY `attivo` (`attivo`),
  KEY `fk_flat_translations_applicazioni` (`id_applicazione`),
  KEY `fk_flat_translations_utenti` (`id_utente`),
  CONSTRAINT `fk_flat_translations_applicazioni` FOREIGN KEY (`id_applicazione`) REFERENCES `applicazioni` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_flat_translations_utenti` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table form_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `form_fields`;

CREATE TABLE `form_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_form` int(11) unsigned NOT NULL,
  `id_tipologia_form_fields` tinyint(2) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `label` varchar(100) NOT NULL,
  `placeholder` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `obbligatorio` tinyint(2) unsigned NOT NULL,
  `ordine` tinyint(2) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`),
  KEY `fk_form_fields_forms` (`id_form`),
  KEY `name` (`name`),
  KEY `fk_form_fields_utenti` (`id_utente`),
  KEY `fk_form_fields_tipologie_form_fields` (`id_tipologia_form_fields`),
  KEY `fk_form_fields_tipologie_stato_form_fields` (`id_tipologia_stato`),
  CONSTRAINT `fk_form_fields_forms` FOREIGN KEY (`id_form`) REFERENCES `forms` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_form_fields_tipologie_form_fields` FOREIGN KEY (`id_tipologia_form_fields`) REFERENCES `tipologie_form_fields` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_form_fields_tipologie_stato_form_fields` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_form_fields` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_form_fields_utenti` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `form_fields` WRITE;
/*!40000 ALTER TABLE `form_fields` DISABLE KEYS */;

INSERT INTO `form_fields` (`id`, `id_form`, `id_tipologia_form_fields`, `id_tipologia_stato`, `name`, `label`, `placeholder`, `value`, `obbligatorio`, `ordine`, `data_creazione`, `data_aggiornamento`, `id_utente`, `attivo`)
VALUES
	(1,1,1,1,'nome','Nome','','',1,1,'2019-06-10 16:38:19','2019-06-10 16:38:19',1,1),
	(2,1,1,1,'email','Email','','',1,2,'2019-06-10 16:38:32','2019-06-10 16:38:46',1,1),
	(3,1,1,1,'messaggio','Messaggio','','',0,3,'2019-06-10 16:38:58','2019-06-10 16:38:58',1,1);

/*!40000 ALTER TABLE `form_fields` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table form_requests
# ------------------------------------------------------------

DROP TABLE IF EXISTS `form_requests`;

CREATE TABLE `form_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_post` int(11) unsigned NOT NULL DEFAULT 0,
  `id_form` int(11) unsigned NOT NULL,
  `letto` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`),
  KEY `fk_form_requests_forms` (`id_form`),
  KEY `fk_form_requests_posts` (`id_post`),
  CONSTRAINT `fk_form_requests_forms` FOREIGN KEY (`id_form`) REFERENCES `forms` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_form_requests_posts` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;



# Dump of table form_requests_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `form_requests_fields`;

CREATE TABLE `form_requests_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_form_request` int(11) unsigned NOT NULL DEFAULT 0,
  `id_form` int(11) unsigned NOT NULL,
  `id_form_field` int(11) unsigned NOT NULL,
  `input_value` text NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_form_requests_fields_form_requests` (`id_form_request`),
  KEY `fk_form_requests_fields_forms` (`id_form`),
  KEY `fk_form_requests_fields_form_fields` (`id_form_field`),
  KEY `attivo` (`attivo`),
  CONSTRAINT `fk_form_requests_fields_form_fields` FOREIGN KEY (`id_form_field`) REFERENCES `form_fields` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_form_requests_fields_form_requests` FOREIGN KEY (`id_form_request`) REFERENCES `form_requests` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_form_requests_fields_forms` FOREIGN KEY (`id_form`) REFERENCES `forms` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table forms
# ------------------------------------------------------------

DROP TABLE IF EXISTS `forms`;

CREATE TABLE `forms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_applicazione` tinyint(2) unsigned NOT NULL,
  `id_tipologia_form` tinyint(2) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `titolo` varchar(125) NOT NULL,
  `testo` text NOT NULL,
  `key` varchar(100) NOT NULL,
  `email_to` varchar(175) DEFAULT NULL,
  `email_cc` text DEFAULT NULL,
  `email_bcc` text DEFAULT NULL,
  `invio_utente` tinyint(1) NOT NULL DEFAULT 0,
  `submit_label` varchar(50) NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(4) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_forms_tipologie_form` (`id_tipologia_form`),
  KEY `fk_forms_tipologie_stato_form` (`id_tipologia_stato`),
  KEY `key` (`key`),
  KEY `fk_forms_utenti` (`id_utente`),
  KEY `attivo` (`attivo`),
  KEY `id_applicazione` (`id_applicazione`),
  CONSTRAINT `fk_forms_tipologie_form` FOREIGN KEY (`id_tipologia_form`) REFERENCES `tipologie_form` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_forms_tipologie_stato_form` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_form` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_forms_utenti` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `forms_ibfk_1` FOREIGN KEY (`id_applicazione`) REFERENCES `applicazioni` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `forms` WRITE;
/*!40000 ALTER TABLE `forms` DISABLE KEYS */;

INSERT INTO `forms` (`id`, `id_applicazione`, `id_tipologia_form`, `id_tipologia_stato`, `titolo`, `testo`, `key`, `email_to`, `email_cc`, `email_bcc`, `invio_utente`, `submit_label`, `data_creazione`, `data_aggiornamento`, `id_utente`, `attivo`)
VALUES
	(1,1,1,1,'Contatto','','contatto','mail@dest.it','','',1,'Invia','2019-06-10 16:38:03','2019-06-10 16:38:03',1,1);

/*!40000 ALTER TABLE `forms` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table gruppi
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gruppi`;

CREATE TABLE `gruppi` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipologia_gruppo` tinyint(2) unsigned NOT NULL,
  `descrizione` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`),
  KEY `fk_gruppi_tipologie_gruppo` (`id_tipologia_gruppo`),
  CONSTRAINT `fk_gruppi_tipologie_gruppo` FOREIGN KEY (`id_tipologia_gruppo`) REFERENCES `tipologie_gruppo` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `gruppi` WRITE;
/*!40000 ALTER TABLE `gruppi` DISABLE KEYS */;

INSERT INTO `gruppi` (`id`, `id_tipologia_gruppo`, `descrizione`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,1,'Amministratore','2015-11-17 14:45:37','2015-11-17 16:45:38',1);

/*!40000 ALTER TABLE `gruppi` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table gruppi_utenti
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gruppi_utenti`;

CREATE TABLE `gruppi_utenti` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_gruppo` tinyint(4) unsigned NOT NULL,
  `id_utente` tinyint(4) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`),
  KEY `fk_gruppi_utenti_gruppi` (`id_gruppo`),
  KEY `fk_gruppi_utenti_utenti` (`id_utente`),
  CONSTRAINT `fk_gruppi_utenti_gruppi` FOREIGN KEY (`id_gruppo`) REFERENCES `gruppi` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `fk_gruppi_utenti_utenti` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `gruppi_utenti` WRITE;
/*!40000 ALTER TABLE `gruppi_utenti` DISABLE KEYS */;

INSERT INTO `gruppi_utenti` (`id`, `id_gruppo`, `id_utente`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,1,1,'2015-11-17 14:46:56','2017-04-26 12:22:29',1);

/*!40000 ALTER TABLE `gruppi_utenti` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `meta`;

CREATE TABLE `meta` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_meta_group` tinyint(2) unsigned NOT NULL,
  `id_tipologia_meta` tinyint(2) unsigned NOT NULL,
  `key` varchar(175) NOT NULL DEFAULT '',
  `label` varchar(275) NOT NULL DEFAULT '',
  `priorita` tinyint(3) NOT NULL DEFAULT 10,
  `dataset` text DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `hidden` tinyint(1) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_meta_group` (`id_meta_group`),
  KEY `id_tipologia_meta` (`id_tipologia_meta`),
  KEY `titolo` (`key`),
  CONSTRAINT `meta_ibfk_1` FOREIGN KEY (`id_meta_group`) REFERENCES `meta_group` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `meta_ibfk_2` FOREIGN KEY (`id_tipologia_meta`) REFERENCES `tipologie_meta` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `meta` WRITE;
/*!40000 ALTER TABLE `meta` DISABLE KEYS */;

INSERT INTO `meta` (`id`, `id_meta_group`, `id_tipologia_meta`, `key`, `label`, `priorita`, `dataset`, `required`, `hidden`, `data_creazione`, `data_aggiornamento`, `id_utente`, `attivo`)
VALUES
	(1,1,3,'meta_title','Meta Title',1,'',1,0,'2017-04-26 17:39:36','2017-07-17 12:24:09',1,1),
	(2,1,3,'meta_description','Meta Descrizione',3,'',1,0,'2017-04-27 11:30:29','2017-07-17 12:24:09',1,1),
	(3,1,3,'og_title','Title Open Graph',2,'',0,0,'2017-04-27 11:31:56','2017-07-17 12:24:09',1,1),
	(4,1,4,'og_description','Open Graph Description',4,'',0,0,'2017-04-27 11:32:44','2017-07-17 12:24:09',1,1),
	(5,1,8,'og_image','Open Graph Image',5,'',0,0,'2017-04-27 11:34:42','2017-05-03 16:35:34',1,1),
	(6,1,6,'robots','Meta Robots',6,'index/follow:Indicizza e segui|noindex/follow:non Indicizzare ma segui|index/nofollow:Indicizza ma non seguire|noindex/nofollow:Non indicizzare e non seguire',1,0,'2017-04-27 11:38:52','2017-05-03 16:35:36',1,1),
	(7,2,8,'immagine','Immagine Principale',1,'',0,0,'2017-04-27 11:44:37','2017-10-17 14:48:14',5,1),
	(8,2,9,'immagini_gallery','Galleria Immagini',2,'',0,0,'2017-04-27 11:45:17','2017-10-17 14:48:14',5,1),
	(9,1,3,'video_url','Url Video Vimeo o Facebook',3,'',0,0,'2017-04-27 11:49:56','2017-05-03 16:35:40',1,1),
	(10,1,4,'header_tags','Header Tags Aggiuntivi',7,'',0,0,'2019-06-12 15:25:37','2019-06-12 15:37:58',1,1);

/*!40000 ALTER TABLE `meta` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table meta_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `meta_group`;

CREATE TABLE `meta_group` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` varchar(75) NOT NULL DEFAULT '',
  `priorita` tinyint(3) NOT NULL DEFAULT 10,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `meta_group` WRITE;
/*!40000 ALTER TABLE `meta_group` DISABLE KEYS */;

INSERT INTO `meta_group` (`id`, `descrizione`, `priorita`, `data_creazione`, `data_aggiornamento`, `id_utente`, `attivo`)
VALUES
	(1,'Seo',98,'2017-04-26 17:37:32','2019-06-12 10:48:04',1,1),
	(2,'Media',3,'2017-04-27 11:44:08','2019-03-11 17:07:56',5,1),
	(8,'Gestione Template',0,'2019-03-18 08:50:31','2019-06-12 10:47:44',1,0);

/*!40000 ALTER TABLE `meta_group` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table meta_group_post_type
# ------------------------------------------------------------

DROP TABLE IF EXISTS `meta_group_post_type`;

CREATE TABLE `meta_group_post_type` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipologia_post` tinyint(2) unsigned NOT NULL,
  `id_meta_group` tinyint(2) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_meta_group_post_type_meta_group` (`id_meta_group`),
  KEY `fk_meta_group_post_type_tipologie_post` (`id_tipologia_post`),
  CONSTRAINT `fk_meta_group_post_type_meta_group` FOREIGN KEY (`id_meta_group`) REFERENCES `meta_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_meta_group_post_type_tipologie_post` FOREIGN KEY (`id_tipologia_post`) REFERENCES `tipologie_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `meta_group_post_type` WRITE;
/*!40000 ALTER TABLE `meta_group_post_type` DISABLE KEYS */;

INSERT INTO `meta_group_post_type` (`id`, `id_tipologia_post`, `id_meta_group`, `attivo`)
VALUES
	(1,1,2,1),
	(2,2,2,1),
	(4,2,1,1),
	(32,1,8,1),
	(33,1,1,1);

/*!40000 ALTER TABLE `meta_group_post_type` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table options
# ------------------------------------------------------------

DROP TABLE IF EXISTS `options`;

CREATE TABLE `options` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` char(75) NOT NULL,
  `option_value` text NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_name_unique` (`option_name`,`attivo`),
  KEY `option_name` (`option_name`),
  KEY `FK_options_utenti` (`id_utente`),
  CONSTRAINT `FK_options_utenti` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `options` WRITE;
/*!40000 ALTER TABLE `options` DISABLE KEYS */;

INSERT INTO `options` (`id`, `option_name`, `option_value`, `data_creazione`, `data_aggiornamento`, `id_utente`, `attivo`)
VALUES
	(1,'default_app_code','it','2017-12-22 10:22:27','2019-06-12 16:57:16',1,1),
	(2,'reindex_queue','[\"1\",\"2\"]','2017-12-22 11:14:18','2019-06-12 15:34:13',1,1),
	(7,'app_md5','2019-06-12 11:36:23','2017-12-27 14:36:58','2019-06-12 11:39:45',1,1),
	(26,'columns_map_it_news_meta','[\"immagine\",\"immagini_gallery\",\"meta_title\",\"meta_description\",\"og_title\",\"og_description\",\"og_image\",\"robots\",\"video_url\"]','2019-03-06 10:07:59','2019-06-12 12:07:25',1,1),
	(27,'columns_map_it_news_filter','[\"categoria\",\"key_categoria\"]','2019-03-06 10:07:59','2019-06-12 12:07:25',1,1),
	(28,'columns_map_it_pagina_meta','[\"immagine\",\"immagini_gallery\",\"meta_title\",\"meta_description\",\"og_title\",\"og_description\",\"og_image\",\"robots\",\"video_url\"]','2019-03-06 10:09:36','2019-06-12 11:06:45',1,1),
	(29,'columns_map_it_pagina_filter','[]','2019-03-06 10:09:36','2019-06-12 11:06:45',1,1),
	(40,'version_number_css','15603490305d010966a2d010.22418600','2019-03-08 16:01:19','2019-06-12 16:17:10',1,1),
	(41,'version_number_js','15603490305d010966a61a80.44652032','2019-03-08 16:01:32','2019-06-12 16:17:10',1,1),
	(42,'version_number_assets_collections','15603490305d010966a8bbb6.43939286','2019-03-08 16:01:19','2019-06-12 16:17:10',1,1),
	(53,'columns_map_en_news_meta','[\"immagine\",\"immagini_gallery\"]','2019-06-12 10:09:21','2019-06-12 10:09:21',1,1),
	(54,'columns_map_en_news_filter','[]','2019-06-12 10:09:21','2019-06-12 10:09:21',1,1),
	(55,'columns_map_en_pagina_meta','[\"immagine\",\"immagini_gallery\",\"meta_title\",\"meta_description\",\"og_title\",\"og_description\",\"og_image\",\"robots\",\"video_url\"]','2019-06-12 10:09:23','2019-06-12 10:09:23',1,1),
	(56,'columns_map_en_pagina_filter','[]','2019-06-12 10:09:23','2019-06-12 10:09:23',1,1);

/*!40000 ALTER TABLE `options` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table posts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_applicazione` tinyint(2) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `id_tipologia_post` tinyint(2) unsigned NOT NULL,
  `titolo` varchar(150) NOT NULL DEFAULT '',
  `slug` varchar(75) DEFAULT NULL,
  `excerpt` varchar(275) DEFAULT '',
  `testo` text DEFAULT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data_inizio_pubblicazione` datetime DEFAULT NULL,
  `data_fine_pubblicazione` datetime DEFAULT NULL,
  `id_utente` tinyint(4) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_unique` (`slug`,`id_tipologia_post`,`id_applicazione`,`attivo`),
  KEY `id_tipologia_stato` (`id_tipologia_stato`),
  KEY `id_tipologia_post` (`id_tipologia_post`),
  KEY `id_utente` (`id_utente`),
  KEY `id_applicazione` (`id_applicazione`),
  KEY `slug` (`slug`),
  KEY `attivo` (`attivo`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_post` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`id_tipologia_post`) REFERENCES `tipologie_post` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `posts_ibfk_4` FOREIGN KEY (`id_applicazione`) REFERENCES `applicazioni` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;

INSERT INTO `posts` (`id`, `id_applicazione`, `id_tipologia_stato`, `id_tipologia_post`, `titolo`, `slug`, `excerpt`, `testo`, `data_creazione`, `data_aggiornamento`, `data_inizio_pubblicazione`, `data_fine_pubblicazione`, `id_utente`, `attivo`)
VALUES
	(1,1,1,2,'Home','index','home','<div class=\"clearfix\"><div>Hello World!</div></div>','2018-12-29 12:45:45','2019-06-12 11:34:07','2018-12-29 12:45:45',NULL,1,1),
	(2,1,1,1,'Notizia a','notizia-a','Notizia a','<p>Notizia as<br></p>','2019-02-05 12:25:34','2019-06-12 11:34:08','2019-02-05 00:00:00',NULL,1,1),
	(9,1,1,2,'test','test','test','<p>test</p>','2019-06-12 11:10:18','2019-06-12 11:34:12','2019-06-12 00:00:00',NULL,1,1);

/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table posts_filtri
# ------------------------------------------------------------

DROP TABLE IF EXISTS `posts_filtri`;

CREATE TABLE `posts_filtri` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_post` int(11) unsigned NOT NULL,
  `id_filtro` int(11) unsigned NOT NULL,
  `id_filtro_valore` int(11) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_posts_filtri_posts` (`id_post`),
  KEY `fk_posts_filtri_filtri` (`id_filtro`),
  KEY `fk_posts_filtri_filtri_valori` (`id_filtro_valore`),
  KEY `attivo` (`attivo`),
  CONSTRAINT `fk_posts_filtri_filtri` FOREIGN KEY (`id_filtro`) REFERENCES `filtri` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_posts_filtri_filtri_valori` FOREIGN KEY (`id_filtro_valore`) REFERENCES `filtri_valori` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_filtri_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `posts_filtri` WRITE;
/*!40000 ALTER TABLE `posts_filtri` DISABLE KEYS */;

INSERT INTO `posts_filtri` (`id`, `id_post`, `id_filtro`, `id_filtro_valore`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,2,1,1,'2019-02-05 12:25:34','2019-02-05 12:25:34',1);

/*!40000 ALTER TABLE `posts_filtri` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table posts_meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `posts_meta`;

CREATE TABLE `posts_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(11) unsigned NOT NULL,
  `id_tipologia_post_meta` tinyint(2) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `id_meta` int(11) unsigned NOT NULL,
  `meta_key` varchar(175) NOT NULL DEFAULT '',
  `meta_value_int` int(11) unsigned DEFAULT NULL,
  `meta_value_decimal` decimal(14,6) unsigned DEFAULT NULL,
  `meta_value_varchar` varchar(255) DEFAULT NULL,
  `meta_value_text` mediumtext DEFAULT NULL,
  `meta_value_datetime` datetime DEFAULT NULL,
  `meta_value_files` int(11) unsigned DEFAULT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_id_unique` (`post_id`,`id_meta`,`attivo`,`id_tipologia_stato`),
  KEY `post_id` (`post_id`),
  KEY `id_tipologia_post_meta` (`id_tipologia_post_meta`),
  KEY `id_tipologia_stato` (`id_tipologia_stato`),
  KEY `id_meta` (`id_meta`),
  KEY `meta_key` (`meta_key`),
  KEY `meta_value_files` (`meta_value_files`),
  CONSTRAINT `posts_meta_ibfk_2` FOREIGN KEY (`id_tipologia_post_meta`) REFERENCES `tipologie_meta` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `posts_meta_ibfk_3` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_post_meta` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `posts_meta_ibfk_4` FOREIGN KEY (`id_meta`) REFERENCES `meta` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `posts_meta_ibfk_5` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `posts_meta` WRITE;
/*!40000 ALTER TABLE `posts_meta` DISABLE KEYS */;

INSERT INTO `posts_meta` (`id`, `post_id`, `id_tipologia_post_meta`, `id_tipologia_stato`, `id_meta`, `meta_key`, `meta_value_int`, `meta_value_decimal`, `meta_value_varchar`, `meta_value_text`, `meta_value_datetime`, `meta_value_files`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(23,1,3,1,1,'meta_title',NULL,NULL,'Home',NULL,NULL,NULL,'2017-12-29 12:45:45','2019-06-10 16:45:34',1),
	(24,1,3,1,3,'og_title',NULL,NULL,NULL,NULL,NULL,NULL,'2017-12-29 12:45:45','2019-06-10 16:45:34',1),
	(25,1,3,1,2,'meta_description',NULL,NULL,'Home',NULL,NULL,NULL,'2017-12-29 12:45:45','2019-06-10 16:45:34',1),
	(26,1,3,1,9,'video_url',NULL,NULL,NULL,NULL,NULL,NULL,'2017-12-29 12:45:45','2019-06-10 16:45:34',1),
	(27,1,4,1,4,'og_description',NULL,NULL,NULL,NULL,NULL,NULL,'2017-12-29 12:45:45','2019-06-10 16:45:34',1),
	(28,1,8,1,5,'og_image',NULL,NULL,NULL,NULL,NULL,NULL,'2017-12-29 12:45:45','2019-06-10 16:45:34',1),
	(29,1,6,1,6,'robots',NULL,NULL,'index/follow',NULL,NULL,NULL,'2017-12-29 12:45:45','2019-06-10 16:45:34',1),
	(30,1,8,1,7,'immagine',NULL,NULL,NULL,NULL,NULL,NULL,'2017-12-29 12:45:45','2019-06-10 16:45:34',1),
	(31,1,9,1,8,'immagini_gallery',NULL,NULL,NULL,NULL,NULL,NULL,'2017-12-29 12:45:45','2019-06-10 16:45:34',1),
	(32,2,3,1,1,'meta_title',NULL,NULL,'Notizia a',NULL,NULL,NULL,'2019-02-05 12:25:34','2019-03-07 09:14:54',1),
	(33,2,3,1,3,'og_title',NULL,NULL,NULL,NULL,NULL,NULL,'2019-02-05 12:25:34','2019-03-07 09:14:54',1),
	(34,2,3,1,2,'meta_description',NULL,NULL,'Notizia Pubblica Notizia Pubblica',NULL,NULL,NULL,'2019-02-05 12:25:34','2019-03-07 09:14:54',1),
	(35,2,3,1,9,'video_url',NULL,NULL,NULL,NULL,NULL,NULL,'2019-02-05 12:25:34','2019-03-07 09:14:54',1),
	(36,2,4,1,4,'og_description',NULL,NULL,NULL,NULL,NULL,NULL,'2019-02-05 12:25:34','2019-03-07 09:14:54',1),
	(37,2,8,1,5,'og_image',NULL,NULL,NULL,NULL,NULL,NULL,'2019-02-05 12:25:34','2019-03-07 09:14:54',1),
	(38,2,6,1,6,'robots',NULL,NULL,'index/follow',NULL,NULL,NULL,'2019-02-05 12:25:34','2019-03-07 09:14:54',1),
	(41,2,8,1,7,'immagine',NULL,NULL,NULL,NULL,NULL,2,'2019-02-05 12:25:34','2019-06-10 14:10:19',1),
	(42,2,9,1,8,'immagini_gallery',NULL,NULL,'39,40,41,42',NULL,NULL,NULL,'2019-02-05 12:25:34','2019-06-10 14:10:19',1),
	(85,9,8,1,7,'immagine',NULL,NULL,NULL,NULL,NULL,NULL,'2019-06-12 11:10:18','2019-06-12 11:10:18',1),
	(86,9,9,1,8,'immagini_gallery',NULL,NULL,NULL,NULL,NULL,NULL,'2019-06-12 11:10:18','2019-06-12 11:10:18',1),
	(87,9,3,1,1,'meta_title',NULL,NULL,'test',NULL,NULL,NULL,'2019-06-12 11:10:18','2019-06-12 11:10:18',1),
	(88,9,3,1,3,'og_title',NULL,NULL,NULL,NULL,NULL,NULL,'2019-06-12 11:10:18','2019-06-12 11:10:18',1),
	(89,9,3,1,2,'meta_description',NULL,NULL,'test',NULL,NULL,NULL,'2019-06-12 11:10:18','2019-06-12 11:10:18',1),
	(90,9,3,1,9,'video_url',NULL,NULL,NULL,NULL,NULL,NULL,'2019-06-12 11:10:18','2019-06-12 11:10:18',1),
	(91,9,4,1,4,'og_description',NULL,NULL,NULL,NULL,NULL,NULL,'2019-06-12 11:10:18','2019-06-12 11:10:18',1),
	(92,9,8,1,5,'og_image',NULL,NULL,NULL,NULL,NULL,NULL,'2019-06-12 11:10:18','2019-06-12 11:10:18',1),
	(93,9,6,1,6,'robots',NULL,NULL,'index/follow',NULL,NULL,NULL,'2019-06-12 11:10:18','2019-06-12 11:10:18',1);

/*!40000 ALTER TABLE `posts_meta` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table posts_tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `posts_tags`;

CREATE TABLE `posts_tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_post` int(11) unsigned NOT NULL,
  `id_tag` int(11) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_post` (`id_post`),
  KEY `id_tag` (`id_tag`),
  CONSTRAINT `posts_tags_ibfk_2` FOREIGN KEY (`id_tag`) REFERENCES `tags` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `posts_tags_ibfk_3` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table posts_users_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `posts_users_groups`;

CREATE TABLE `posts_users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_post` int(11) unsigned NOT NULL,
  `id_user_group` mediumint(4) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_post` (`id_post`),
  KEY `id_user_group` (`id_user_group`),
  KEY `attivo` (`attivo`),
  CONSTRAINT `posts_users_groups_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_users_groups_ibfk_2` FOREIGN KEY (`id_user_group`) REFERENCES `users_groups` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table ruoli
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ruoli`;

CREATE TABLE `ruoli` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `ordine` tinyint(2) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `ruoli` WRITE;
/*!40000 ALTER TABLE `ruoli` DISABLE KEYS */;

INSERT INTO `ruoli` (`id`, `descrizione`, `ordine`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,'Amministratore',1,'2015-10-02 13:11:48','2019-06-10 14:22:25',1),
	(2,'Editore',1,'2015-10-02 13:11:48','2015-11-17 16:24:49',1);

/*!40000 ALTER TABLE `ruoli` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ruoli_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ruoli_menu`;

CREATE TABLE `ruoli_menu` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_ruolo` tinyint(3) unsigned NOT NULL,
  `livello` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `risorsa` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `azione` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `descrizione` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `class` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `header` tinyint(1) unsigned NOT NULL,
  `visible` tinyint(1) unsigned NOT NULL,
  `id_padre` tinyint(4) unsigned NOT NULL,
  `ordine` tinyint(2) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_ruoli_menu_ruoli` (`id_ruolo`),
  KEY `attivo` (`attivo`),
  KEY `id_padre` (`id_padre`),
  KEY `risorsa` (`risorsa`),
  KEY `azione` (`azione`),
  KEY `livello` (`livello`),
  CONSTRAINT `fk_ruoli_menu_ruoli` FOREIGN KEY (`id_ruolo`) REFERENCES `ruoli` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `ruoli_menu` WRITE;
/*!40000 ALTER TABLE `ruoli_menu` DISABLE KEYS */;

INSERT INTO `ruoli_menu` (`id`, `id_ruolo`, `livello`, `risorsa`, `azione`, `descrizione`, `class`, `header`, `visible`, `id_padre`, `ordine`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,1,0,'index','index','Dashboard','fa-send',0,1,0,1,'2015-10-02 14:09:20','2017-01-09 18:29:59',1),
	(2,1,0,'','','Utenti','fa-user',0,1,0,8,'2015-10-02 14:09:20','2017-03-30 17:23:35',1),
	(3,1,1,'utenti','index','Cerca utente','fa-search',0,1,106,1,'2015-10-02 14:09:20','2019-01-30 10:26:49',1),
	(4,1,1,'utenti','new','Nuovo utente','fa-plus',0,1,106,2,'2015-10-02 14:09:20','2019-01-30 10:26:26',1),
	(24,1,0,'','','Contenuti','fa-clipboard',0,1,0,3,'2015-10-02 15:00:00','2017-03-16 14:25:44',1),
	(25,1,0,'posts','index','Cerca contenuto','fa-search',0,1,24,1,'2015-10-02 15:01:00','2017-03-16 14:25:50',1),
	(27,1,0,'posts','new','Nuovo contenuto','fa-plus',0,1,24,2,'2015-10-02 15:01:00','2017-12-28 10:58:33',1),
	(29,1,0,'','','Applicazioni','fa-mobile',0,1,0,2,'2017-02-01 12:00:00','2017-01-09 18:29:54',1),
	(33,1,0,'applicazioni','index','Cerca App','fa-search',0,1,29,1,'2017-02-01 13:00:00','2017-01-03 17:03:57',1),
	(34,1,0,'applicazioni','new','Nuova App','fa-plus',0,1,29,2,'2017-02-01 13:00:00','2017-01-03 17:03:01',1),
	(35,1,0,'','','Utenti Applicazioni','fa-user',0,1,29,3,'2017-02-01 12:00:00','2017-01-03 17:06:08',1),
	(36,1,0,'applicazioni_utenti','index','Cerca Utente','fa-search',0,1,35,1,'2017-02-01 13:00:00','2017-01-05 17:10:59',1),
	(37,1,0,'applicazioni_utenti','new','Nuovo Utente App','fa-plus',0,1,35,2,'2017-02-01 13:00:00','2017-01-09 12:26:54',1),
	(38,1,0,'','','Meta Attributi','fa-info',0,1,51,3,'2017-01-20 00:00:00','2017-06-27 19:36:08',1),
	(39,1,0,'meta','index','Gestisci Campi','fa-search',0,1,124,3,'2017-01-20 00:00:00','2019-03-11 09:44:24',1),
	(40,1,0,'meta_group','index','Gestisci Gruppi','fa-search',0,1,123,1,'2017-02-01 00:00:00','2019-03-11 09:43:08',1),
	(41,1,1,'meta','new','Nuovo Campo','fa fa-plus',0,1,124,4,'2017-02-01 00:00:00','2019-03-11 09:46:56',1),
	(42,1,0,'meta_group','new','Nuovo Gruppo','fa-plus',0,1,123,2,'2017-02-01 00:00:00','2019-03-11 09:44:39',1),
	(43,1,0,'','','Files','fa-file-o',0,1,0,5,'2017-01-20 00:00:00','2017-03-07 19:05:27',1),
	(44,1,0,'files','index','Gestisci File','fa-search',0,1,43,1,'2017-01-20 00:00:00','2017-02-24 19:48:16',1),
	(45,1,0,'files','new','Carica File','fa-plus',0,1,43,2,'2017-01-20 00:00:00','2017-02-24 19:48:20',1),
	(46,1,0,'','','Filtri','fa-filter',0,1,51,2,'2017-01-20 00:00:00','2017-06-27 19:36:25',1),
	(47,1,0,'filtri','index','Gestisci FIltri','fa-search',0,1,121,3,'2017-01-20 00:00:00','2019-03-11 09:17:00',1),
	(48,1,0,'filtri','new','Nuovo Filtro','fa-plus',0,1,121,4,'2017-02-01 00:00:00','2019-03-11 09:16:55',1),
	(49,1,0,'filtri_group','index','Gestisci Gruppi','fa-search',0,1,120,1,'2017-02-01 00:00:00','2019-03-11 09:38:29',1),
	(50,1,0,'filtri_group','new','Nuovo Gruppo','fa-plus',0,1,120,2,'2017-02-01 00:00:00','2019-03-11 09:38:23',1),
	(51,1,0,'','','Tassonomie','fa-tree',0,1,0,4,'2017-01-20 00:00:00','2017-03-07 19:05:24',1),
	(52,1,0,'filtri_valori','index','Gestisci Valori','fa-sliders',0,1,122,5,'2017-02-01 00:00:00','2019-03-11 09:38:15',1),
	(53,1,0,'filtri_valori','new','Nuovo Valore','fa-plus',0,1,122,6,'2017-02-01 00:00:00','2019-03-11 09:16:33',1),
	(54,1,0,'','','Tipologie Post','fa-cubes',0,1,51,1,'2017-03-07 15:20:59','2017-06-27 19:36:09',1),
	(55,1,0,'tipologie_post','index','Gestisci Post Types','fa-cog',0,1,54,1,'2017-02-01 00:00:00','2017-03-07 17:25:17',1),
	(56,1,0,'tipologie_post','new','Nuovo Post Type','fa-plus',0,1,54,2,'2017-02-01 00:00:00','2017-03-07 17:25:18',1),
	(57,1,0,'','','Tags','fa-tags',0,1,51,4,'2017-03-07 15:20:59','2017-06-27 19:36:33',1),
	(58,1,0,'tags','index','Gestisci Tags','fa-plus',0,1,57,1,'2017-03-24 14:13:02','2017-03-24 16:13:33',1),
	(59,1,0,'tags','new','Nuovo Tag','fa-plus',0,1,57,2,'2017-03-24 14:13:02','2017-03-24 16:13:02',1),
	(60,1,0,'','','Blocchi','fa-th-large',0,1,0,6,'2017-03-07 15:20:59','2017-03-07 17:24:37',1),
	(61,1,0,'blocks','index','Gestisci Blocchi','fa-cog',0,1,60,1,'2017-03-07 15:20:59','2017-03-30 14:38:39',1),
	(62,1,0,'blocks','new','Crea Blocco','fa-plus',0,1,60,2,'2017-03-07 15:20:59','2017-03-30 14:38:39',1),
	(63,1,0,'','','Opzioni Custom','fa-cogs',0,1,0,6,'2017-03-07 15:20:59','2017-04-12 18:31:12',1),
	(64,1,0,'options','index','Gestisci Options','fa-cog',0,1,63,1,'2017-03-07 15:20:59','2017-04-12 13:28:29',1),
	(65,1,0,'options','new','Nuova Option','fa-plus',0,1,63,2,'2017-03-07 15:20:59','2017-04-12 13:28:33',1),
	(66,1,0,'','','Amministrazione','fa-university',0,1,0,8,'2017-03-07 15:20:59','2017-03-07 17:24:37',1),
	(67,1,0,'ruoli_menu','new','Nuovo Admin Menu','fa-plus',0,1,69,2,'2017-03-07 15:20:59','2017-04-12 13:31:11',1),
	(68,1,0,'ruoli_menu','index','Gestisci Admin Menu','fa-cog',0,1,69,1,'2017-03-07 15:20:59','2017-04-12 13:31:09',1),
	(69,1,0,'','','Menu Admin','fa-bars',0,1,66,1,'2017-03-07 15:20:59','2017-03-07 17:24:37',1),
	(70,1,0,'','','Permessi Admin','fa-user-secret',0,1,66,2,'2017-03-07 15:20:59','2017-03-07 17:24:37',1),
	(71,1,0,'ruoli_permessi','index','Gestisci Permessi','fa-cog',0,1,70,1,'2017-03-07 15:20:59','2017-04-12 17:46:59',1),
	(72,1,0,'ruoli_permessi','new','Nuovo Permesso Admin','fa-plus',0,1,70,2,'2017-03-07 15:20:59','2017-04-12 17:45:27',1),
	(75,1,0,'','','Impostazioni Media','fa fa-file-image-o',0,1,66,3,'2017-04-12 16:29:17','2017-04-12 18:30:38',1),
	(76,1,0,'files_sizes','index','Gestisci Files Size','fa fa-cog',0,1,75,1,'2017-04-12 16:32:00','2017-04-12 18:32:00',1),
	(77,1,0,'files_sizes','new','Nuova Files Size','fa fa-plus',0,1,75,1,'2017-04-12 16:32:51','2017-04-12 18:32:51',1),
	(78,1,0,'index','indexStatus','Gestisci Indici','fa-indent',0,1,66,4,'2017-04-19 15:55:21','2017-04-19 17:58:51',1),
	(79,1,0,'','','Forms','fa-comments-o',0,1,0,9,'2017-04-21 12:19:07','2017-04-21 14:19:07',1),
	(80,1,0,'forms','index','Gestisci Forms','fa-cog',0,1,79,1,'2017-04-21 12:20:11','2017-04-21 14:20:12',1),
	(81,1,0,'forms','new','Nuovo Form','fa-plus',0,1,79,2,'2017-04-21 12:20:44','2017-04-21 14:45:50',1),
	(82,1,0,'form_requests','index','Richieste Ricevute','fa-commenting-o',0,1,79,3,'2017-04-21 12:22:11','2017-04-21 14:22:11',1),
	(84,2,0,'index','index','Dashboard','fa-send',0,1,0,1,'2015-10-02 14:09:20','2017-01-09 18:29:59',1),
	(85,2,0,'','','Files','fa-file-o',0,1,0,5,'2017-01-20 00:00:00','2017-03-07 19:05:27',1),
	(86,2,0,'files','index','Gestisci File','fa-search',0,1,85,1,'2017-01-20 00:00:00','2017-04-28 19:21:07',1),
	(87,2,0,'files','new','Carica File','fa-plus',0,1,85,2,'2017-01-20 00:00:00','2017-04-28 19:21:09',1),
	(88,2,0,'','','Filtri','fa-filter',0,1,51,4,'2017-01-20 00:00:00','2017-03-02 17:19:48',1),
	(89,2,0,'filtri_valori','index','Gestisci Valori','fa-sliders',0,1,88,5,'2017-02-01 00:00:00','2019-03-11 09:38:35',1),
	(90,2,0,'filtri_valori','new','Nuovo Valore','fa-plus',0,1,88,6,'2017-02-01 00:00:00','2017-04-28 19:21:17',1),
	(91,2,0,'','','Tags','fa-tags',0,1,0,5,'2017-03-07 15:20:59','2017-04-28 19:27:57',1),
	(92,2,0,'tags','index','Gestisci Tags','fa-plus',0,1,91,1,'2017-03-24 14:13:02','2017-04-28 19:21:23',1),
	(93,2,0,'tags','new','Nuovo Tag','fa-plus',0,1,91,2,'2017-03-24 14:13:02','2017-04-28 19:21:26',1),
	(94,2,0,'','','Form','fa-comments-o',0,1,0,7,'2017-03-24 14:13:02','2019-03-11 16:45:50',1),
	(97,2,0,'form_requests','index','Richieste Ricevute','fa-commenting-o',0,1,94,3,'2017-04-21 12:22:11','2017-04-28 19:21:35',1),
	(98,2,1,'','','Contenuti','fa-clipboard',0,1,0,12,'2015-10-02 15:00:00','2017-12-22 17:16:07',1),
	(99,2,0,'posts','index','Cerca contenuto','fa-search',0,1,98,1,'2015-10-02 15:01:00','2017-04-28 19:23:35',1),
	(100,2,0,'posts','new/1','Nuovo contenuto','fa-plus',0,1,98,2,'2015-10-02 15:01:00','2017-04-28 19:23:37',1),
	(101,1,0,'files','regenerateAllThumbs','Rigenera Miniature','fa fa-file-image-o',0,1,75,5,'2017-05-18 11:41:39','2017-05-18 13:51:40',1),
	(102,1,1,'','','Traduzioni Stringhe','fa-globe',0,1,0,13,'2017-12-22 16:16:40','2017-12-22 17:17:09',1),
	(103,1,1,'flat_translations','index','Gestisci Traduzoioni','fa-cog',0,1,102,1,'2017-12-22 16:17:47','2017-12-22 17:22:21',1),
	(104,1,1,'flat_translations','new','Nuova Traduzione','fa-plus',0,1,102,2,'2017-12-22 16:18:59','2017-12-22 17:19:56',1),
	(105,1,1,'blocks','templates','Gestisci Templates','fa fa-magic',0,1,60,3,'2018-01-09 17:33:06','2019-01-28 11:35:53',1),
	(106,1,0,'','','Utenti Admin','fa fa-user-circle',0,1,2,1,'2019-01-30 10:18:41','2019-01-30 10:18:41',1),
	(107,1,1,'','','Utenti Sito','fa fa-users',0,1,2,2,'2019-01-30 12:10:58','2019-01-30 12:10:59',1),
	(108,1,1,'users','index','Gestisci Utenti','fa fa-cog',0,1,107,1,'2019-01-30 12:12:44','2019-01-30 12:12:44',1),
	(109,1,1,'users','new','Nuovo Utente','fa fa-plus',0,1,107,2,'2019-01-30 12:13:29','2019-01-30 12:13:29',1),
	(117,1,1,'','','Organizza Blocchi','fa fa-tag',0,1,60,3,'2019-03-04 15:23:16','2019-03-04 15:23:32',1),
	(118,1,1,'blocks_tags','index','Gestisci Tag Blocchi','fa fa-cog',0,1,117,1,'2019-03-04 15:24:18','2019-03-04 15:24:18',1),
	(119,1,1,'blocks_tags','new','Nuovo Tag blocchi','fa fa-plus',0,1,117,2,'2019-03-04 15:24:48','2019-03-04 15:24:48',1),
	(120,1,1,'','','Gruppi','fa fa-circle',0,1,46,1,'2019-03-11 09:11:18','2019-03-11 09:37:40',1),
	(121,1,1,'','','Filtri','fa fa-filter',0,1,46,2,'2019-03-11 09:11:52','2019-03-11 09:11:52',1),
	(122,1,1,'','','Valori','fa fa-align-center',0,1,46,3,'2019-03-11 09:13:13','2019-03-11 09:37:30',1),
	(123,1,1,'','','Gruppi','fa fa-circle',0,1,38,1,'2019-03-11 09:41:01','2019-03-11 09:41:01',1),
	(124,1,1,'','','Campi','fa-align-center',0,1,38,2,'2019-03-11 09:41:57','2019-03-11 09:47:20',1),
	(132,2,0,'','','Blocchi','fa-th-large',0,1,0,6,'2017-03-07 15:20:59','2017-03-07 17:24:37',1),
	(133,2,0,'blocks','index','Gestisci Blocchi','fa-cog',0,1,132,1,'2017-03-07 15:20:59','2017-03-30 14:38:39',1),
	(134,2,0,'blocks','new','Crea Blocco','fa-plus',0,1,132,2,'2017-03-07 15:20:59','2017-03-30 14:38:39',1),
	(135,2,1,'','','Organizza Blocchi','fa fa-tag',0,1,132,3,'2019-03-04 15:23:16','2019-03-04 15:23:32',1),
	(136,2,1,'blocks_tags','index','Gestisci Tag Blocchi','fa fa-cog',0,1,135,1,'2019-03-04 15:24:18','2019-03-04 15:24:18',1),
	(137,2,1,'blocks_tags','new','Nuovo Tag blocchi','fa fa-plus',0,1,135,2,'2019-03-04 15:24:48','2019-03-04 15:24:48',1),
	(138,1,1,'import_export','index','Import / Export','fa fa-download',0,1,66,4,'2019-03-20 13:08:32','2019-03-20 13:08:45',1),
	(139,2,1,'import_export','index','Import / Export','fa fa-download',0,1,0,8,'2019-03-26 12:41:44','2019-03-26 12:41:44',1),
	(140,1,1,'files','cleanFiles','Pulizia Files','fa fa-trash',0,1,75,3,'2019-03-27 10:10:27','2019-03-27 10:10:53',1);

/*!40000 ALTER TABLE `ruoli_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ruoli_permessi
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ruoli_permessi`;

CREATE TABLE `ruoli_permessi` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_ruolo` tinyint(3) unsigned NOT NULL,
  `livello` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `risorsa` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `azione` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `id_ruolo` (`id_ruolo`),
  KEY `attivo` (`attivo`),
  CONSTRAINT `fk_ruoli_permessi_ruoli` FOREIGN KEY (`id_ruolo`) REFERENCES `ruoli` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `ruoli_permessi` WRITE;
/*!40000 ALTER TABLE `ruoli_permessi` DISABLE KEYS */;

INSERT INTO `ruoli_permessi` (`id`, `id_ruolo`, `livello`, `risorsa`, `azione`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,1,0,'index','[\"index\",\"indexStatus\",\"rebuildIndex\"]','2015-10-02 14:09:41','2017-04-20 16:31:23',1),
	(2,1,0,'utenti','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"getUtentiFromGruppiUtenti\"]','2015-10-02 15:31:09','2015-11-13 14:08:00',1),
	(19,1,0,'posts','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"clone\"]','2017-01-02 15:27:21','2017-12-29 16:17:19',1),
	(20,1,0,'applicazioni','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-01-03 15:05:02','2017-01-03 17:05:02',1),
	(21,1,0,'applicazioni_domini','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-01-04 14:25:48','2017-01-04 16:25:48',1),
	(22,1,0,'applicazioni_utenti','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-01-05 15:11:11','2017-01-05 17:11:11',1),
	(23,1,0,'meta','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2017-01-09 16:35:31','2017-01-09 19:21:55',1),
	(24,1,0,'meta_group','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2017-01-09 16:36:02','2017-01-16 16:59:42',1),
	(25,1,0,'files','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"getLastFile\",\"getFileList\",\"updateFileInfo\",\"regenerateSingleFile\",\"regenerateAllThumbs\",\"getFile\",\"iframeList\",\"iframeUpload\",\"cleanFiles\"]','2017-01-20 12:19:41','2019-04-02 12:47:47',1),
	(26,1,0,'filtri','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2017-01-09 16:35:31','2017-01-09 19:21:55',1),
	(27,1,0,'filtri_group','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2017-01-09 16:36:02','2017-01-16 16:59:42',1),
	(28,1,0,'filtri_valori','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"checkFiltro\",\"getChildrenFilterValues\"]','2017-03-02 16:10:55','2017-03-20 13:18:43',1),
	(29,1,0,'filtri_group_post_type','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-03-02 16:10:55','2017-03-02 18:10:55',1),
	(30,1,0,'meta_group_post_type','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-03-02 16:10:55','2017-03-02 18:10:55',1),
	(31,1,0,'tipologie_post','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-03-07 15:19:24','2017-03-07 17:19:24',1),
	(32,1,0,'tags','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-03-07 15:19:24','2017-03-07 17:19:24',1),
	(33,1,0,'blocks','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"clone\",\"templates\",\"edit_inline\"]','2017-03-07 15:19:24','2019-03-04 15:32:53',1),
	(34,1,0,'options','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2017-03-07 15:19:24','2017-04-12 18:13:23',1),
	(35,1,0,'ruoli_menu','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2017-03-07 15:19:24','2017-03-30 17:43:29',1),
	(36,1,0,'ruoli_permessi','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2017-03-07 15:19:24','2017-03-30 17:43:29',1),
	(38,1,0,'files_sizes','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-04-12 16:34:07','2017-04-12 18:34:07',1),
	(39,1,0,'forms','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-04-21 12:23:25','2017-04-21 14:23:25',1),
	(42,1,0,'form_requests','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-04-21 12:38:11','2017-04-21 14:38:11',1),
	(43,1,0,'form_fields','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-04-21 15:48:44','2017-04-21 17:48:44',1),
	(44,1,0,'flat_translations','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-12-22 16:02:37','2017-12-22 17:21:33',1),
	(45,1,0,'users_groups','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2019-01-28 17:48:23','2019-01-28 17:48:23',1),
	(46,1,1,'users','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2019-01-30 12:21:39','2019-01-30 12:21:39',1),
	(47,1,0,'volantini','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"save\",\"edit_inline\",\"fileupload\",\"trashImages\",\"clone\"]','2019-02-19 14:56:10','2019-02-21 14:33:53',1),
	(48,1,0,'punti_vendita','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2019-02-19 14:56:54','2019-02-19 14:56:54',1),
	(49,1,0,'blocks_tags','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2019-03-04 15:20:12','2019-03-04 15:21:44',1),
	(50,1,1,'blocks_history','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2019-03-04 17:58:22','2019-03-04 17:58:22',1),
	(73,2,0,'index','[\"index\",\"indexStatus\",\"rebuildIndex\"]','2015-10-02 14:09:41','2017-04-20 16:31:23',1),
	(74,2,0,'utenti','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"getUtentiFromGruppiUtenti\"]','2015-10-02 15:31:09','2015-11-13 14:08:00',1),
	(75,2,0,'posts','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"clone\"]','2017-01-02 15:27:21','2017-12-29 16:17:19',1),
	(76,2,0,'applicazioni','[\"index\",\"search\"]','2017-01-03 15:05:02','2017-01-03 17:05:02',1),
	(79,2,0,'files','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"getLastFile\",\"getFileList\",\"updateFileInfo\",\"regenerateSingleFile\",\"regenerateAllThumbs\",\"getFile\",\"iframeList\",\"iframeUpload\",\"cleanFiles\"]','2017-01-20 12:19:41','2019-04-02 12:47:48',1),
	(86,2,0,'tags','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-03-07 15:19:24','2017-03-07 17:19:24',1),
	(87,2,0,'blocks','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"clone\",\"templates\",\"edit_inline\"]','2017-03-07 15:19:24','2019-03-04 15:32:53',1),
	(89,2,0,'form_requests','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2017-04-21 12:38:11','2017-04-21 14:38:11',1),
	(91,2,0,'volantini','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"save\",\"edit_inline\",\"fileupload\",\"trashImages\",\"clone\"]','2019-02-19 14:56:10','2019-02-21 14:33:53',1),
	(92,2,0,'punti_vendita','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2019-02-19 14:56:54','2019-02-19 14:56:54',1),
	(93,2,0,'blocks_tags','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2019-03-04 15:20:12','2019-03-04 15:21:44',1),
	(94,2,1,'blocks_history','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\",\"edit_inline\"]','2019-03-04 17:58:22','2019-03-04 17:58:22',1),
	(95,1,1,'import_export','[\"index\",\"import\",\"export\",\"model\"]','2019-03-20 13:07:21','2019-03-20 13:07:21',1),
	(96,2,1,'import_export','[\"index\",\"import\",\"export\",\"model\"]','2019-03-26 12:40:41','2019-03-26 12:40:41',1),
	(97,2,1,'files_sizes','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2019-04-02 12:48:13','2019-04-02 12:48:13',1),
	(98,1,0,'applicazioni_routes','[\"index\",\"search\",\"edit\",\"save\",\"delete\",\"new\",\"create\"]','2019-06-06 09:51:46','2019-06-06 09:51:46',1);

/*!40000 ALTER TABLE `ruoli_permessi` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tags`;

CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_applicazione` tinyint(2) unsigned NOT NULL,
  `tag` varchar(50) NOT NULL DEFAULT '',
  `titolo` varchar(125) NOT NULL,
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `tag_2` (`tag`),
  KEY `id_applicazione` (`id_applicazione`),
  CONSTRAINT `tags_ibfk_2` FOREIGN KEY (`id_applicazione`) REFERENCES `applicazioni` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table tipologie_applicazione
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_applicazione`;

CREATE TABLE `tipologie_applicazione` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tipologie_applicazione` WRITE;
/*!40000 ALTER TABLE `tipologie_applicazione` DISABLE KEYS */;

INSERT INTO `tipologie_applicazione` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Sito Web',1,1),
	(2,'Applicazione',2,1),
	(3,'Web App',3,1),
	(4,'Api',4,1);

/*!40000 ALTER TABLE `tipologie_applicazione` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_block
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_block`;

CREATE TABLE `tipologie_block` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_block` WRITE;
/*!40000 ALTER TABLE `tipologie_block` DISABLE KEYS */;

INSERT INTO `tipologie_block` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Html',1,1),
	(2,'Css',2,1),
	(3,'Javascript',3,1);

/*!40000 ALTER TABLE `tipologie_block` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_categoria
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_categoria`;

CREATE TABLE `tipologie_categoria` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table tipologie_filtro
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_filtro`;

CREATE TABLE `tipologie_filtro` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `tipologie_filtro` WRITE;
/*!40000 ALTER TABLE `tipologie_filtro` DISABLE KEYS */;

INSERT INTO `tipologie_filtro` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Select',1,1),
	(2,'Multiselect',2,1);

/*!40000 ALTER TABLE `tipologie_filtro` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_form
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_form`;

CREATE TABLE `tipologie_form` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) DEFAULT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_form` WRITE;
/*!40000 ALTER TABLE `tipologie_form` DISABLE KEYS */;

INSERT INTO `tipologie_form` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Contatto',1,1),
	(2,'Richiesta Informazioni',2,1),
	(3,'Prenotazione Evento',2,1),
	(4,'Car Advisor',2,1);

/*!40000 ALTER TABLE `tipologie_form` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_form_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_form_fields`;

CREATE TABLE `tipologie_form_fields` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_form_fields` WRITE;
/*!40000 ALTER TABLE `tipologie_form_fields` DISABLE KEYS */;

INSERT INTO `tipologie_form_fields` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Text',1,1),
	(2,'Email',2,1),
	(3,'Telefono',3,1),
	(4,'Numero',4,1),
	(5,'Textarea',5,1),
	(6,'Select',6,1),
	(7,'Checkbox',7,1),
	(8,'Multi Select',8,1);

/*!40000 ALTER TABLE `tipologie_form_fields` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_gruppo
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_gruppo`;

CREATE TABLE `tipologie_gruppo` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `vincoli_controller` text COLLATE utf8_swedish_ci NOT NULL,
  `ordine` tinyint(2) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_gruppo` WRITE;
/*!40000 ALTER TABLE `tipologie_gruppo` DISABLE KEYS */;

INSERT INTO `tipologie_gruppo` (`id`, `descrizione`, `vincoli_controller`, `ordine`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,'Amministratore','',1,'2015-11-17 14:44:55','2015-11-17 16:44:56',1),
	(2,'Editore','',2,'2015-11-17 14:45:12','2016-01-20 19:52:22',1),
	(3,'Data Entry','',4,'2015-11-17 14:45:13','2016-01-20 19:52:36',1);

/*!40000 ALTER TABLE `tipologie_gruppo` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_meta`;

CREATE TABLE `tipologie_meta` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tipologie_meta` WRITE;
/*!40000 ALTER TABLE `tipologie_meta` DISABLE KEYS */;

INSERT INTO `tipologie_meta` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Intero',1,1),
	(2,'Decimale',2,1),
	(3,'Stringa',3,1),
	(4,'Testo',4,1),
	(5,'Date/Time',5,1),
	(6,'Select',6,1),
	(7,'Checkbox',7,1),
	(8,'File',8,1),
	(9,'File Collection',9,1),
	(10,'Html',10,1);

/*!40000 ALTER TABLE `tipologie_meta` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_post
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_post`;

CREATE TABLE `tipologie_post` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `slug` char(75) NOT NULL DEFAULT '',
  `admin_menu` tinyint(1) NOT NULL DEFAULT 0,
  `admin_icon` char(50) NOT NULL,
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug_tp` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tipologie_post` WRITE;
/*!40000 ALTER TABLE `tipologie_post` DISABLE KEYS */;

INSERT INTO `tipologie_post` (`id`, `descrizione`, `slug`, `admin_menu`, `admin_icon`, `ordine`, `attivo`)
VALUES
	(1,'Notizie','news',1,'fa fa-file-text-o',3,1),
	(2,'Pagine','pagina',1,'fa fa-file-o',2,1);

/*!40000 ALTER TABLE `tipologie_post` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_routes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_routes`;

CREATE TABLE `tipologie_routes` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `metodo` varchar(25) COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `ordine` tinyint(2) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_routes` WRITE;
/*!40000 ALTER TABLE `tipologie_routes` DISABLE KEYS */;

INSERT INTO `tipologie_routes` (`id`, `descrizione`, `metodo`, `ordine`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,'Add','add',1,'2019-06-06 09:00:00','2019-06-06 09:00:00',1),
	(2,'Add Get','addGet',2,'2019-06-06 09:00:00','2019-06-06 09:11:55',1),
	(3,'Add Post','addPost',3,'2019-06-06 09:00:00','2019-06-06 09:11:55',1),
	(4,'Add Put','addPut',4,'2019-06-06 09:00:00','2019-06-06 09:11:55',1),
	(5,'Add Patch','addPatch',5,'2019-06-06 09:00:00','2019-06-06 09:11:55',1),
	(6,'Add Delete','addDelete',6,'2019-06-06 09:00:00','2019-06-06 09:11:55',1),
	(7,'Add Options','addOptions',7,'2019-06-06 09:00:00','2019-06-06 09:11:55',1),
	(8,'Add Head','addHead',8,'2019-06-06 09:00:00','2019-06-06 09:11:55',1),
	(9,'Add Purge','addPurge',9,'2019-06-06 09:00:00','2019-06-06 09:13:43',1),
	(10,'Add Trace','addTrace',10,'2019-06-06 09:00:00','2019-06-06 09:13:43',1),
	(11,'Add Connect','addConnect',11,'2019-06-06 09:00:00','2019-06-06 09:13:43',1);

/*!40000 ALTER TABLE `tipologie_routes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_applicazione
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_applicazione`;

CREATE TABLE `tipologie_stato_applicazione` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tipologie_stato_applicazione` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_applicazione` DISABLE KEYS */;

INSERT INTO `tipologie_stato_applicazione` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Attiva',1,1),
	(2,'Non Attiva',2,1);

/*!40000 ALTER TABLE `tipologie_stato_applicazione` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_applicazione_route
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_applicazione_route`;

CREATE TABLE `tipologie_stato_applicazione_route` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tipologie_stato_applicazione_route` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_applicazione_route` DISABLE KEYS */;

INSERT INTO `tipologie_stato_applicazione_route` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Attiva',1,1),
	(2,'Non Attiva',2,1);

/*!40000 ALTER TABLE `tipologie_stato_applicazione_route` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_block
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_block`;

CREATE TABLE `tipologie_stato_block` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_stato_block` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_block` DISABLE KEYS */;

INSERT INTO `tipologie_stato_block` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Attivo',1,1),
	(2,'Non Attivo',2,1);

/*!40000 ALTER TABLE `tipologie_stato_block` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_file
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_file`;

CREATE TABLE `tipologie_stato_file` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tipologie_stato_file` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_file` DISABLE KEYS */;

INSERT INTO `tipologie_stato_file` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Pubblico',1,1),
	(2,'Privato',2,1),
	(3,'Eliminato',3,1);

/*!40000 ALTER TABLE `tipologie_stato_file` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_filtro
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_filtro`;

CREATE TABLE `tipologie_stato_filtro` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `tipologie_stato_filtro` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_filtro` DISABLE KEYS */;

INSERT INTO `tipologie_stato_filtro` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Attivo',1,1),
	(2,'Non Attivo',2,1);

/*!40000 ALTER TABLE `tipologie_stato_filtro` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_form
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_form`;

CREATE TABLE `tipologie_stato_form` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_stato_form` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_form` DISABLE KEYS */;

INSERT INTO `tipologie_stato_form` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Pubblico',1,1),
	(2,'Privato',2,1),
	(3,'Disabilitato',4,1);

/*!40000 ALTER TABLE `tipologie_stato_form` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_form_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_form_fields`;

CREATE TABLE `tipologie_stato_form_fields` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_stato_form_fields` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_form_fields` DISABLE KEYS */;

INSERT INTO `tipologie_stato_form_fields` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Attivo',1,1),
	(2,'Non Attivo',2,1);

/*!40000 ALTER TABLE `tipologie_stato_form_fields` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_post
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_post`;

CREATE TABLE `tipologie_stato_post` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tipologie_stato_post` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_post` DISABLE KEYS */;

INSERT INTO `tipologie_stato_post` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Pubblico',1,1),
	(2,'Bozza',2,1),
	(3,'Privato',3,1),
	(4,'Cancellato',4,1);

/*!40000 ALTER TABLE `tipologie_stato_post` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_post_file
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_post_file`;

CREATE TABLE `tipologie_stato_post_file` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

LOCK TABLES `tipologie_stato_post_file` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_post_file` DISABLE KEYS */;

INSERT INTO `tipologie_stato_post_file` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Pubblico',1,1),
	(2,'Bozza',2,1),
	(3,'Privato',3,1),
	(4,'Cancellato',4,1);

/*!40000 ALTER TABLE `tipologie_stato_post_file` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_post_meta
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_post_meta`;

CREATE TABLE `tipologie_stato_post_meta` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(175) NOT NULL DEFAULT '',
  `ordine` tinyint(2) NOT NULL,
  `attivo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tipologie_stato_post_meta` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_post_meta` DISABLE KEYS */;

INSERT INTO `tipologie_stato_post_meta` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Attivo',1,1),
	(2,'Non Attivo',2,1);

/*!40000 ALTER TABLE `tipologie_stato_post_meta` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_user`;

CREATE TABLE `tipologie_stato_user` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `avanzamento` tinyint(3) unsigned NOT NULL,
  `soglia_giorni` tinyint(2) unsigned NOT NULL,
  `ordine` tinyint(1) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`),
  KEY `attivo_2` (`attivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_stato_user` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_user` DISABLE KEYS */;

INSERT INTO `tipologie_stato_user` (`id`, `descrizione`, `avanzamento`, `soglia_giorni`, `ordine`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,'Attivo',0,0,0,'2018-01-01 00:00:00','2019-01-28 16:37:52',1),
	(2,'Non Attivo - Attesa Attivazione',0,0,0,'2018-01-01 00:00:00','2019-01-28 16:38:24',1),
	(3,'Non Attivo - Reset Password',0,0,0,'2018-01-01 00:00:00','2019-01-28 16:38:09',1),
	(4,'Cancellato',0,0,0,'2018-01-01 00:00:00','2019-01-28 16:38:13',1),
	(5,'Non Attivo - Sospeso da Admin',0,0,0,'2018-01-01 00:00:00','2019-01-31 15:44:31',1);

/*!40000 ALTER TABLE `tipologie_stato_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_stato_utente
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_stato_utente`;

CREATE TABLE `tipologie_stato_utente` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `avanzamento` tinyint(3) unsigned NOT NULL,
  `soglia_giorni` tinyint(2) unsigned NOT NULL,
  `ordine` tinyint(1) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_stato_utente` WRITE;
/*!40000 ALTER TABLE `tipologie_stato_utente` DISABLE KEYS */;

INSERT INTO `tipologie_stato_utente` (`id`, `descrizione`, `avanzamento`, `soglia_giorni`, `ordine`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,'Attivo',0,0,1,'2015-03-13 10:49:55','2015-03-19 15:04:01',1),
	(2,'In attesa di attivazione',0,0,2,'2015-03-13 10:49:55','2015-03-31 19:22:05',1),
	(3,'Bannato',0,0,3,'2015-03-13 10:49:55','2015-03-31 19:21:51',1),
	(4,'Sospeso',0,0,4,'2015-03-13 10:49:55','2015-03-31 18:34:08',1);

/*!40000 ALTER TABLE `tipologie_stato_utente` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_user`;

CREATE TABLE `tipologie_user` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` varchar(75) NOT NULL DEFAULT '',
  `ordine` tinyint(1) unsigned NOT NULL,
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tipologie_user` WRITE;
/*!40000 ALTER TABLE `tipologie_user` DISABLE KEYS */;

INSERT INTO `tipologie_user` (`id`, `descrizione`, `ordine`, `attivo`)
VALUES
	(1,'Registrazione Sito',1,1),
	(2,'Registrazione Facebook',2,1),
	(3,'Registrazione Google',3,1),
	(4,'Registrazione Admin',4,1);

/*!40000 ALTER TABLE `tipologie_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tipologie_utente
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tipologie_utente`;

CREATE TABLE `tipologie_utente` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `descrizione` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `ordine` tinyint(2) unsigned NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `tipologie_utente` WRITE;
/*!40000 ALTER TABLE `tipologie_utente` DISABLE KEYS */;

INSERT INTO `tipologie_utente` (`id`, `descrizione`, `ordine`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,'Amminstratore',1,'2015-03-13 10:42:47','2016-01-20 18:46:28',1),
	(2,'Editore',1,'2015-03-13 10:42:47','2016-01-20 19:51:42',1),
	(3,'Data Entry',1,'2015-03-13 10:42:47','2016-01-20 19:51:47',1);

/*!40000 ALTER TABLE `tipologie_utente` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_users_groups` mediumint(4) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(2) unsigned NOT NULL,
  `id_tipologia_user` tinyint(2) unsigned NOT NULL,
  `username` varchar(125) NOT NULL DEFAULT '',
  `email` varchar(125) NOT NULL DEFAULT '',
  `nome` varchar(250) NOT NULL DEFAULT '',
  `cognome` varchar(250) NOT NULL DEFAULT '',
  `telefono` varchar(75) DEFAULT NULL,
  `indirizzo` varchar(250) DEFAULT NULL,
  `localita` varchar(125) DEFAULT NULL,
  `cap` char(5) DEFAULT NULL,
  `data_di_nascita` date DEFAULT NULL,
  `validation_token` text DEFAULT NULL,
  `token_validated` tinyint(1) NOT NULL DEFAULT 0,
  `password` binary(60) NOT NULL DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `password_reset_token` text DEFAULT NULL,
  `validation_expiration_date` datetime DEFAULT NULL,
  `reset_password_expiration_date` datetime DEFAULT NULL,
  `user_registration_date` datetime NOT NULL,
  `user_last_login` datetime DEFAULT NULL,
  `ip_address` varchar(25) DEFAULT '',
  `facebook_auth_id` varchar(250) DEFAULT NULL,
  `facebook_auth_token` text DEFAULT NULL,
  `google_auth_id` varchar(250) DEFAULT NULL,
  `google_auth_token` text DEFAULT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`,`attivo`),
  UNIQUE KEY `username` (`username`,`attivo`),
  KEY `id_users_groups` (`id_users_groups`),
  KEY `id_tipologia_stato` (`id_tipologia_stato`),
  KEY `attivo` (`attivo`),
  KEY `id_tipologia_user` (`id_tipologia_user`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_users_groups`) REFERENCES `users_groups` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_user` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`id_tipologia_user`) REFERENCES `tipologie_user` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_groups`;

CREATE TABLE `users_groups` (
  `id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `titolo` varchar(75) NOT NULL DEFAULT '',
  `visitors` tinyint(1) NOT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `attivo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `attivo` (`attivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `users_groups` WRITE;
/*!40000 ALTER TABLE `users_groups` DISABLE KEYS */;

INSERT INTO `users_groups` (`id`, `titolo`, `visitors`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1,'Utenti Loggati',0,'2018-01-28 00:00:01','2019-02-04 11:35:19',1);

/*!40000 ALTER TABLE `users_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table utenti
# ------------------------------------------------------------

DROP TABLE IF EXISTS `utenti`;

CREATE TABLE `utenti` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipologia_utente` tinyint(2) unsigned NOT NULL,
  `id_tipologia_stato` tinyint(1) unsigned NOT NULL,
  `id_ruolo` tinyint(3) unsigned NOT NULL,
  `livello` tinyint(2) unsigned NOT NULL DEFAULT 0,
  `nome_utente` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `password` binary(60) NOT NULL,
  `nome` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `cognome` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `email` char(100) COLLATE utf8_swedish_ci NOT NULL,
  `avatar` char(50) COLLATE utf8_swedish_ci NOT NULL,
  `token` char(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `api_level` tinyint(2) NOT NULL DEFAULT 0,
  `public_key` varchar(75) COLLATE utf8_swedish_ci DEFAULT NULL,
  `private_key` varchar(75) COLLATE utf8_swedish_ci DEFAULT NULL,
  `data_creazione_token` datetime DEFAULT NULL,
  `data_creazione` datetime NOT NULL,
  `data_aggiornamento` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attivo` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_utenti_tipologie_utente` (`id_tipologia_utente`),
  KEY `fk_utenti_tipologie_stato_utente` (`id_tipologia_stato`),
  KEY `fk_utenti_ruoli` (`id_ruolo`),
  KEY `attivo` (`attivo`),
  KEY `public_key` (`public_key`),
  CONSTRAINT `utenti_ibfk_1` FOREIGN KEY (`id_tipologia_utente`) REFERENCES `tipologie_utente` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `utenti_ibfk_2` FOREIGN KEY (`id_tipologia_stato`) REFERENCES `tipologie_stato_utente` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `utenti_ibfk_3` FOREIGN KEY (`id_ruolo`) REFERENCES `ruoli` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci ROW_FORMAT=COMPACT;

LOCK TABLES `utenti` WRITE;
/*!40000 ALTER TABLE `utenti` DISABLE KEYS */;

INSERT INTO `utenti` (`id`, `id_tipologia_utente`, `id_tipologia_stato`, `id_ruolo`, `livello`, `nome_utente`, `password`, `nome`, `cognome`, `email`, `avatar`, `token`, `api_level`, `public_key`, `private_key`, `data_creazione_token`, `data_creazione`, `data_aggiornamento`, `attivo`)
VALUES
	(1, 1, 1, 1, 99, 'admin', X'243279243132246432314F52575A336445524964556332646B7379514F61707272434B476752533739414B687A3272616F6538356B3141504E445536', 'Admin', 'Admin', 'admin@cms.io', 'avatar/avatar_work_01.png', 'b45f4da602d91f3e82c56d83bbe8eff9ac511aee', 99, 'admin', 'admin', '2019-06-10 14:22:25', '2015-10-02 00:00:00', '2019-06-12 17:00:44', 1);


/*!40000 ALTER TABLE `utenti` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
