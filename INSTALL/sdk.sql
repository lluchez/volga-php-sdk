-- phpMyAdmin SQL Dump
-- version 2.9.0.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 07, 2010 at 07:38 PM
-- Server version: 5.0.27
-- PHP Version: 5.2.0
-- 
-- Database: `sdk`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `account`
-- 

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `superAdmin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `account`
-- 

INSERT INTO `account` (`id`, `name`, `email`, `password`, `superAdmin`) VALUES 
(1, 'Lionel Luchez', 'l1on3l@hotmail.com', '06b74739b6e027fa77dcbbf41b461005', 1),
(2, '0123456789', '0123456789@gmail.com', '781e5e245d69b566979b86e28d23f2c7', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `connection`
-- 

DROP TABLE IF EXISTS `connection`;
CREATE TABLE IF NOT EXISTS `connection` (
  `session_id` mediumint(9) NOT NULL auto_increment,
  `login_date` datetime NOT NULL,
  `logout_date` datetime NOT NULL,
  `ip_address` varchar(16) collate latin1_general_ci NOT NULL,
  `hostname` varchar(80) collate latin1_general_ci NOT NULL,
  `referer` text collate latin1_general_ci,
  `browser` varchar(20) collate latin1_general_ci default NULL,
  `browserVersion` varchar(8) collate latin1_general_ci default NULL,
  `oldIE` enum('yes','no') collate latin1_general_ci default NULL,
  `system_os` varchar(10) collate latin1_general_ci default NULL,
  `language` varchar(3) collate latin1_general_ci default NULL,
  `pages_viewed` smallint(6) NOT NULL default '1',
  PRIMARY KEY  (`session_id`),
  KEY `navigateur` (`browser`),
  KEY `system_os` (`system_os`),
  KEY `oldIE` (`oldIE`),
  KEY `language` (`language`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `connection`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `data`
-- 

DROP TABLE IF EXISTS `data`;
CREATE TABLE IF NOT EXISTS `data` (
  `key` varchar(20) NOT NULL,
  `type` enum('html','text','js','int','float') NOT NULL default 'text',
  `value` text NOT NULL,
  `tiny` tinyint(1) NOT NULL default '1',
  `validator` text COMMENT '''Validate.isXXX'' should be the language tables',
  PRIMARY KEY  (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='`validator`: ''Validate.isXXX'' should be in the lang table';

-- 
-- Dumping data for table `data`
-- 

INSERT INTO `data` (`key`, `type`, `value`, `tiny`, `validator`) VALUES 
('email', 'text', 'Lionel@sdk.com', 1, 'isEmail'),
('regexp_isRobot', 'text', '(\\.googlebot\\.|msnbot\\-|\\.crawl\\.yahoo\\.|\\.exabot\\.|msnbot\\-)', 0, 'isRegExp');

-- --------------------------------------------------------

-- 
-- Table structure for table `language`
-- 

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `langID` char(3) NOT NULL,
  `langName` varchar(10) NOT NULL,
  `isAdminLang` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`langID`),
  UNIQUE KEY `keys` (`langName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `language`
-- 

