--
-- Table structure for table `secret_table`
--

DROP TABLE IF EXISTS `secret_table`;
CREATE TABLE `secret_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL,
  `line` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `document` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
