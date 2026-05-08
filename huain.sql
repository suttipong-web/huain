/*
Navicat MySQL Data Transfer

Source Server         : xampLocalhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : huain

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2026-05-08 16:23:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of admins
-- ----------------------------
INSERT INTO `admins` VALUES ('1', 'admin', '$2y$10$aHCL3Sr0Fdc3QyIFWBETROtSoerJD6/7g15GKrxN25Dsx7AHguLC.', 'HUAIN Administrator', 'admin', '1', '2026-05-08 14:33:25');

-- ----------------------------
-- Table structure for banners
-- ----------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_en` varchar(255) DEFAULT NULL,
  `title_th` varchar(255) DEFAULT NULL,
  `subtitle_en` text DEFAULT NULL,
  `subtitle_th` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of banners
-- ----------------------------
INSERT INTO `banners` VALUES ('1', 'Professional AV Solutions', 'โซลูชัน AV ระดับมืออาชีพ', 'High reliability systems for enterprise projects in Thailand.', 'ระบบที่มีความเสถียรสูงสำหรับงานองค์กรในประเทศไทย', '', 'products.php', '1', '1', '2026-05-08 14:33:25');
INSERT INTO `banners` VALUES ('2', 'HDMI Matrix Flagship Series', 'ซีรีส์ HDMI Matrix รุ่นหลัก', 'Scalable matrix switching for mission critical spaces.', 'รองรับการขยายระบบสำหรับพื้นที่สำคัญ', '', 'product.php?slug=hdmi-matrix', '2', '1', '2026-05-08 14:33:25');
INSERT INTO `banners` VALUES ('3', 'HUAIN Power Squeezer', 'HUAIN Power Squeezer', 'Smart power and signal optimization platform.', 'แพลตฟอร์มจัดการพลังงานและสัญญาณอัจฉริยะ', '', 'product.php?slug=huain-power-squeezer', '3', '1', '2026-05-08 14:33:25');

-- ----------------------------
-- Table structure for contact_information
-- ----------------------------
DROP TABLE IF EXISTS `contact_information`;
CREATE TABLE `contact_information` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `line_id` varchar(100) DEFAULT NULL,
  `google_map` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of contact_information
-- ----------------------------
INSERT INTO `contact_information` VALUES ('1', 'HUAIN Thailand', 'Bangkok, Thailand', '+66 2 000 0000', 'sales@huain-th.com', '@huainth', '', 'https://facebook.com', 'https://youtube.com');

-- ----------------------------
-- Table structure for contact_messages
-- ----------------------------
DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of contact_messages
-- ----------------------------

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title_th` varchar(255) DEFAULT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `short_desc_th` text DEFAULT NULL,
  `short_desc_en` text DEFAULT NULL,
  `description_th` longtext DEFAULT NULL,
  `description_en` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of news
-- ----------------------------
INSERT INTO `news` VALUES ('1', 'โครงการห้องประชุมองค์กร กรุงเทพฯ', 'Bangkok Enterprise Conference Room', 'bangkok-enterprise-conference-room', 'ติดตั้งระบบควบคุมสัญญาณ HDMI Matrix และระบบเสียงครบวงจร', 'Installed complete HDMI matrix and integrated audio visual workflow.', 'โครงการออกแบบและติดตั้งระบบ AV สำหรับห้องประชุมหลักขององค์กรในกรุงเทพฯ พร้อมระบบควบคุมส่วนกลาง.', 'Designed and deployed complete AV solution for the main conference floor with centralized control.', '', 'Bangkok Enterprise Conference Room', 'HUAIN project showcase in Thailand.', '1', '2026-05-08 14:33:25');
INSERT INTO `news` VALUES ('2', 'โครงการ Video Wall โรงแรม', 'Hotel Video Wall Deployment', 'hotel-video-wall-deployment', 'ยกระดับพื้นที่ lobby ด้วย video wall ความละเอียดสูง', 'Premium high-resolution video wall for hotel lobby engagement.', 'ติดตั้งระบบ video wall พร้อม player และ scheduling สำหรับงาน hospitality.', 'Installed a full video wall stack with content scheduling for hospitality operation.', '', 'Hotel Video Wall Deployment', 'Hotel project and deployment result.', '1', '2026-05-08 14:33:25');
INSERT INTO `news` VALUES ('3', 'โครงการ Smart Classroom', 'Smart Classroom Upgrade', 'smart-classroom-upgrade', 'ระบบการเรียนรู้แบบ interactive สำหรับสถาบันการศึกษา', 'Interactive learning AV environment for education institutions.', 'ติดตั้งระบบแสดงผลและจัดการสัญญาณในห้องเรียนอัจฉริยะเพื่อรองรับการเรียนการสอนรูปแบบใหม่.', 'Installed display and signal management systems for modern smart learning classrooms.', '', 'Smart Classroom Upgrade', 'Education AV case study by HUAIN Thailand.', '1', '2026-05-08 14:33:25');

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name_th` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `short_desc_th` text DEFAULT NULL,
  `short_desc_en` text DEFAULT NULL,
  `description_th` longtext DEFAULT NULL,
  `description_en` longtext DEFAULT NULL,
  `specification_th` longtext DEFAULT NULL,
  `specification_en` longtext DEFAULT NULL,
  `price` decimal(12,2) DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `pdf_th` varchar(255) DEFAULT NULL,
  `pdf_en` varchar(255) DEFAULT NULL,
  `featured` tinyint(4) DEFAULT 0,
  `status` tinyint(4) DEFAULT 1,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of products
-- ----------------------------
INSERT INTO `products` VALUES ('1', '1', 'HDMI Matrix', 'HDMI Matrix', 'hdmi-matrix', 'ระบบสวิตช์สัญญาณ HDMI สำหรับงานประชุมและงานองค์กร', 'Enterprise HDMI matrix switching platform for meetings and control environments.', 'รองรับการจัดการสัญญาณหลายอินพุตและหลายเอาต์พุต พร้อมการควบคุมผ่าน LAN และ RS232.', 'Supports multi-input multi-output signal routing with LAN and RS232 control integration.', '4K60, HDCP compliant, EDID management, IR pass-through.', '4K60, HDCP compliant, EDID management, IR pass-through.', '98000.00', '', 'HDMIMatrixcatalog.pdf', 'HDMIMatrixcatalog.pdf', '1', '1', 'HDMI Matrix | HUAIN Thailand', 'Professional HDMI Matrix catalog and product details.', '2026-05-08 14:33:25');
INSERT INTO `products` VALUES ('2', '4', 'HUAIN Power Squeezer', 'HUAIN Power Squeezer', 'huain-power-squeezer', 'ระบบจัดการพลังงานและสัญญาณอัจฉริยะ', 'Intelligent power and signal optimization for critical AV racks.', 'ออกแบบเพื่อช่วยลดการใช้พลังงานและเสริมความเสถียรของระบบ AV.', 'Designed to reduce energy consumption and improve AV infrastructure stability.', 'Remote scheduling, surge protection, power analytics.', 'Remote scheduling, surge protection, power analytics.', '125000.00', '', '', '', '1', '1', 'Power Squeezer | HUAIN Thailand', 'Power optimization product for AV infrastructure.', '2026-05-08 14:33:25');
INSERT INTO `products` VALUES ('3', '2', 'AV Over IP', 'AV Over IP', 'av-over-ip', 'ระบบกระจายสัญญาณภาพและเสียงผ่านเครือข่าย', 'Scalable AV distribution over managed network infrastructure.', 'ขยายระบบได้ง่ายและรองรับงานหลายจุดแสดงผล.', 'Highly scalable architecture for enterprise multi-display projects.', '1Gb/10Gb support, multicast-ready, central management.', '1Gb/10Gb support, multicast-ready, central management.', '145000.00', '', '', '', '1', '1', 'AV Over IP | HUAIN Thailand', 'Enterprise AV over IP product family.', '2026-05-08 14:33:25');
INSERT INTO `products` VALUES ('4', '3', 'HDMI Extender', 'HDMI Extender', 'hdmi-extender', 'ส่งสัญญาณ HDMI ระยะไกลแบบเสถียร', 'Long-distance HDMI signal extension with stable quality.', 'เหมาะสำหรับห้องประชุมและงานติดตั้งที่ต้องเดินสายไกล.', 'Ideal for meeting room and long-cable AV installation scenarios.', 'Up to 100m extension, low latency transport.', 'Up to 100m extension, low latency transport.', '39000.00', '', '', '', '0', '1', 'HDMI Extender | HUAIN Thailand', 'Reliable HDMI extension solutions.', '2026-05-08 14:33:25');
INSERT INTO `products` VALUES ('5', '4', 'Video Wall Controller', 'Video Wall Controller', 'video-wall-controller', 'ควบคุมผนังภาพหลายจอสำหรับศูนย์ควบคุม', 'Multi-display video wall control system for command center operation.', 'รองรับการจัด layout และ preset สำหรับงานเฝ้าระวังแบบเรียลไทม์.', 'Supports layout presets and real-time monitoring workflows.', 'Flexible layouts, low-latency output, failover ready.', 'Flexible layouts, low-latency output, failover ready.', '210000.00', '', '', '', '0', '1', 'Video Wall Controller | HUAIN Thailand', 'Video wall management platform.', '2026-05-08 14:33:25');
INSERT INTO `products` VALUES ('6', '1', 'HDMI Splitter', 'HDMI Splitter', 'hdmi-splitter', 'กระจายสัญญาณ HDMI หลายจอ', 'Professional HDMI signal distribution to multiple displays.', 'เหมาะสำหรับงานโชว์รูม ห้องประชุม และ signage.', 'Ideal for showrooms, meeting spaces, and digital signage systems.', '1x4 / 1x8 output options, signal equalization, reliable sync.', '1x4 / 1x8 output options, signal equalization, reliable sync.', '22000.00', '', '', '', '0', '1', 'HDMI Splitter | HUAIN Thailand', 'Signal distribution products from HUAIN.', '2026-05-08 14:33:25');

-- ----------------------------
-- Table structure for product_categories
-- ----------------------------
DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_th` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of product_categories
-- ----------------------------
INSERT INTO `product_categories` VALUES ('1', 'แมทริกซ์และสวิตช์', 'Matrix and Switchers', 'matrix-and-switchers', '1');
INSERT INTO `product_categories` VALUES ('2', 'AV over IP', 'AV over IP', 'av-over-ip', '1');
INSERT INTO `product_categories` VALUES ('3', 'Signal Extension', 'Signal Extension', 'signal-extension', '1');
INSERT INTO `product_categories` VALUES ('4', 'Control Systems', 'Control Systems', 'control-systems', '1');

-- ----------------------------
-- Table structure for product_images
-- ----------------------------
DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of product_images
-- ----------------------------
