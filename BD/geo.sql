-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Jeu 29 Janvier 2015 à 14:46
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `geo`
--

-- --------------------------------------------------------

--
-- Structure de la table `object`
--

CREATE TABLE IF NOT EXISTS `object` (
  `idO` int(20) NOT NULL AUTO_INCREMENT,
  `nameO` varchar(20) NOT NULL,
  PRIMARY KEY (`idO`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `oinp`
--

CREATE TABLE IF NOT EXISTS `oinp` (
  `idOP` int(20) NOT NULL AUTO_INCREMENT,
  `idO` int(20) NOT NULL,
  `idP` int(20) NOT NULL,
  `posX` int(20) NOT NULL,
  `posY` int(20) NOT NULL,
  `posZ` int(20) NOT NULL,
  `rotX` int(20) NOT NULL,
  `rotY` int(20) NOT NULL,
  `rotZ` int(20) NOT NULL,
  `sizeX` int(20) NOT NULL,
  `sizeY` int(20) NOT NULL,
  `sizeZ` int(20) NOT NULL,
  PRIMARY KEY (`idOP`),
  KEY `idO` (`idO`),
  KEY `idP` (`idP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `project`
--

CREATE TABLE IF NOT EXISTS `project` (
  `idP` int(20) NOT NULL AUTO_INCREMENT,
  `nameP` varchar(20) NOT NULL,
  `descP` text NOT NULL,
  `urlP` varchar(50) NOT NULL,
  `creatorP` int(20) NOT NULL,
  `privateP` tinyint(1) NOT NULL COMMENT '0 pour public, 1 pour privé',
  PRIMARY KEY (`idP`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `idU` int(20) NOT NULL AUTO_INCREMENT,
  `nameU` varchar(20) NOT NULL,
  `passwordU` varchar(32) NOT NULL,
  PRIMARY KEY (`idU`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `oinp`
--
ALTER TABLE `oinp`
  ADD CONSTRAINT `objectinproject` FOREIGN KEY (`idO`) REFERENCES `object` (`idO`),
  ADD CONSTRAINT `projectofobject` FOREIGN KEY (`idP`) REFERENCES `project` (`idP`);

--
-- Contraintes pour la table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `posseder` FOREIGN KEY (`idP`) REFERENCES `user` (`idU`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
