/*
SQLyog Community v13.1.5  (64 bit)
MySQL - 5.7.26 : Database - gzczxuxknk
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*Data for the table `acrm_contacts` */

insert  into `acrm_contacts`(`id`,`first_name`,`last_name`,`thumbnail`,`phone1_code`,`phone1`,`phone2_code`,`phone2`,`email`,`skype`,`address`,`created_at`,`updated_at`,`deleted_at`,`company_id`,`city`,`state_region`,`zip_postal_code`,`tax_id`,`group_id`,`country_id`,`delivery_address`,`currency_id`,`language_id`,`ticket_emails`,`name`,`fulladdress`,`email_verified_at`,`password`,`remember_token`,`department_id`,`ticketit_admin`,`ticketit_agent`,`status`,`theme`,`portal_language`,`confirmation_code`,`last_login_from`,`hourly_rate`,`is_user`,`color_theme`,`color_skin`) values 
(1,'Admin','1','1565780140-2019-08-14.png',NULL,'0123454568',NULL,'9876543789','admin@admin.com',NULL,'Madhapur','2019-07-18 10:29:00','2019-10-01 04:10:31',NULL,NULL,'Hyderabad','Telangana',NULL,'GSTIN1234r',1,1,'{\"first_name\":\"Admin\",\"last_name\":\"1\",\"address\":\"Madhapurr\",\"city\":\"Hyderabadr\",\"state_region\":\"Telanganar\",\"zip_postal_code\":\"523002\",\"country_id\":\"1\"}',1,1,1,'Admin 1','Madhapurr\nHyderabadr\nTelanganar','0000-00-00 00:00:00','$2y$10$2tscyXExNXSr/aPKxdimNOHUgHj2uDAegNPsABInBJ4vUDKO69JEy','vKZ2q7PCCism9gUSdPiTy90CqiFVYkrVAKXaskC5iekCedkKUj1xic3fz4LK',NULL,1,1,'Active','default','en',NULL,'183.82.114.32',0,'yes','default','skin-blue');

/*Data for the table `acrm_role_user` */

insert  into `acrm_role_user`(`role_id`,`user_id`) values 
(1,1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
