/*
Navicat MySQL Data Transfer

Source Server         : xammp_localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : huain

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2026-05-10 07:32:15
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of banners
-- ----------------------------
INSERT INTO `banners` VALUES ('4', '', '', '', '', '20260509010355_0cf2f26b0119.webp', '', '0', '1', '2026-05-09 01:03:55');
INSERT INTO `banners` VALUES ('5', '', '', '', '', '20260509010403_a28d6eae8eff.webp', '', '0', '1', '2026-05-09 01:04:03');
INSERT INTO `banners` VALUES ('6', '', '', '', '', '20260509010409_459869b109b9.webp', '', '0', '1', '2026-05-09 01:04:09');
INSERT INTO `banners` VALUES ('7', '', '', '', '', '20260509010413_39369acb716d.webp', '', '0', '1', '2026-05-09 01:04:13');
INSERT INTO `banners` VALUES ('8', '', '', '', '', '20260509010422_e15bc0a87dc6.webp', '', '0', '1', '2026-05-09 01:04:22');

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
INSERT INTO `contact_information` VALUES ('1', 'HUAIN Thailand', '', '+66 2 000 0000', 'sales@huain-th.com', '@huainth', '', '', '');

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
INSERT INTO `news` VALUES ('1', 'โครงการห้องประชุมองค์กร กรุงเทพฯ', 'Bangkok Enterprise Conference Room', 'bangkok-enterprise-conference-room', 'ติดตั้งระบบควบคุมสัญญาณ HDMI Matrix และระบบเสียงครบวงจร', 'Installed complete HDMI matrix and integrated audio visual workflow.', '', '', '', 'Bangkok Enterprise Conference Room', 'HUAIN project showcase in Thailand.', '1', '2026-05-08 14:33:25');
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
INSERT INTO `products` VALUES ('1', '1', 'HDMI Matrix', 'HDMI Matrix', 'hdmi-matrix', 'ระบบสวิตช์สัญญาณ HDMI สำหรับงานประชุมและงานองค์กร', 'HDMI Matrix Seamless Switching 4×4 Channels 8×8 Channels 16×16 Channels HY-HD044 HY-HD088 HY-HD1616', 'Adopting programmable logic display circuit, input and output signals can be switched interactively at will;\r\n\r\nSupport 4/8/16-channel high-definition signal source input, 4/8/16-channel high-definition signal source output, using HDMI female interface;\r\n\r\nWith 4 independent audio outputs, using 3.5 analog audio output;\r\n\r\nInput and output support HDMI1.4, the highest resolution is 3840x2160@30Hz;\r\n\r\nEquipped with 2.0\" LCD display, support real-time display of information status;\r\n\r\nSupport audio embedding and de-embedding; support changing output resolution; support power-off memory function; support continuous hot plugging of HDMI interface;\r\n\r\nWith 1 RS232 input, 1 RS232 output, 1 TCP/IP; 1 power switch;\r\n\r\nSupport APP, IR, RS232, TCP/IP control, key switching control;\r\n\r\nUnique blue light key display; support EDID management;\r\n\r\nInput and output color space supports RGB4:4:4, YCbCr4:4:4, YCbCr4:2:2, etc.\r\n\r\nแปลให้ที', 'Adopting programmable logic display circuit, input and output signals can be switched interactively at will;\r\n\r\nSupport 4/8/16-channel high-definition signal source input, 4/8/16-channel high-definition signal source output, using HDMI female interface;\r\n\r\nWith 4 independent audio outputs, using 3.5 analog audio output;\r\n\r\nInput and output support HDMI1.4, the highest resolution is 3840x2160@30Hz;\r\n\r\nEquipped with 2.0\" LCD display, support real-time display of information status;\r\n\r\nSupport audio embedding and de-embedding; support changing output resolution; support power-off memory function; support continuous hot plugging of HDMI interface;\r\n\r\nWith 1 RS232 input, 1 RS232 output, 1 TCP/IP; 1 power switch;\r\n\r\nSupport APP, IR, RS232, TCP/IP control, key switching control;\r\n\r\nUnique blue light key display; support EDID management;\r\n\r\nInput and output color space supports RGB4:4:4, YCbCr4:4:4, YCbCr4:2:2, etc.', 'Support 4/8/16-channel high-definition signal source input, 4/8/16-channel high-definition signal source output, using HDMI female interface', 'Support 4/8/16-channel high-definition signal source input, 4/8/16-channel high-definition signal source output, using HDMI female interface', '98000.00', '20260509173517_c474b90f7bc9.webp', 'HDMIMatrixcatalog.pdf', '20260509174716_9564e6014e24.pdf', '1', '1', 'HDMI Matrix | HUAIN Thailand', 'Professional HDMI Matrix catalog and product details.', '2026-05-08 14:33:25');
INSERT INTO `products` VALUES ('2', '4', 'HUAIN Power Squeezer', 'HUAIN Power Squeezer', 'huain-power-squeezer', 'ระบบจัดการพลังงานและสัญญาณอัจฉริยะ', 'Intelligent power and signal optimization for critical AV racks.', 'ฟังก์ชันและคุณสมบัติ\r\nรองรับเอาต์พุตจ่ายไฟกำลังสูงแบบอิสระ 8 ช่อง โดยรองรับกำลังไฟสูงสุด 2500W ต่อช่อง\r\nมาพร้อมหน้าจอ LCD สี ขนาด 2.0 นิ้ว รองรับการแสดงผลแรงดันไฟฟ้า วันที่ เวลา และสถานะการสลับช่องสัญญาณแบบเรียลไทม์\r\nรองรับฟังก์ชันตั้งเวลาเปิด-ปิด ด้วยชิปนาฬิกาในตัว สามารถตั้งค่าตามวันและเวลาได้โดยไม่ต้องควบคุมด้วยตนเอง ช่วยให้การจัดการอุปกรณ์สะดวกยิ่งขึ้น\r\nรองรับเอาต์พุต 8 ช่อง พร้อมตั้งค่าหน่วงเวลาแต่ละช่องได้อย่างอิสระ (0–999 วินาที)\r\nรองรับการบันทึกและเรียกคืนชุดคำสั่งการทำงาน (Scene) ได้ 10 ชุด ช่วยให้จัดการระบบได้ง่ายและสะดวก\r\nมีระบบตรวจจับแรงดันไฟต่ำและแรงดันไฟเกิน พร้อมระบบแจ้งเตือน เพื่อป้องกันอุปกรณ์อย่างมีประสิทธิภาพ\r\nรองรับการเชื่อมต่อและควบคุมอุปกรณ์หลายตัวร่วมกัน (Cascading) รวมถึงรองรับระบบควบคุมกลางภายนอก\r\nรองรับการควบคุมจากศูนย์กลางระยะไกล โดยแต่ละอุปกรณ์สามารถกำหนดและตรวจสอบ Device ID ได้\r\nรองรับฟังก์ชันล็อกแผงควบคุม (Panel Lock)\r\nรองรับการควบคุมทั้งแบบ Manual, Central Control และผ่านซอฟต์แวร์คอมพิวเตอร์ พร้อมสั่งเปิด/ปิดแต่ละช่องแบบลำดับได้ด้วยปุ่มเดียว\r\nใช้เต้ารับไฟฟ้าแบบมัลติฟังก์ชัน รองรับปลั๊กไฟหลายมาตรฐาน ทั้งแบบสากล อเมริกา และยุโรป', 'Functions and Parameters\r\nFeatures 8 independent high-power controlled power outputs, with a maximum power of 2500W per channel;\r\nEquipped with a 2.0-inch color LCD display, supporting real-time display of current voltage, date and time, and channel switch status;\r\nFeatures a timer switch function with a built-in clock chip, allowing for setting based on date and time without manual operation, simplifying equipment management;\r\nSupports 8 channel outputs, with freely configurable delay times for each channel (0–999 seconds);\r\nFeatures 10 sets of equipment switch scene data storage and retrieval, making scene management simple and convenient;\r\nIncludes undervoltage and overvoltage detection and alarm functions, providing reliable protection for the equipment;\r\nSupports cascading control of multiple devices and external central control equipment;\r\nEnables remote centralized control, with each device having its own device ID detection and setting;\r\nSupports panel lock;\r\nFeatures simultaneous manual, central control, or computer software control, with one-button switch on/off control of channels to achieve sequential operation;\r\nEmploys a multi-functional power socket, compatible with various power plugs including international, American, and European standards;', 'ข้อมูลทางเทคนิค\r\nแหล่งจ่ายไฟ: AC100V~240V ความถี่ 50/60Hz\r\nหน้าจอแสดงผล: จอ LCD สี ขนาด 2.0 นิ้ว\r\nจำนวนรีเลย์แบบ Normally Open (NO): 2 ชุด\r\nกำลังโหลดต่อช่อง: 2500 วัตต์\r\nวิธีการเชื่อมต่อพ่วง (Cascade): ผ่าน Terminal Block\r\nไฟแสดงสถานะ: ไฟแสดงสถานะพลังงานแยกอิสระ\r\nพอร์ตควบคุมกลาง: RS-232 แบบ Female Port\r\nสี: ดำ\r\nขนาด: 483 × 204 × 45 มม.\r\nรูปแบบการติดตั้ง: รองรับการติดตั้งบนตู้ Rack มาตรฐานขนาด 19 นิ้ว', 'Technical Parameters\r\nOperating Power Supply: AC100V~240V 50/60Hz\r\nDisplay: 2.0-inch color LCD display\r\nNumber of Normally Open Relays: 2\r\nSingle-Channel Load: 2500W\r\nCascading Method: Terminal Block\r\nIndicator Lights: Independent Power Indicator\r\nCentral Control Interface: RS-232 Female Port\r\nColor: Black\r\nDimensions: 483 × 204 × 45 mm\r\nInstallation Method: Suitable for 19-inch standard rack mounting', '125000.00', '20260509181927_2df32300839a.jpg', '', '20260509181645_e57197c025a4.pdf', '1', '1', 'Power Squeezer | HUAIN Thailand', 'Power optimization product for AV infrastructure.', '2026-05-08 14:33:25');
INSERT INTO `products` VALUES ('4', '3', 'HDMI Switcher', 'HDMI Switcher', 'hdmi-extender', 'รองรับสัญญาณภาพความละเอียดสูงแบบอินพุต 4 / 8 / 16 ช่อง และเอาต์พุต 4 / 8 / 16 ช่อง ผ่านพอร์ต HDMI แบบ Female', 'HUAIN 3 in 1 out / 5 in 1 out HDMI Switcher HY-HDMI0301 HY-HDMI0501', '', '', 'Up to 100m extension, low latency transport.', 'Up to 100m extension, low latency transport.', '0.00', '20260509183542_bcdce1f95bd8.webp', '', '', '1', '1', 'HDMI Extender | HUAIN Thailand', 'Reliable HDMI extension solutions.', '2026-05-08 14:33:25');

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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of product_images
-- ----------------------------
INSERT INTO `product_images` VALUES ('1', '1', '20260509173517_afe5ea3e6f13.webp', '1');
INSERT INTO `product_images` VALUES ('2', '1', '20260509173517_0e6a08c2852b.webp', '2');
INSERT INTO `product_images` VALUES ('3', '1', '20260509174718_ce23d9678794.webp', '3');
INSERT INTO `product_images` VALUES ('4', '2', '20260509181645_35afcb0ce857.jpg', '1');
INSERT INTO `product_images` VALUES ('5', '2', '20260509181645_3b190cc8fb30.jpg', '2');
INSERT INTO `product_images` VALUES ('6', '4', '20260509183554_d66eb2855b68.webp', '1');
INSERT INTO `product_images` VALUES ('7', '4', '20260509183554_6c2a74ea16d9.webp', '2');
