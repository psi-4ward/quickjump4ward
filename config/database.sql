-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_user`
-- 

CREATE TABLE `tl_user` (
  `quickjump4ward_enabled` char(1) NOT NULL default '1',
  `quickjump4ward` blob NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;