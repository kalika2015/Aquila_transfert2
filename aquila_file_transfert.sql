-- --------------------------------------------------------
-- Hôte :                        localhost
-- Version du serveur:           5.7.19 - MySQL Community Server (GPL)
-- SE du serveur:                Win64
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Export de la structure de la table aquila_file_transfert. emetteurs
CREATE TABLE IF NOT EXISTS `emetteurs` (
  `id_emetteur` int(11) NOT NULL AUTO_INCREMENT,
  `nom_emetteur` varchar(255) NOT NULL,
  `email_emetteur` varchar(255) NOT NULL,
  `file_message` text NOT NULL,
  `random_value` int(25) NOT NULL,
  PRIMARY KEY (`id_emetteur`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;


-- Export de la structure de la table aquila_file_transfert. fichiers
CREATE TABLE IF NOT EXISTS `fichiers` (
  `id_fichier` int(11) NOT NULL AUTO_INCREMENT,
  `file_url` varchar(255) NOT NULL,
  `date_transfert` datetime NOT NULL,
  `id_emetteur` int(11) NOT NULL,
  PRIMARY KEY (`id_fichier`),
  KEY `FK_emetteur_file` (`id_emetteur`),
  CONSTRAINT `FK_emetteur_file` FOREIGN KEY (`id_emetteur`) REFERENCES `emetteurs` (`id_emetteur`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;



-- Export de la structure de la table aquila_file_transfert. recepteurs
CREATE TABLE IF NOT EXISTS `recepteurs` (
  `id_recepteur` int(11) NOT NULL AUTO_INCREMENT,
  `email_recepteur` varchar(255) NOT NULL,
  `id_emetteur` int(11) NOT NULL,
  PRIMARY KEY (`id_recepteur`),
  KEY `FK_emetteur_recepteur` (`id_emetteur`),
  CONSTRAINT `FK_emetteur_recepteur` FOREIGN KEY (`id_emetteur`) REFERENCES `emetteurs` (`id_emetteur`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;


-- Export de la structure de la table aquila_file_transfert. fichier_recepteur
CREATE TABLE IF NOT EXISTS `fichier_recepteur` (
  `id_fichier` int(11) NOT NULL,
  `id_recepteur` int(11) NOT NULL,
  PRIMARY KEY (`id_fichier`,`id_recepteur`),
  KEY `FK_recepteur` (`id_recepteur`),
  CONSTRAINT `FK_fichier` FOREIGN KEY (`id_fichier`) REFERENCES `fichiers` (`id_fichier`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_recepteur` FOREIGN KEY (`id_recepteur`) REFERENCES `recepteurs` (`id_recepteur`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