INSERT INTO `language` (`langID`, `langName`, `isAdminLang`) VALUES 
('en', 'English', 1),
('fr', 'Français', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `loc_key`
-- 

DROP TABLE IF EXISTS `loc_key`;
CREATE TABLE IF NOT EXISTS `loc_key` (
  `locID` smallint(6) NOT NULL auto_increment,
  `key` varchar(50) NOT NULL,
  `type` enum('html','text','js','int','float') NOT NULL COMMENT 'Same values as used in ''classes/string.class.php''',
  `tiny` tinyint(1) NOT NULL default '1',
  `admin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`locID`),
  UNIQUE KEY `key` (`key`),
  KEY `tiny` (`tiny`),
  KEY `admin` (`admin`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=270 ;

-- 
-- Dumping data for table `loc_key`
-- 

INSERT INTO `loc_key` (`locID`, `key`, `type`, `tiny`, `admin`) VALUES 
(1, 'bo.data.edit.error.callBack', 'js', 1, 1),
(2, 'bo.data.edit.error.update', 'js', 0, 1),
(3, 'bo.data.edit.success', 'js', 0, 1),
(4, 'bo.data.email.desc', 'text', 0, 1),
(5, 'bo.data.email.title', 'text', 1, 1),
(6, 'bo.data.form.fieldset', 'text', 1, 1),
(7, 'bo.data.form.submitBtn', 'js', 1, 1),
(8, 'bo.data.form.title', 'text', 1, 1),
(9, 'bo.data.regexp_isRobot.desc', 'text', 0, 1),
(10, 'bo.data.regexp_isRobot.title', 'text', 1, 1),
(11, 'bo.lang.search.emptyFields', 'js', 1, 1),
(12, 'bo.login_form.btn.submit', 'js', 1, 1),
(13, 'bo.login_form.errorAuth', 'js', 1, 1),
(14, 'bo.login_form.fetch_pwd.emailError', 'js', 1, 1),
(15, 'bo.login_form.fetch_pwd.emailMissing', 'js', 1, 1),
(16, 'bo.login_form.fetch_pwd.emailSent', 'js', 1, 1),
(17, 'bo.login_form.field.email', 'text', 1, 1),
(18, 'bo.login_form.field.pass', 'text', 1, 1),
(19, 'bo.login_form.fieldset', 'text', 1, 1),
(20, 'bo.login_form.forgetpassword', 'text', 1, 1),
(21, 'bo.login_form.sfield.email', 'text', 1, 1),
(22, 'bo.login_form.sfield.pass', 'text', 1, 1),
(23, 'bo.login_form.title', 'text', 1, 1),
(24, 'bo.logout.confirmation', 'js', 1, 1),
(25, 'bo.menu.adm_menu', 'text', 1, 1),
(26, 'bo.menu.datatext', 'text', 1, 1),
(27, 'bo.menu.information', 'text', 1, 1),
(28, 'bo.menu.logout', 'text', 1, 1),
(29, 'bo.smenu.cnx', 'text', 1, 1),
(30, 'bo.smenu.contact', 'text', 1, 1),
(31, 'bo.smenu.data', 'text', 1, 1),
(32, 'bo.smenu.help', 'text', 1, 1),
(33, 'bo.smenu.langs', 'text', 1, 1),
(34, 'bo.smenu.texts', 'text', 1, 1),
(35, 'bo.smenu.users', 'text', 1, 1),
(36, 'bo.texts.create.barTitle', 'text', 1, 1),
(37, 'bo.texts.create.langText.stitle', 'text', 1, 1),
(38, 'bo.texts.create.main_fieldset', 'text', 1, 1),
(39, 'bo.texts.create.RscAdmin.stitle', 'text', 1, 1),
(40, 'bo.texts.create.RscAdmin.title', 'text', 1, 1),
(41, 'bo.texts.create.rsc_fieldset', 'text', 1, 1),
(42, 'bo.texts.create.submitBtn', 'text', 1, 1),
(43, 'bo.texts.create.success', 'html', 1, 1),
(44, 'bo.texts.create.title', 'text', 1, 1),
(45, 'bo.texts.error.1', 'js', 1, 1),
(46, 'bo.texts.error.2', 'js', 1, 1),
(47, 'bo.texts.error.3', 'js', 1, 1),
(48, 'bo.texts.error.4', 'js', 1, 1),
(49, 'bo.texts.error.5', 'js', 1, 1),
(50, 'bo.texts.error.6', 'js', 1, 1),
(51, 'bo.texts.results.barTitle', 'text', 1, 1),
(52, 'bo.texts.results.drop.confirm', 'js', 1, 1),
(53, 'bo.texts.results.img.drop.title', 'js', 1, 1),
(54, 'bo.texts.results.img.edit.title', 'js', 1, 1),
(55, 'bo.texts.results.title', 'text', 1, 1),
(56, 'bo.texts.RscContent.stitle', 'text', 1, 1),
(57, 'bo.texts.RscContent.title', 'text', 1, 1),
(58, 'bo.texts.RscKey.stitle', 'text', 1, 1),
(59, 'bo.texts.RscKey.title', 'text', 1, 1),
(60, 'bo.texts.RscTiny.stitle', 'text', 1, 1),
(61, 'bo.texts.RscTiny.title', 'text', 1, 1),
(62, 'bo.texts.RscType.stitle', 'text', 1, 1),
(63, 'bo.texts.RscType.title', 'text', 1, 1),
(64, 'bo.texts.search.fieldset', 'text', 1, 1),
(65, 'bo.texts.search.RscAdmin.stitle', 'text', 1, 1),
(66, 'bo.texts.search.RscAdmin.title', 'text', 1, 1),
(67, 'bo.texts.search.submitBtn', 'js', 1, 1),
(68, 'bo.texts.search.title', 'text', 1, 1),
(69, 'bo.texts.searchID.barTitle', 'text', 1, 1),
(70, 'bo.texts.searchText.barTitle', 'text', 1, 1),
(71, 'bo.texts.search.RscLang.title', 'text', 1, 1),
(72, 'bo.texts.search.title2', 'text', 1, 1),
(73, 'bo.texts.results.errorFindRsc', 'js', 1, 1),
(74, 'bo.texts.edit.title', 'text', 1, 1),
(75, 'bo.texts.edit.submitBtn', 'js', 1, 1),
(76, 'bo.texts.error.7', 'js', 1, 1),
(77, 'bo.texts.error.8', 'js', 1, 1),
(78, 'bo.texts.error.9', 'js', 1, 1),
(79, 'bo.texts.error.10', 'js', 1, 1),
(80, 'bo.texts.edit.popup.title', 'text', 1, 1),
(81, 'bo.texts.error.11', 'js', 1, 1),
(82, 'bo.texts.edit.success', 'js', 1, 1),
(83, 'bo.texts.results.drop.unableDropRes', 'js', 1, 1),
(84, 'bo.texts.ajax.no_keys', 'js', 1, 1),
(85, 'options.all', 'text', 1, 1),
(86, 'options.no', 'text', 1, 1),
(87, 'options.yes', 'text', 1, 1),
(88, 'Popup.CloseTitle', 'js', 1, 1),
(89, 'Popup.DefaultBtnText', 'text', 1, 1),
(90, 'Popup.Title.Default', 'js', 1, 1),
(91, 'Popup.Title.Error', 'js', 1, 1),
(92, 'Popup.Title.Help', 'js', 1, 1),
(93, 'Popup.Title.Information', 'js', 1, 1),
(94, 'Popup.Title.Warning', 'js', 1, 1),
(95, 'rsc.type.all', 'text', 1, 1),
(96, 'rsc.type.float', 'text', 1, 1),
(97, 'rsc.type.html', 'text', 1, 1),
(98, 'rsc.type.int', 'text', 1, 1),
(99, 'rsc.type.js', 'text', 1, 1),
(100, 'rsc.type.text', 'text', 1, 1),
(101, 'lang_all', 'text', 1, 1),
(102, 'lang_en', 'text', 1, 1),
(103, 'lang_fr', 'text', 1, 1),
(104, 'Validate.ErrorPanel.ErrorAlt', 'js', 1, 1),
(105, 'Validate.ErrorPanel.Title', 'js', 1, 1),
(106, 'Validate.isEmail', 'js', 1, 1),
(107, 'Validate.isFloat', 'js', 1, 1),
(108, 'Validate.isInteger', 'js', 1, 1),
(109, 'Validate.isNotEmpty', 'js', 1, 1),
(110, 'Validate.isPassword', 'js', 1, 1),
(111, 'Validate.isRegExp', 'js', 1, 1),
(112, 'Validate.isValidKey', 'js', 1, 1),
(113, 'btn.cancel', 'js', 1, 1),
(200, 'change_language', 'text', 1, 0),
(201, 'forms.requiredField', 'js', 1, 0),
(202, 'meta_description', 'text', 0, 0),
(203, 'meta_keywords', 'text', 0, 0),
(204, 'meta_subject', 'text', 0, 0),
(205, 'site.menu.achievements', 'text', 1, 0),
(206, 'site.menu.cv', 'text', 1, 0),
(207, 'site.menu.home', 'text', 1, 0),
(208, 'site.menu.information', 'text', 1, 0),
(209, 'site.menu.others', 'text', 1, 0),
(210, 'site.smenu.drawings', 'text', 1, 0),
(211, 'site.smenu.otherInfo', 'text', 1, 0),
(212, 'site.smenu.progs', 'text', 1, 0),
(213, 'site.smenu.sites', 'text', 1, 0),
(214, 'site.smenu.tools', 'text', 1, 0),
(215, 'site.smenu.trips', 'text', 1, 0),
(216, 'site.smenu.websites', 'text', 1, 0),
(217, 'test', 'html', 0, 0),
(218, 'prog.cat.multi', 'text', 1, 1),
(219, 'prog.cat.games', 'text', 1, 1),
(220, 'prog.cat.tools', 'text', 1, 1),
(221, 'progs.desc.alp', 'text', 0, 1),
(222, 'progs.desc.divxtech', 'text', 0, 1),
(223, 'page.progs.title', 'text', 1, 1),
(224, 'page.home.title', 'text', 1, 1),
(225, 'progs.desc.advent2d', 'text', 0, 1),
(226, 'progs.desc.imgCompressor', 'text', 0, 1),
(227, 'progs.desc.istria', 'text', 0, 1),
(228, 'progs.desc.mp3renamer', 'text', 0, 1),
(229, 'progs.desc.personalFolders', 'text', 0, 1),
(230, 'progs.desc.pureSources', 'text', 0, 1),
(231, 'progs.desc.videosDownloader', 'text', 0, 1),
(232, 'progs.desc.wallChanger', 'text', 0, 1),
(233, 'progs.label.category', 'text', 1, 1),
(234, 'progs.label.langs', 'text', 1, 1),
(235, 'progs.label.size', 'text', 1, 1),
(236, 'progs.label.version', 'text', 1, 1),
(237, 'progs.label.downloads', 'text', 1, 1),
(238, 'progs.label.previews', 'text', 1, 1),
(239, 'progs.label.downloads.times', 'text', 1, 1),
(240, 'progs.search.title', 'text', 1, 1),
(241, 'prog.cat.all', 'text', 1, 1),
(242, 'progs.search.category', 'text', 1, 1),
(243, 'progs.search.sortby', 'text', 1, 1),
(244, 'progs.search.language', 'text', 1, 1),
(245, 'progs.search.sortby.dl', 'text', 1, 1),
(246, 'progs.search.sortby.name', 'text', 1, 1),
(247, 'progs.search.sortby.date', 'text', 1, 1),
(248, 'progs.search.sortby.category', 'text', 1, 1),
(249, 'progs.search.submit', 'js', 1, 1),
(250, 'progs.img_title.dl', 'js', 1, 1),
(251, 'home.img_title.profile', 'js', 1, 1),
(252, 'progs.nb_progs_found', 'text', 1, 1),
(253, 'Thumbnails.title', 'js', 1, 1),
(254, 'Thumbnails.next', 'js', 1, 1),
(255, 'Thumbnails.previous', 'js', 1, 1),
(256, 'Thumbnails.close', 'js', 1, 1),
(257, 'progs.label.nb_images', 'text', 1, 1),
(258, 'page.cv.title', 'text', 1, 1),
(259, 'page.sites.title', 'text', 1, 1),
(260, 'wsites.desc.basicunivers', 'text', 0, 1),
(261, 'wsites.desc.notaire', 'text', 0, 1),
(262, 'wsites.desc.mb', 'text', 0, 1),
(263, 'wsites.desc.philippe', 'text', 0, 1),
(264, 'wsites.desc.ips', 'text', 0, 1),
(265, 'wsites.desc.papagayo', 'text', 0, 1),
(266, 'wsites.offline.philippe', 'text', 0, 1),
(267, 'wsites.offline.ips', 'text', 0, 1),
(268, 'wsites.label.nb_images', 'text', 1, 1),
(269, 'wsites.offline.text', 'html', 0, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `loc_text`
-- 

DROP TABLE IF EXISTS `loc_text`;
CREATE TABLE IF NOT EXISTS `loc_text` (
  `locIdx` smallint(6) NOT NULL,
  `langIdx` char(3) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`locIdx`,`langIdx`),
  KEY `langIdx` (`langIdx`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `loc_text`
-- 

INSERT INTO `loc_text` (`locIdx`, `langIdx`, `content`) VALUES 
(1, 'en', 'An error has occured. <br />Maybe your session has timed-out !'),
(1, 'fr', 'Une erreur est survenue.<br />Votre session a sûrement expirée !'),
(2, 'en', 'At least one field hasn''t been updated. The field is still marked as unsaved !'),
(2, 'fr', 'Au moins un champ n''a pu être mis à jour.<br />Il est toujours marqué comme non sauvegardé !'),
(3, 'en', 'Updated !'),
(3, 'fr', 'Mise à jour effectuée !'),
(4, 'en', 'Your email address'),
(4, 'fr', 'Votre adresse email'),
(5, 'en', 'Email address'),
(5, 'fr', 'Courriel'),
(6, 'en', 'Data -'),
(6, 'fr', 'Données -'),
(7, 'en', 'Update'),
(7, 'fr', 'Mettre à jour'),
(8, 'en', 'Data management'),
(8, 'fr', 'Gestion des données'),
(9, 'en', 'Regex to identify robots'),
(9, 'fr', 'Regex pour identifier les robots'),
(10, 'en', 'Robots'),
(10, 'fr', 'Robots'),
(11, 'en', 'You need to specify at least one of these two fields: key or text !'),
(11, 'fr', 'Vous devez renseigner au moins un des deux champs: clé ou contenu !'),
(12, 'en', 'Log in'),
(12, 'fr', 'Se connecter'),
(13, 'en', 'Your email or your password is not correct. Please try again !'),
(13, 'fr', 'Votre courriel et/ou mot de mot sont incorrects. Réessayez !'),
(14, 'en', 'Unable to sent the email. Please try again later !'),
(14, 'fr', 'Une erreur est survenue lors de l''envoi du courriel. Réessayez plus tard !'),
(15, 'en', 'You need to specify first your email address !'),
(15, 'fr', 'Vous devez d''abord saisir votre adresse email !'),
(16, 'en', 'An email has been successfuly sent to you. <br />You may receive it soon !'),
(16, 'fr', 'Un courriel vous a été envoyé. <br />Vous devriez bientôt le recevoir !'),
(17, 'en', 'Email'),
(17, 'fr', 'Courriel'),
(18, 'en', 'Password'),
(18, 'fr', 'Mot de passe'),
(19, 'en', 'Required fields'),
(19, 'fr', 'Champs obligatoires'),
(20, 'en', 'I forgot my password'),
(20, 'fr', 'J''ai oublié mon mot de passe'),
(21, 'en', 'Your email address'),
(21, 'fr', 'Votre adresse email'),
(22, 'en', 'Case-sensitive field'),
(22, 'fr', ''),
(23, 'en', 'Authentication'),
(23, 'fr', 'Identification'),
(24, 'en', 'Are you sure to close the current session ?'),
(24, 'fr', 'Etes-vous sûr de vouloir clôturer votre session ?'),
(25, 'en', 'Admin menu'),
(25, 'fr', 'Admin menu'),
(26, 'en', 'Texts and data'),
(26, 'fr', 'Textes et données'),
(27, 'en', 'Information'),
(27, 'fr', 'Informations'),
(28, 'en', 'Logout'),
(28, 'fr', 'Déconnexion'),
(29, 'en', 'Connections'),
(29, 'fr', 'Connexions'),
(30, 'en', 'Contact'),
(30, 'fr', 'Contact'),
(31, 'en', 'Global data'),
(31, 'fr', 'Données'),
(32, 'en', 'Help'),
(32, 'fr', 'Aide'),
(33, 'en', 'Languages'),
(33, 'fr', 'Langues'),
(34, 'en', 'Translated texts'),
(34, 'fr', 'Textes traduits'),
(35, 'en', 'Users'),
(35, 'fr', 'Utilisateurs'),
(36, 'en', 'Create a new ressource'),
(36, 'fr', 'Créer une nouvelle ressource'),
(37, 'en', 'Text for this language'),
(37, 'fr', 'Texte pour cette langue'),
(38, 'en', 'Ressource information'),
(38, 'fr', 'Informations sur la ressource'),
(39, 'en', 'Ressource for admin part'),
(39, 'fr', 'Ressource pour la partie admin'),
(40, 'en', 'For Back Office'),
(40, 'fr', 'Pour l''administration'),
(41, 'en', 'Ressource texts'),
(41, 'fr', 'Textes de la ressource'),
(42, 'en', 'Create'),
(42, 'fr', 'Créer'),
(43, 'en', 'Ressource %1 correctly created'),
(43, 'fr', 'La ressource %1 a bien été crée'),
(44, 'en', 'Create a ressource'),
(44, 'fr', 'Création d''une ressource'),
(45, 'en', 'Invalid key name !'),
(45, 'fr', 'Clé invalide. Veuillez saisir un autre nom !'),
(46, 'en', 'Duplicate key ! Choose another name !'),
(46, 'fr', 'Clé déjà utilisée. Veuillez saisir un autre nom !'),
(47, 'en', 'You need to select a correct type for the ressource !'),
(47, 'fr', 'Vous devez sélectionner un type correct pour la ressource a créer !'),
(48, 'en', 'An SQL error as occured. This ressource is maybe corrupted !'),
(48, 'fr', 'Une erreur SQL est survenue. Cette ressource est peut-être corrompue !'),
(49, 'en', 'Wrong language selected !'),
(49, 'fr', 'La langue choisie n''est pas valide !'),
(50, 'en', 'No match found for your search !'),
(50, 'fr', 'Aucun résultat ne correspond à votre recherche !'),
(51, 'en', 'Search results'),
(51, 'fr', 'Résultats de la recherche'),
(52, 'en', 'Are you sure to delete this ressource for all languages ?'),
(52, 'fr', 'Etes-vous sûr de supprimer cette ressource pour toutes les langues ?'),
(53, 'en', 'Delete the ressource for all languages ?'),
(53, 'fr', 'Supprimer la ressource pour tous les langages ?'),
(54, 'en', 'Edit the ressource'),
(54, 'fr', 'Modifier la ressource'),
(55, 'en', 'Results of your search'),
(55, 'fr', 'Résultats de votre recherche'),
(56, 'en', 'Ressource content/text'),
(56, 'fr', 'Text dans la ressource'),
(57, 'en', 'Content'),
(57, 'fr', 'Contenu'),
(58, 'en', 'Ressource key'),
(58, 'fr', 'Identifiant'),
(59, 'en', 'Key'),
(59, 'fr', 'Clé'),
(60, 'en', 'Display a small field'),
(60, 'fr', 'Petite zonde de texte'),
(61, 'en', 'Short text'),
(61, 'fr', 'Texte court'),
(62, 'en', 'Ressource type'),
(62, 'fr', 'Type de ressource'),
(63, 'en', 'Type'),
(63, 'fr', 'Type'),
(64, 'en', 'Information to find ressources'),
(64, 'fr', 'Informations pour la recherche de ressources'),
(65, 'en', 'Admin part ressource ?'),
(65, 'fr', 'Pour la partie Admin ?'),
(66, 'en', 'For Back Office'),
(66, 'fr', 'Visibilité'),
(67, 'en', 'Search'),
(67, 'fr', 'Rechercher'),
(68, 'en', 'Search for localized text'),
(68, 'fr', 'Rechercher un texte traduit'),
(69, 'en', 'Search for localized text by key/ID'),
(69, 'fr', 'Recherche de ressource par clé/ID'),
(70, 'en', 'Search for localized text from its content'),
(70, 'fr', 'Recherche de ressource par son contenu'),
(71, 'en', 'Language'),
(71, 'fr', 'Langage'),
(72, 'en', 'Search text by ID'),
(72, 'fr', 'Rechercher un texte par son identifiant'),
(73, 'en', 'Unable to find this ressource !'),
(73, 'fr', 'Ressource introuvable !'),
(74, 'en', 'Edit a ressource'),
(74, 'fr', 'Modification d''une ressource'),
(75, 'en', 'Update'),
(75, 'fr', 'Modifier'),
(76, 'en', '''Yes'' should be selected for this field (because of the resource type) !'),
(76, 'fr', 'Ce champ doit être laissé à ''Oui'' à cause du type de ressource choisi !'),
(77, 'en', 'Unable to find the selected resource !'),
(77, 'fr', 'Impossible de trouver la ressource sélectionnée !'),
(78, 'en', 'Resource key already used by another !'),
(78, 'fr', 'Le nom de la ressource ets déjà utilisé par une autre ressource !'),
(79, 'en', 'Error while changing resource properties !'),
(79, 'fr', 'Les nouvelles propriétés sont incorrectes !'),
(80, 'en', 'Edit a resource'),
(80, 'fr', 'Modifier une ressource'),
(81, 'en', 'An SQL error as occured. This ressource is maybe corrupted !'),
(81, 'fr', 'Une erreur SQL est survenue. Cette ressource est peut-être corrompue !'),
(82, 'en', 'The resource has been succesfully edited !'),
(82, 'fr', 'La ressource a bien été modifiée !'),
(83, 'en', 'Unable to delete this resource. Something went wrong !'),
(83, 'fr', 'Une erreur est survenue lors de la suppression de cette ressource !'),
(84, 'en', 'No keys found !'),
(84, 'fr', 'Aucune clée trouvée !'),
(85, 'en', 'All'),
(85, 'fr', 'Tous'),
(86, 'en', 'No'),
(86, 'fr', 'Non'),
(87, 'en', 'Yes'),
(87, 'fr', 'Oui'),
(88, 'en', 'Close this popup'),
(88, 'fr', 'Fermer cette fenêtre'),
(89, 'en', 'Ok'),
(89, 'fr', 'Ok'),
(90, 'en', 'Popup window'),
(90, 'fr', 'Fenêtre popup'),
(91, 'en', 'Error'),
(91, 'fr', 'Une erreur est survenue...'),
(92, 'en', 'Help'),
(92, 'fr', 'Fenêtre d''aide'),
(93, 'en', 'Information'),
(93, 'fr', 'Information'),
(94, 'en', 'Warning'),
(94, 'fr', 'Attention'),
(95, 'en', 'All'),
(95, 'fr', 'Tous'),
(96, 'en', 'Float'),
(96, 'fr', 'Nombre réel'),
(97, 'en', 'HTML text'),
(97, 'fr', 'Texte au format HTML'),
(98, 'en', 'Integer'),
(98, 'fr', 'Nombre entier'),
(99, 'en', 'JavaScript'),
(99, 'fr', 'JavaScript'),
(100, 'en', 'Raw text'),
(100, 'fr', 'Texte brut'),
(101, 'en', 'All languages'),
(101, 'fr', 'Toutes les langues'),
(102, 'en', 'English'),
(102, 'fr', 'Anglais'),
(103, 'en', 'French'),
(103, 'fr', 'Français'),
(104, 'en', 'Click to focus on the wrong field'),
(104, 'fr', 'Cliquez pour voir le champ gênant'),
(105, 'en', 'Please review the following items :'),
(105, 'fr', 'Veuillez vérifier les items suivants :'),
(106, 'en', 'The email address is not correct !'),
(106, 'fr', 'Le courriel saisi ne semble pas être correct !'),
(107, 'en', 'You should type a float !'),
(107, 'fr', 'Vous devez entrer un nombre réel !'),
(108, 'en', 'You should type an integer !'),
(108, 'fr', 'Vous devez saisir un nombre entier !'),
(109, 'en', 'Please fill this field !'),
(109, 'fr', 'The champ ne doit pas être vide !'),
(110, 'en', 'The password needs to be at least 8 chars long !'),
(110, 'fr', 'Le mot de passe doit contenir au moins 8 caractères !'),
(111, 'en', 'The RegExp is not correct !'),
(111, 'fr', 'L''expression régulière saisie est incorrecte !'),
(112, 'en', 'The key is not correct !'),
(112, 'fr', 'La clé n''est pas correcte !'),
(113, 'en', 'Cancel'),
(113, 'fr', 'Annuler'),
(200, 'en', 'Switch to'),
(200, 'fr', 'Passer en'),
(201, 'en', 'Required field'),
(201, 'fr', 'Champ obligatoire'),
(202, 'en', ''),
(202, 'fr', ''),
(203, 'en', ''),
(203, 'fr', ''),
(204, 'en', ''),
(204, 'fr', ''),
(205, 'en', 'Achievements'),
(205, 'fr', 'Réalisations'),
(206, 'en', 'Curriculum Vitæ'),
(206, 'fr', 'Curriculum Vitæ'),
(207, 'en', 'Home'),
(207, 'fr', 'Accueil'),
(208, 'en', 'Information'),
(208, 'fr', 'Informations'),
(209, 'en', 'Tips: Tools & Websites'),
(209, 'fr', 'Astuces: tools & sites'),
(210, 'en', 'Drawings'),
(210, 'fr', 'Dessins'),
(211, 'en', 'Other information'),
(211, 'fr', 'Infos supplémentaires'),
(212, 'en', 'Programs'),
(212, 'fr', 'Programmes'),
(213, 'en', 'Websites'),
(213, 'fr', 'Sites Web'),
(214, 'en', 'Free tools'),
(214, 'fr', 'Gratuiciels'),
(215, 'en', 'Trips'),
(215, 'fr', 'Voyages'),
(216, 'en', 'Web sites selection'),
(216, 'fr', 'Sites recommandés'),
(217, 'en', 'This is %text% within ¤email¤<br />\nLink to another page: HTML(''Test?aa=bb&cc=dd'',''here'')<br />\nLink to another page: DATA(''test.zip'',''archive test'')<br />\nLink to another page: HTML(''Test?abc=cdb'',''la'')<br />'),
(217, 'fr', 'Ceci est %text% pris dans ¤email¤<br />\nLien vers une autre page: HTML(''Test?aa=bb&cc=dd'',''here'')<br />\nLien vers une autre page: DATA(''test.zip'',''archive test'')<br />\nLien vers une autre page: HTML(''Test?abc=cdb'',''la'')<br />'),
(218, 'en', 'Multimedia'),
(218, 'fr', 'Multimédia'),
(219, 'en', 'Games'),
(219, 'fr', 'Jeux vidéos'),
(220, 'en', 'Tools'),
(220, 'fr', 'Outils'),
(221, 'en', 'ALP is a simple player supporting most of audio files (MP3, WAV, etc.). You can easily create playlists (M3U format) by dragging files and retrieving songs with the search engine included (by typing parts of the song/artist name).'),
(221, 'fr', 'ALP est un lecteur audio simple à utiliser qui supporte la plupart des formats (MP3, WAV, etc). Vous pouvez créer facilement des listes de lectures (au format M3U) en glissant-copiant des fichiers ou en les recherchant avec le moteur de recherche inclus (en tapant une partie du nom de la chanson ou de l''artiste).'),
(222, 'en', 'Div-X-Tech is a library containing the movies (DivX or DVDs) you have. Add you movies in there (synopsis, category, actors, duration, date, etc.). Then when you don''t know which one to see, use the search engine with criterion to find one you would like to watch.'),
(222, 'fr', 'Div-X-Tech est une bibliothèque recensant vos DivX ou DVDs. Ajouter vos films en précisant des champs comme résumé, acteurs, catégorie, durée, date de sortie. Ainsi vus pourrez trouver facilement un film à vous mettre sous la dent en précisant quelques critères.'),
(223, 'en', 'My programs'),
(223, 'fr', 'Mes programmes'),
(224, 'en', 'Home'),
(224, 'fr', 'Accueil'),
(225, 'en', 'Advent2D is a short platform game on the moon. Get to the end without falling down into infinite holes by jumping over moving rocks/walls.'),
(225, 'fr', 'Advent2D est un petit jeu de plates-formes sur la lune. Arrivez jusqu''à la fin sans tomber dans les cratères tout en vous aidant des éléments mouvants.'),
(226, 'en', 'Images Compressor is useful to improve the compression of your photos (select the rate) or to convert your images onto another format (BMP, PNG, JPEG). Images Compressor has other features: images resizing, mass renaming, deleting source files, moving new files to another path, etc. This program has been designed to work with folders.'),
(226, 'fr', 'Images Compressor vous permettant de mieux compresser vos photos (selon différents niveaux d''encodage) ou de convertir vos images sous un autre format (BMP, PNG ou JPEG). Images Compressor permet également diverses options dont le redimensionnement des images, le renommage groupé, la suppression des fichiers source, le déplacement vers un autre dossier, etc. Ce programme est surtout conçu pour travailler par répertoire.'),
(227, 'en', 'Istria is a short medieval RPG (technical demo). Get your weapons, move through animated maps and fight monsters. About 20min games (1 quest).'),
(227, 'fr', 'Istria est un court RPG médiéval (démo technique). Récupérez vos armes, déplacez vous dans les décors animés et combattez les monstres qui se mettront sur votre chemin. Cette démo dure environ 20min (1 quête)'),
(228, 'en', 'MP3 Renamer rename for you MP3 albums. Choice the pattern you want for the new names and rename all musics with one click.'),
(228, 'fr', 'MP3 Renamer vous permet de renommer facilement toute une série de fichiers MP3 d''un même artiste en choisissant le formatage du futur nom.'),
(229, 'en', 'Change the location of common directories: "My Documents", "My Music", "Desktop", "Start Menu", etc. Useful when you get your data on another separated drive/partition.'),
(229, 'fr', 'Personal Folders vous permet de modifier l''emplacement des dossiers tels que : "Mes documents", "Ma Musique", "Bureau", "Menu Démarrer", etc. Très utile quand vos données sont sur un autre disque dur (ou partition).'),
(230, 'en', 'PureSources makes you fast find text files with a simple search UI. Choose search options to find the file you are searching for: search type, files folder, file extensions, etc.'),
(230, 'fr', 'PureSources est un utilitaire vous permettant de retrouver facilement des fichiers textes en grace à une interface de recherche. Diverses options de recherches vous permettent de localiser le fichier que vous cherchez (type de recherche, dossier de recherche, extension des fichiers, etc.)'),
(231, 'en', 'Contains 2 programs to download videos from Youtube and Dailymotion. Simple to use and efficient.'),
(231, 'fr', 'Contient deux programmes permettent de télécharger des vidéos sur Youtube ou DailyMotion. Très simple d''emploi et très efficace.'),
(232, 'en', 'WallChanger is a wallpaper play-list maker. Easy to use: define groups (from folder or text-file), choose an active group and every n seconds (delay editable) a new picture will be set as wallpaper. This program is barely visible: no windows, right-click on the corresponding icon on the systray-bar (aside Windows clock) to perform actions.'),
(232, 'fr', 'WallChanger vous permet de définir une playlist d''arrières-plan. Ce programme est très simple d''emploi : on défini des groupes (soit en spécifiant un répertoire, soit un fichier de sélection: fichier texte). Puis on choisi le groupe actif et toutes les n secondes (modifiable) une nouvelle image est appliquée en fond d''écran. En plus il est quasi-invisible: pour accéder aux diverses fonctions, un clic sur son icône près de l''horloge Windows permet de le faire réapparaître.'),
(233, 'en', 'Category'),
(233, 'fr', 'Catégorie'),
(234, 'en', 'Languages'),
(234, 'fr', 'Langues'),
(235, 'en', 'Size'),
(235, 'fr', 'Taille'),
(236, 'en', 'Version'),
(236, 'fr', 'Version'),
(237, 'en', 'Downloads'),
(237, 'fr', 'Téléchargements'),
(238, 'en', 'Previews'),
(238, 'fr', 'Aperçus'),
(239, 'en', '(q1:Nerver|Once|% times)'),
(239, 'fr', '(q1:Jamais|Une fois|% fois)'),
(240, 'en', 'Search filters'),
(240, 'fr', 'Filtres de recherche'),
(241, 'en', 'All categories'),
(241, 'fr', 'Toutes les catégories'),
(242, 'en', 'Category'),
(242, 'fr', 'Catégorie'),
(243, 'en', 'Sort by'),
(243, 'fr', 'Trier par'),
(244, 'en', 'Language'),
(244, 'fr', 'Langue'),
(245, 'en', 'Downloads'),
(245, 'fr', 'Téléchargements'),
(246, 'en', 'Name'),
(246, 'fr', 'Nom'),
(247, 'en', 'Date'),
(247, 'fr', 'Date'),
(248, 'en', 'Category'),
(248, 'fr', 'Catégorie'),
(249, 'en', 'Filter'),
(249, 'fr', 'Filtrer'),
(250, 'en', 'Download'),
(250, 'fr', 'Télécharger'),
(251, 'en', 'Profile picture: Lionel Luchez'),
(251, 'fr', 'Photo de profil: Lionel Luchez'),
(252, 'en', '(q0:None|%|%) program(q1:|s) ha(q2:s|ve) been found'),
(252, 'fr', '(q0:Aucun|%|%) programme(q1:|s) (q2:n''a|a|ont) été trouvé(q3:|s)'),
(253, 'en', 'Thumbnails preview'),
(253, 'fr', 'Visionneuse d''images'),
(254, 'en', 'Next'),
(254, 'fr', 'Suivant'),
(255, 'en', 'Previous'),
(255, 'fr', 'Précédent'),
(256, 'en', 'Close this window'),
(256, 'fr', 'Fermer l''aperçu'),
(257, 'en', '(q0:None|One|%) preview(q1:|s)'),
(257, 'fr', '(q0:Aucune|Une|%) image(q1:|s)'),
(258, 'en', 'Curriculum Vitæ'),
(258, 'fr', 'Curriculum Vitæ'),
(259, 'en', 'Websites'),
(259, 'fr', 'Sites Web'),
(260, 'en', 'Basicunivers has been created in order to share information related to Basic programming languages as PureBasic (PB), DarkBasic (alias 3DGC), Visual Basic and QuickBasic. It has a PB source sharing system and user-list programs developed with Basic languages. With this site I can also expose my drawings, school-projects and my main project: Istria (medieval RPG developed during more than one year). Finally a freeware/software selection can be useful for your computer.'),
(260, 'fr', 'Basicunivers a été créé dans le but de permettre le partage d’informations autour des langages Basic tels que PureBasic (PB) et DarkBasic (alias 3DGC), ainsi que Visual Basic et QuickBasic. Il permet de partager des codes sources PB et des programmes fait avec ces différents langages Basic. Ce site est aussi une vitrine pour mes dessins, projets scolaires et surtout au plus important de mes projets : Istria (RPG médiéval développé pendant plus d’un an). Dans un dernier temps, une sélection de logiciels et gratuiciels vous permet d’équiper correctement votre ordinateur.'),
(261, 'en', 'This Website has been design for a solicitor (Master Ludovic Merlin) located in Oise (North of France). He wanted to have a store window with real estate property items (flats, sites) for sale purpose.'),
(261, 'fr', 'Ce site a été développé pour un notaire (maître Ludovic Merlin) basé dans l’Oise qui voulait diffuser sur Internet les biens immobiliers (maisons, terrains, locaux) dans un but commercial de vente.'),
(262, 'en', 'Modules Briques (MB) is a building SME with made-to-measure services. This Website is a store window for their products with a picture gallery and technical documents.'),
(262, 'fr', 'Modules Briques (MB) est une société dans le bâtiment proposant des services sur mesures. Ce site met en avant les produits réalisés par MB grâce à une galerie photo et des fiches techniques.'),
(263, 'en', 'Philippe de Bussy is a butcher sailing quality meet with variety of quality labels. He also has local products and local dishes. Recipes and cooking tips are available.'),
(263, 'fr', 'Philippe de Bussy est boucher-charcutier proposant de la viande de qualité selon divers labels ainsi que des produits de terroir et des plats traiteurs de qualité. Des recettes et conseils de préparation sont également disponibles.'),
(264, 'en', 'Instant Pour Soi is about relaxing massages to take off all the stress you have during everyday life. Once the massage over you will feel release from everything. Lucien Luchez will help you find the right care you need to restart on the right way.'),
(264, 'fr', 'Instant Pour Soi vous propose des massages de relaxation afin d’évacuer le stress de la vie quotidienne et de sentir un vrai apaisement physique et mental. Lucien Luchez vous aidera à trouver le massage qu’il vous faut pour repartir du bon pied.'),
(265, 'en', 'Papagayo is a hotel at Sihanoukville (Cambodia) close to the sea. Here you will find everything you need for a relaxing and peaceful holiday. Many services are available for nothing to bother you during your journey.'),
(265, 'fr', 'Papagayo est un hôtel à Sihanoukville (Cambodge) près de la mer qui vous permettra de passer des vacances de qualité en toute quiétude. De nombreux services sont à votre disposition pour qu’aucune contrainte ne vienne perturber votre séjour.'),
(266, 'en', 'This Website should have evolved to propose mail orders but the postal service refused to assure products, hence the project has been abandoned. Cooking tips pages were not really viewed as people preferred to talk to Philippe instead of reading the Net.'),
(266, 'fr', 'Ce site devait être une première étape à une future refonte pour offrir de la vente par correspondance mais La Poste ne pouvant assurer l’envoie par Collisimo l’idée a été abandonnée. La partie recettes et tips n’a pas beaucoup été visitée car les gens préféraient dialoguer avec Philippe.'),
(267, 'en', 'In spite of my advice IPS was too much verbose (too much text). As I hadn’t enough time to update this Website someone else did that job and you can see now the new version.'),
(267, 'fr', 'Malgré mes conseils, IPS contenait beaucoup trop de textes. N’ayant pas assez de temps pour me relancer sur la modification de ce site ces changements ont été faits par quelqu’un d’autre. Le nouveau site est toujours disponible.'),
(268, 'en', '(q0:None|One|%) preview(q1:|s)'),
(268, 'fr', '(q0:Aucune|Une|%) image(q1:|s)'),
(269, 'en', '<b>Warning:</b><br />This Website is no longer online or made by me. Read the section above for more details.'),
(269, 'fr', '<b>Attention</b>:<br />Le site que j''ai réalisé n''est plus en ligne. Lisez le paragraphe ci-dessus pour en connaître les détails.');

-- --------------------------------------------------------

-- 
-- Table structure for table `privileges`
-- 

DROP TABLE IF EXISTS `privileges`;
CREATE TABLE IF NOT EXISTS `privileges` (
  `account_id` tinyint(4) NOT NULL,
  `editAccount` tinyint(1) NOT NULL default '0',
  `editData` tinyint(1) NOT NULL default '0',
  `editTexts` tinyint(1) NOT NULL default '0',
  `addTexts` tinyint(1) NOT NULL default '0',
  `delTexts` tinyint(1) NOT NULL default '0',
  `delSqlLog` binary(1) NOT NULL default '0',
  PRIMARY KEY  (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `privileges`
-- 

INSERT INTO `privileges` (`account_id`, `editAccount`, `editData`, `editTexts`, `addTexts`, `delTexts`, `delSqlLog`) VALUES 
(1, 1, 1, 1, 1, 1, 0x30),
(2, 0, 1, 1, 0, 0, 0x30);

-- --------------------------------------------------------

-- 
-- Table structure for table `prog_category`
-- 

DROP TABLE IF EXISTS `prog_category`;
CREATE TABLE IF NOT EXISTS `prog_category` (
  `cID` tinyint(4) NOT NULL auto_increment,
  `cName` varchar(50) NOT NULL,
  `cIcon` varchar(20) NOT NULL,
  `cClass` varchar(16) NOT NULL,
  PRIMARY KEY  (`cID`),
  UNIQUE KEY `name` (`cName`,`cIcon`,`cClass`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `prog_category`
-- 

INSERT INTO `prog_category` (`cID`, `cName`, `cIcon`, `cClass`) VALUES 
(2, 'prog.cat.games', 'games', 'cat_games'),
(1, 'prog.cat.multi', 'multi', 'cat_multi'),
(3, 'prog.cat.tools', 'tools', 'cat_tools');

-- --------------------------------------------------------

-- 
-- Table structure for table `prog_photos`
-- 

DROP TABLE IF EXISTS `prog_photos`;
CREATE TABLE IF NOT EXISTS `prog_photos` (
  `phProgIDx` tinyint(4) NOT NULL,
  `phOrder` tinyint(4) NOT NULL,
  `phFilename` varchar(30) NOT NULL,
  PRIMARY KEY  (`phProgIDx`,`phOrder`),
  UNIQUE KEY `phFilename` (`phFilename`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `prog_photos`
-- 

INSERT INTO `prog_photos` (`phProgIDx`, `phOrder`, `phFilename`) VALUES 
(5, 1, 'advent2D_1.png'),
(5, 2, 'advent2D_2.png'),
(5, 3, 'advent2D_3.png'),
(1, 3, 'alp_config.png'),
(1, 1, 'alp_hifi.png'),
(1, 6, 'alp_maj.png'),
(1, 4, 'alp_pl.png'),
(1, 2, 'alp_roxor.png'),
(1, 5, 'alp_skins.png'),
(2, 1, 'divxtech_1.png'),
(2, 2, 'divxtech_2.png'),
(2, 3, 'divxtech_3.png'),
(2, 4, 'divxtech_4.png'),
(2, 5, 'divxtech_5.png'),
(3, 1, 'ImagesCompressor2_1.png'),
(3, 2, 'ImagesCompressor2_2.png'),
(3, 3, 'ImagesCompressor2_3.png'),
(3, 4, 'ImagesCompressor2_4.png'),
(4, 1, 'Istria_1.png'),
(4, 2, 'Istria_2.png'),
(4, 3, 'Istria_3.png'),
(4, 4, 'Istria_4.png'),
(4, 5, 'Istria_5.png'),
(4, 6, 'Istria_6.gif'),
(6, 1, 'mp3_renammer.png'),
(7, 1, 'personal_folder_1.png'),
(7, 2, 'personal_folder_2.png'),
(8, 1, 'PureSources_1.png'),
(8, 2, 'PureSources_2.png'),
(10, 1, 'wallchanger1.png'),
(10, 2, 'wallchanger2.png');

-- --------------------------------------------------------

-- 
-- Table structure for table `program`
-- 

DROP TABLE IF EXISTS `program`;
CREATE TABLE IF NOT EXISTS `program` (
  `pID` tinyint(4) NOT NULL auto_increment,
  `pName` varchar(50) NOT NULL COMMENT 'Not unique for different versions case',
  `pFilename` text NOT NULL,
  `pDescription` varchar(50) NOT NULL COMMENT 'langkey name',
  `pCategoryIdx` tinyint(4) NOT NULL,
  `pVersion` varchar(15) NOT NULL,
  `pLanguages` varchar(10) NOT NULL COMMENT 'lang code imploded with '',''',
  `pDownloads` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`pID`),
  KEY `fk_id_prog` (`pCategoryIdx`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='filesize and filedate are autocomputed' AUTO_INCREMENT=11 ;

-- 
-- Dumping data for table `program`
-- 

INSERT INTO `program` (`pID`, `pName`, `pFilename`, `pDescription`, `pCategoryIdx`, `pVersion`, `pLanguages`, `pDownloads`) VALUES 
(1, 'Audio Lib Player (ALP)', 'ALP.zip', 'progs.desc.alp', 1, '', 'en,fr', 0),
(2, 'Div-X-Tech', 'Div-X-Tech.zip', 'progs.desc.divxtech', 1, '', 'fr', 0),
(3, 'Images Compressor 2', 'ImagesCompressor2.zip', 'progs.desc.imgCompressor', 1, '2.5', 'en,fr', 0),
(4, 'Istria', 'Istria.zip', 'progs.desc.istria', 2, 'demo', 'en,fr', 0),
(5, 'Advent 2D', 'Advent2D.zip', 'progs.desc.advent2d', 2, '', 'en', 3),
(6, 'MP3 Renamer', 'MP3 renamer.zip', 'progs.desc.mp3renamer', 3, '', 'fr', 0),
(7, 'Personal Folders', 'PersonalFolders.zip', 'progs.desc.personalFolders', 3, '', 'fr', 0),
(8, 'Pure Sources', 'PureSources.zip', 'progs.desc.pureSources', 3, '', 'en,fr', 0),
(9, 'Videos Downloader', 'Videos Downloader.zip', 'progs.desc.videosDownloader', 1, 'beta', 'en,fr', 0),
(10, 'WallChanger', 'WallChanger.zip', 'progs.desc.wallChanger', 3, '1.2', 'en', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `rsc_type`
-- 

DROP TABLE IF EXISTS `rsc_type`;
CREATE TABLE IF NOT EXISTS `rsc_type` (
  `key` varchar(6) NOT NULL,
  `value` text NOT NULL,
  `order` tinyint(4) NOT NULL,
  `tiny` tinyint(1) NOT NULL,
  `validator` text COMMENT '''Validate.isXXX'' should be the language tables',
  PRIMARY KEY  (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `rsc_type`
-- 

INSERT INTO `rsc_type` (`key`, `value`, `order`, `tiny`, `validator`) VALUES 
('float', 'rsc.type.float', 5, 1, 'isFloat'),
('html', 'rsc.type.html', 2, 0, NULL),
('int', 'rsc.type.int', 4, 1, 'isInteger'),
('js', 'rsc.type.js', 3, 1, NULL),
('text', 'rsc.type.text', 1, 0, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `sql_log`
-- 

DROP TABLE IF EXISTS `sql_log`;
CREATE TABLE IF NOT EXISTS `sql_log` (
  `logUserIdx` mediumint(9) NOT NULL,
  `logDate` datetime NOT NULL,
  `logSqlQuery` text NOT NULL,
  `logUrl` text NOT NULL,
  `logErrNumber` smallint(6) NOT NULL,
  `logErrMsg` text NOT NULL
) ENGINE=ARCHIVE DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `sql_log`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `test`
-- 

DROP TABLE IF EXISTS `test`;
CREATE TABLE IF NOT EXISTS `test` (
  `id` smallint(6) NOT NULL auto_increment,
  `name` text character set utf8 collate utf8_swedish_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `test`
-- 

INSERT INTO `test` (`id`, `name`) VALUES 
(2, 'dfs fsdf sdfsd'),
(3, 'c''est supèèéèéèéèéres !!!');

-- --------------------------------------------------------

-- 
-- Table structure for table `viewed_page`
-- 

DROP TABLE IF EXISTS `viewed_page`;
CREATE TABLE IF NOT EXISTS `viewed_page` (
  `pageID` varchar(20) NOT NULL,
  `page_type` varchar(8) NOT NULL,
  `url_query` text NOT NULL,
  `session_id` mediumint(9) NOT NULL,
  `display_time` date NOT NULL,
  KEY `session_id` (`session_id`,`display_time`),
  KEY `page_type` (`page_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `viewed_page`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `website`
-- 

DROP TABLE IF EXISTS `website`;
CREATE TABLE IF NOT EXISTS `website` (
  `wID` tinyint(4) NOT NULL auto_increment,
  `wName` varchar(50) NOT NULL,
  `wLogo` varchar(20) NOT NULL,
  `wLanguages` varchar(10) NOT NULL,
  `wDescription` varchar(50) NOT NULL,
  `wCreationYear` smallint(6) NOT NULL,
  `wOfflineReason` varchar(50) default NULL,
  `wLink` text,
  PRIMARY KEY  (`wID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `website`
-- 

INSERT INTO `website` (`wID`, `wName`, `wLogo`, `wLanguages`, `wDescription`, `wCreationYear`, `wOfflineReason`, `wLink`) VALUES 
(1, 'Basic Univers', 'basicunivers.jpg', 'fr', 'wsites.desc.basicunivers', 2006, NULL, 'http://basicunivers.free.fr/'),
(2, 'Ludovic Merlin Maître Notaire', 'notaire.jpg', 'fr', 'wsites.desc.notaire', 2007, NULL, 'http://www.merlin-ludovic-notaire.com/'),
(3, 'Modules Briques', 'mb.jpg', 'fr', 'wsites.desc.mb', 2007, NULL, 'http://www.mb-bossuet.com/'),
(4, 'Boucherie Philippe de Bussy', 'philippe.jpg', 'fr', 'wsites.desc.philippe', 2008, 'wsites.offline.philippe', NULL),
(5, 'Instant Pour Soi', 'ips.jpg', 'fr', 'wsites.desc.ips', 2008, 'wsites.offline.ips', 'http://www.instantpoursoi.fr/'),
(6, 'Papagayo', 'papagayo.jpg', 'en,fr', 'wsites.desc.papagayo', 2010, NULL, 'http://www.papagayo-hostel.com/');

-- --------------------------------------------------------

-- 
-- Table structure for table `website_photos`
-- 

DROP TABLE IF EXISTS `website_photos`;
CREATE TABLE IF NOT EXISTS `website_photos` (
  `phSiteIDx` tinyint(4) NOT NULL,
  `phOrder` tinyint(4) NOT NULL,
  `phFilename` varchar(30) NOT NULL,
  PRIMARY KEY  (`phSiteIDx`,`phOrder`),
  UNIQUE KEY `whFilename` (`phFilename`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `website_photos`
-- 

INSERT INTO `website_photos` (`phSiteIDx`, `phOrder`, `phFilename`) VALUES 
(1, 1, 'basicunivers1.jpg'),
(1, 2, 'basicunivers2.jpg'),
(1, 3, 'basicunivers3.jpg'),
(1, 4, 'basicunivers4.jpg'),
(1, 5, 'basicunivers5.jpg'),
(1, 6, 'basicunivers6.jpg'),
(1, 7, 'basicunivers7.jpg'),
(5, 1, 'ips1.jpg'),
(5, 2, 'ips2.jpg'),
(5, 3, 'ips3.jpg'),
(3, 1, 'mb1.jpg'),
(3, 2, 'mb2.jpg'),
(3, 3, 'mb3.jpg'),
(3, 4, 'mb4.jpg'),
(3, 5, 'mb_bo1.jpg'),
(3, 6, 'mb_bo2.jpg'),
(3, 7, 'mb_bo3.jpg'),
(2, 1, 'notaire1.jpg'),
(2, 2, 'notaire2.jpg'),
(2, 3, 'notaire3.jpg'),
(2, 4, 'notaire_bo1.jpg'),
(6, 1, 'papagayo1.jpg'),
(6, 2, 'papagayo2.jpg'),
(6, 3, 'papagayo3.jpg'),
(6, 4, 'papagayo4.jpg'),
(6, 5, 'papagayo_bo1.jpg'),
(6, 6, 'papagayo_bo2.jpg'),
(6, 7, 'papagayo_bo3.jpg'),
(4, 1, 'philippe1.jpg'),
(4, 2, 'philippe2.jpg'),
(4, 3, 'philippe3.jpg'),
(4, 4, 'philippe_bo1.jpg'),
(4, 5, 'philippe_bo2.jpg'),
(4, 6, 'philippe_bo3.jpg');

-- --------------------------------------------------------

-- 
-- Table structure for table `loc_view`
-- 

DROP VIEW IF EXISTS `loc_view`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sdk`.`loc_view` AS select `k`.`locID` AS `locID`,`k`.`key` AS `key`,`k`.`type` AS `type`,`k`.`tiny` AS `tiny`,`k`.`admin` AS `admin`,`t`.`locIdx` AS `locIdx`,`t`.`langIdx` AS `langIdx`,`t`.`content` AS `content` from (`sdk`.`loc_text` `t` join `sdk`.`loc_key` `k` on((`k`.`locID` = `t`.`locIdx`)));

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `loc_text`
-- 
ALTER TABLE `loc_text`
  ADD CONSTRAINT `loc_text_ibfk_1` FOREIGN KEY (`locIdx`) REFERENCES `loc_key` (`locID`),  ADD CONSTRAINT `loc_text_ibfk_2` FOREIGN KEY (`langIdx`) REFERENCES `language` (`langID`);

-- 
-- Constraints for table `privileges`
-- 
ALTER TABLE `privileges`
  ADD CONSTRAINT `privileges_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `prog_photos`
-- 
ALTER TABLE `prog_photos`
  ADD CONSTRAINT `prog_photos_ibfk_1` FOREIGN KEY (`phProgIDx`) REFERENCES `program` (`pID`);

-- 
-- Constraints for table `program`
-- 
ALTER TABLE `program`
  ADD CONSTRAINT `fk_id_prog` FOREIGN KEY (`pCategoryIdx`) REFERENCES `prog_category` (`cID`);

-- 
-- Constraints for table `website_photos`
-- 
ALTER TABLE `website_photos`
  ADD CONSTRAINT `website_photos_ibfk_1` FOREIGN KEY (`phSiteIDx`) REFERENCES `website` (`wID`);
