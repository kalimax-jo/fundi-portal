-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2025 at 11:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fundi_rwanda`
--

-- --------------------------------------------------------

--
-- Table structure for table `business_partners`
--

CREATE TABLE `business_partners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `subdomain` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `type` enum('bank','insurance','microfinance','mortgage','investment') NOT NULL,
  `tier` enum('bronze','silver','gold','platinum') NOT NULL DEFAULT 'bronze',
  `license_number` varchar(100) DEFAULT NULL,
  `registration_number` varchar(100) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Rwanda',
  `logo` varchar(255) DEFAULT NULL,
  `partnership_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `partnership_status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `billing_type` enum('monthly','per_inspection','custom') NOT NULL DEFAULT 'monthly',
  `billing_cycle` enum('monthly','quarterly','annually') NOT NULL DEFAULT 'monthly',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_partners`
--

INSERT INTO `business_partners` (`id`, `name`, `subdomain`, `email`, `phone`, `website`, `type`, `tier`, `license_number`, `registration_number`, `contact_person`, `contact_email`, `contact_phone`, `address`, `city`, `country`, `logo`, `partnership_start_date`, `contract_end_date`, `partnership_status`, `billing_type`, `billing_cycle`, `discount_percentage`, `notes`, `credit_limit`, `created_at`, `updated_at`) VALUES
(2, 'Bank of africa', 'bannkofafrica', 'bankofafrica@fundi.com', '078123445666', 'https://www.boarwanda.com/', 'bank', 'silver', NULL, '10929903', 'kamali B', 'kamalie@fundi.com', '0', 'KKakkaaaa1221', 'Kigali', 'Rwanda', NULL, '2025-06-27', NULL, 'active', 'monthly', 'monthly', 0.00, 'Kigali', NULL, '2025-06-27 08:31:32', '2025-06-27 08:31:32'),
(3, 'Bank of Kigali', 'bankofkigali', 'info@bankofkigali.com', '+250788123456', 'https://bk.rw/', 'bank', 'gold', NULL, '1001001', 'Bank Admin', 'admin@bankofkigali.com', '+250788123456', '123 Kigali St', 'Kigali', 'Rwanda', NULL, '2025-07-04', '2026-01-26', 'active', 'monthly', 'monthly', 0.00, NULL, NULL, '2025-07-04 10:24:05', '2025-07-06 21:30:07'),
(4, 'Demo Partner', 'demopartner', 'demo@partner.com', '123456789', NULL, 'bank', 'gold', NULL, NULL, 'Demo Admin', 'admin@demopartner.com', '123456789', '123 Demo St', 'Demo City', 'DemoLand', NULL, '2025-07-05', NULL, 'active', 'monthly', 'monthly', 0.00, NULL, NULL, '2025-07-05 14:15:33', '2025-07-05 14:15:33');

-- --------------------------------------------------------

--
-- Table structure for table `business_partner_users`
--

CREATE TABLE `business_partner_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_partner_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `access_level` enum('admin','user','viewer') NOT NULL DEFAULT 'user',
  `is_primary_contact` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `added_by` bigint(20) UNSIGNED DEFAULT NULL,
  `added_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_partner_users`
--

INSERT INTO `business_partner_users` (`id`, `business_partner_id`, `user_id`, `position`, `department`, `access_level`, `is_primary_contact`, `created_at`, `updated_at`, `added_by`, `added_at`) VALUES
(2, 2, 8, NULL, NULL, 'admin', 1, '2025-06-27 08:31:32', '2025-06-27 08:31:32', 1, '2025-06-27 08:31:32'),
(3, 3, 9, NULL, NULL, 'admin', 1, '2025-07-04 10:24:06', '2025-07-04 10:24:06', 1, '2025-07-04 10:24:06'),
(4, 4, 10, NULL, NULL, 'admin', 1, '2025-07-05 14:17:35', '2025-07-05 14:17:35', NULL, NULL),
(5, 4, 11, NULL, NULL, 'admin', 0, '2025-07-05 16:21:23', '2025-07-05 16:21:23', NULL, NULL),
(6, 4, 12, NULL, NULL, 'viewer', 0, '2025-07-05 17:08:05', '2025-07-05 17:08:05', NULL, NULL),
(7, 4, 13, NULL, NULL, 'user', 0, '2025-07-05 17:08:51', '2025-07-05 17:08:51', NULL, NULL),
(13, 3, 19, NULL, NULL, 'user', 0, '2025-07-06 16:47:44', '2025-07-06 16:47:44', NULL, NULL),
(14, 3, 21, NULL, NULL, 'admin', 1, '2025-07-06 21:30:08', '2025-07-06 21:30:08', NULL, NULL),
(15, 3, 22, NULL, NULL, 'user', 0, '2025-07-06 21:58:06', '2025-07-06 21:58:06', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_packages`
--

CREATE TABLE `inspection_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'RWF',
  `duration_hours` int(11) NOT NULL DEFAULT 4,
  `is_custom_quote` tinyint(1) NOT NULL DEFAULT 0,
  `target_client_type` enum('individual','business','both') NOT NULL DEFAULT 'both',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inspection_packages`
--

INSERT INTO `inspection_packages` (`id`, `name`, `display_name`, `description`, `price`, `currency`, `duration_hours`, `is_custom_quote`, `target_client_type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'A_CHECK', 'A-Check Package', 'Exterior, interior, plumbing, electrical, air, and fire safety. Recommended before and after rental.', 200000.00, 'RWF', 3, 0, 'both', 1, '2025-06-25 06:59:58', '2025-06-25 06:59:58'),
(2, 'B_CHECK', 'B-Check Package', 'A-Check + foundation cracks, garden trees, fence, and flooding risks. Recommended for buy or sell.', 400000.00, 'RWF', 5, 0, 'both', 1, '2025-06-25 06:59:58', '2025-06-25 06:59:58'),
(3, 'C_CHECK', 'C-Check Package', 'Comprehensive inspection (A+B), environmental hazards, and septic tank. Recommended every 5 years.', 0.00, 'RWF', 8, 1, 'both', 1, '2025-06-25 06:59:58', '2025-06-25 06:59:58');

-- --------------------------------------------------------

--
-- Table structure for table `inspection_reports`
--

CREATE TABLE `inspection_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inspection_request_id` bigint(20) UNSIGNED NOT NULL,
  `inspector_id` bigint(20) UNSIGNED NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `status` enum('in_progress','completed') NOT NULL DEFAULT 'in_progress',
  `progress` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inspection_reports`
--

INSERT INTO `inspection_reports` (`id`, `inspection_request_id`, `inspector_id`, `data`, `status`, `progress`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '{\"general_comments\":\"cool\"}', 'completed', 100, '2025-07-06 17:54:01', '2025-07-06 17:41:08', '2025-07-06 17:54:01'),
(2, 3, 1, '{\"service_notes_7\":\"add tanks to the water\",\"service_notes_1\":\"okay\",\"service_notes_8\":\"okay\",\"service_notes_2\":\"okay\",\"service_notes_9\":\"okay\",\"service_notes_3\":\"tha\",\"service_notes_6\":\"iiis\",\"general_comments\":\"all is well\",\"service_photo_7\":\"reports\\/2\\/IAtT6VcTaZQE3Fc7h32JBwIRpF5AoMP1hry8YDgt.png\",\"service_photo_1\":\"reports\\/2\\/ScNAhokSLLMZEQZld6ks1SbLIjBF5VikQdzavXvE.png\",\"service_photo_8\":\"reports\\/2\\/8P6cxBTsvsEMQXWHb0yAQyXUeOtFgjeIy6o6BKVV.png\",\"service_photo_2\":\"reports\\/2\\/3mxoIAnDdFXbhCrSoHO0QQMPfSCc2aUXR1QoGYxC.png\",\"service_photo_9\":\"reports\\/2\\/AIbT6H2wC7xZssjiB6WwhX9aHPCtrHOaXbMzE1Tj.jpg\",\"service_photo_3\":\"reports\\/2\\/dNE7KGx8OxlZbSdJaJ9xVetIYeKQZShW7HOtdadR.png\",\"service_photo_6\":\"reports\\/2\\/30qyiGTRpraWSl29XDTt3BANE9RaJe123IszXYZs.png\"}', 'completed', 100, '2025-07-06 22:03:33', '2025-07-06 22:01:35', '2025-07-06 22:03:33');

-- --------------------------------------------------------

--
-- Table structure for table `inspection_requests`
--

CREATE TABLE `inspection_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(32) NOT NULL,
  `requester_type` enum('individual','business_partner') NOT NULL,
  `requester_user_id` bigint(20) UNSIGNED NOT NULL,
  `business_partner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `property_id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `purpose` enum('rental','sale','purchase','loan_collateral','insurance','maintenance','other') NOT NULL,
  `urgency` enum('normal','urgent','emergency') NOT NULL DEFAULT 'normal',
  `preferred_date` date DEFAULT NULL,
  `preferred_time_slot` enum('morning','afternoon','evening','flexible') NOT NULL DEFAULT 'flexible',
  `special_instructions` text DEFAULT NULL,
  `loan_amount` decimal(15,2) DEFAULT NULL,
  `loan_reference` varchar(100) DEFAULT NULL,
  `applicant_name` varchar(255) DEFAULT NULL,
  `applicant_phone` varchar(20) DEFAULT NULL,
  `status` enum('pending','assigned','in_progress','completed','cancelled','on_hold') NOT NULL DEFAULT 'pending',
  `assigned_inspector_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `scheduled_time` time DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `total_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','partial','paid','refunded') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `institutional_partner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inspection_requests`
--

INSERT INTO `inspection_requests` (`id`, `request_number`, `requester_type`, `requester_user_id`, `business_partner_id`, `property_id`, `package_id`, `purpose`, `urgency`, `preferred_date`, `preferred_time_slot`, `special_instructions`, `loan_amount`, `loan_reference`, `applicant_name`, `applicant_phone`, `status`, `assigned_inspector_id`, `assigned_by`, `assigned_at`, `scheduled_date`, `scheduled_time`, `started_at`, `completed_at`, `total_cost`, `payment_status`, `created_at`, `updated_at`, `institutional_partner_id`, `client_id`) VALUES
(2, 'INSP-20250706-686AC4D0805DA', 'business_partner', 9, 3, 2, 2, 'rental', 'normal', '2025-07-09', 'flexible', NULL, NULL, NULL, NULL, NULL, 'completed', 1, 2, '2025-07-06 17:27:53', '2025-07-08', '01:01:00', '2025-07-06 17:41:08', '2025-07-06 17:54:01', 0.00, 'pending', '2025-07-06 16:47:44', '2025-07-06 17:54:01', NULL, 19),
(3, 'INSP-20250706-686B0D8E33C59', 'business_partner', 9, 3, 3, 2, 'rental', 'normal', '2025-07-18', 'flexible', NULL, NULL, NULL, NULL, NULL, 'completed', 1, 2, '2025-07-06 22:00:12', '2025-07-11', '16:54:00', '2025-07-06 22:01:34', '2025-07-06 22:03:33', 0.00, 'pending', '2025-07-06 21:58:06', '2025-07-06 22:03:33', NULL, 22),
(4, 'INSP-20250707-686B86EF0D181', 'business_partner', 9, 3, 2, 2, 'rental', 'normal', '2025-07-10', 'flexible', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 'pending', '2025-07-07 06:35:59', '2025-07-07 06:35:59', NULL, 19);

-- --------------------------------------------------------

--
-- Table structure for table `inspection_services`
--

CREATE TABLE `inspection_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('exterior','interior','plumbing','electrical','foundation','environmental','safety') NOT NULL,
  `requires_equipment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`requires_equipment`)),
  `estimated_duration_minutes` int(11) NOT NULL DEFAULT 30,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inspection_services`
--

INSERT INTO `inspection_services` (`id`, `name`, `description`, `category`, `requires_equipment`, `estimated_duration_minutes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Exterior Property Assessment', 'Complete evaluation of building exterior including walls, roof, windows, doors, and structural elements', 'exterior', '[\"digital_camera\",\"measuring_tape\",\"ladder\",\"flashlight\"]', 45, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(2, 'Interior Condition Evaluation', 'Assessment of interior spaces, floors, ceilings, walls, fixtures, and general condition', 'interior', '[\"digital_camera\",\"measuring_tape\",\"flashlight\",\"moisture_meter\"]', 60, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(3, 'Plumbing System Inspection', 'Water supply, drainage systems, fixtures, pipes, and water pressure testing', 'plumbing', '[\"pressure_gauge\",\"flashlight\",\"digital_camera\"]', 30, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(4, 'Electrical Safety Check', 'Electrical systems safety inspection including wiring, outlets, panels, and grounding', 'electrical', '[\"multimeter\",\"digital_camera\",\"flashlight\"]', 30, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(5, 'Air Quality Assessment', 'Indoor air quality evaluation including ventilation, humidity, and potential contaminants', 'environmental', '[\"air_quality_monitor\",\"digital_camera\"]', 20, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(6, 'Fire Safety Evaluation', 'Fire safety systems assessment including exits, extinguishers, smoke detectors', 'safety', '[\"digital_camera\",\"measuring_tape\"]', 25, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(7, 'Foundation Crack Analysis', 'Detailed foundation inspection including crack assessment and structural integrity', 'foundation', '[\"foundation_scanner\",\"measuring_tape\",\"digital_camera\"]', 40, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(8, 'Garden and Trees Assessment', 'Landscape evaluation including tree health, root systems, and potential property risks', 'exterior', '[\"digital_camera\",\"measuring_tape\"]', 30, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(9, 'Fence and Boundary Evaluation', 'Property boundary assessment including fence condition and security features', 'exterior', '[\"digital_camera\",\"measuring_tape\"]', 20, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(10, 'Flooding Risk Analysis', 'Water damage assessment and flooding risk evaluation including drainage systems', 'environmental', '[\"moisture_meter\",\"digital_camera\",\"measuring_tape\"]', 35, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(11, 'Environmental Hazards Assessment', 'Comprehensive evaluation of environmental hazards including asbestos, lead, and toxic materials', 'environmental', '[\"air_quality_monitor\",\"ph_meter\",\"digital_camera\",\"gas_detector\"]', 60, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(12, 'Septic Tank Inspection', 'Septic system evaluation including tank condition, drainage field, and pumping requirements', 'plumbing', '[\"endoscope\",\"digital_camera\",\"gas_detector\"]', 45, 1, '2025-06-25 07:00:00', '2025-06-25 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `inspection_status_history`
--

CREATE TABLE `inspection_status_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inspection_request_id` bigint(20) UNSIGNED NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_by` bigint(20) UNSIGNED NOT NULL,
  `change_reason` text DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inspection_status_history`
--

INSERT INTO `inspection_status_history` (`id`, `inspection_request_id`, `old_status`, `new_status`, `changed_by`, `change_reason`, `changed_at`) VALUES
(2, 2, NULL, 'pending', 9, 'Request created', '2025-07-06 18:47:44'),
(3, 2, 'pending', 'assigned', 2, 'Inspector assigned', '2025-07-06 19:27:53'),
(4, 2, 'assigned', 'in_progress', 20, 'Inspection started', '2025-07-06 19:41:08'),
(5, 2, 'assigned', 'in_progress', 1, 'Inspection started', '2025-07-06 19:41:08'),
(6, 2, 'in_progress', 'completed', 20, 'Inspection completed', '2025-07-06 19:41:26'),
(7, 2, 'in_progress', 'completed', 20, 'Inspection completed', '2025-07-06 19:42:04'),
(8, 2, 'in_progress', 'completed', 20, 'Inspection completed', '2025-07-06 19:54:01'),
(9, 2, 'in_progress', 'completed', 1, 'Inspection completed', '2025-07-06 19:54:01'),
(10, 3, NULL, 'pending', 9, 'Request created', '2025-07-06 23:58:06'),
(11, 3, 'pending', 'assigned', 2, 'Inspector assigned', '2025-07-07 00:00:12'),
(12, 3, 'assigned', 'in_progress', 20, 'Inspection started', '2025-07-07 00:01:34'),
(13, 3, 'assigned', 'in_progress', 1, 'Inspection started', '2025-07-07 00:01:34'),
(14, 3, 'in_progress', 'completed', 20, 'Inspection completed', '2025-07-07 00:03:33'),
(15, 3, 'in_progress', 'completed', 1, 'Inspection completed', '2025-07-07 00:03:33'),
(16, 4, NULL, 'pending', 9, 'Request created', '2025-07-07 08:35:59');

-- --------------------------------------------------------

--
-- Table structure for table `inspectors`
--

CREATE TABLE `inspectors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `inspector_code` varchar(20) NOT NULL,
  `certification_level` enum('basic','advanced','expert') NOT NULL DEFAULT 'basic',
  `specializations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specializations`)),
  `experience_years` int(11) NOT NULL DEFAULT 0,
  `certification_expiry` date DEFAULT NULL,
  `equipment_assigned` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`equipment_assigned`)),
  `availability_status` enum('available','busy','offline') NOT NULL DEFAULT 'available',
  `current_location_lat` decimal(10,8) DEFAULT NULL,
  `current_location_lng` decimal(11,8) DEFAULT NULL,
  `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_inspections` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inspectors`
--

INSERT INTO `inspectors` (`id`, `user_id`, `inspector_code`, `certification_level`, `specializations`, `experience_years`, `certification_expiry`, `equipment_assigned`, `availability_status`, `current_location_lat`, `current_location_lng`, `rating`, `total_inspections`, `created_at`, `updated_at`) VALUES
(1, 20, 'INS3354', 'advanced', '[\"residential\",\"commercial\",\"industrial\",\"electrical\",\"plumbing\",\"structural\",\"hvac\",\"fire_safety\",\"environmental\",\"energy_efficiency\"]', 2, '2025-07-31', '[\"moisture_meter\",\"thermal_camera\",\"electrical_tester\",\"gas_detector\",\"ladder\",\"measuring_tools\",\"flashlight\",\"camera\",\"tablet\",\"safety_gear\"]', 'available', NULL, NULL, 5.00, 2, '2025-07-06 17:13:38', '2025-07-06 22:03:33');

-- --------------------------------------------------------

--
-- Table structure for table `inspector_certifications`
--

CREATE TABLE `inspector_certifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inspector_id` bigint(20) UNSIGNED NOT NULL,
  `certification_name` varchar(255) NOT NULL,
  `issuing_body` varchar(255) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `certificate_file` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `institutional_partners`
--

CREATE TABLE `institutional_partners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `subdomain` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `institution_type` varchar(255) NOT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `primary_color` varchar(255) NOT NULL DEFAULT '#3B82F6',
  `secondary_color` varchar(255) NOT NULL DEFAULT '#1F2937',
  `branding_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`branding_settings`)),
  `partnership_start_date` date NOT NULL,
  `contract_end_date` date DEFAULT NULL,
  `status` enum('active','inactive','suspended','pending') NOT NULL DEFAULT 'pending',
  `billing_type` enum('monthly','quarterly','annually','per_inspection') NOT NULL DEFAULT 'monthly',
  `monthly_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `per_inspection_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `credit_limit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `max_users` int(11) NOT NULL DEFAULT 10,
  `max_inspections_per_month` int(11) DEFAULT NULL,
  `feature_access` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`feature_access`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `institutional_partners`
--

INSERT INTO `institutional_partners` (`id`, `name`, `subdomain`, `domain`, `email`, `phone`, `website`, `institution_type`, `license_number`, `registration_number`, `contact_person`, `contact_email`, `contact_phone`, `address`, `city`, `country`, `logo`, `favicon`, `primary_color`, `secondary_color`, `branding_settings`, `partnership_start_date`, `contract_end_date`, `status`, `billing_type`, `monthly_fee`, `per_inspection_fee`, `discount_percentage`, `credit_limit`, `max_users`, `max_inspections_per_month`, `feature_access`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Bank of Rwanda', 'bpr', NULL, 'info@bpr.rw', '+250788123456', 'https://www.bpr.rw', 'bank', 'BPR001', 'REG001', 'John Doe', 'john.doe@bpr.rw', '+250788123457', 'KN 3 Ave, Kigali', 'Kigali', 'Rwanda', NULL, NULL, '#1e40af', '#1f2937', '{\"show_logo\":true,\"show_favicon\":true,\"welcome_message\":\"Welcome to Bank of Rwanda Portal\",\"footer_text\":\"Powered by Fundi Portal\"}', '2024-12-25', NULL, 'active', 'monthly', 500.00, 25.00, 10.00, 10000.00, 20, 100, '{\"inspection_requests\":true,\"property_management\":true,\"reports\":true,\"analytics\":true,\"user_management\":true,\"billing\":true}', NULL, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(2, 'Rwanda Insurance Company', 'ric', NULL, 'info@ric.rw', '+250788234567', 'https://www.ric.rw', 'insurance', 'RIC001', 'REG002', 'Jane Smith', 'jane.smith@ric.rw', '+250788234568', 'KG 123 St, Kigali', 'Kigali', 'Rwanda', NULL, NULL, '#059669', '#374151', '{\"show_logo\":true,\"show_favicon\":true,\"welcome_message\":\"Welcome to Rwanda Insurance Portal\",\"footer_text\":\"Powered by Fundi Portal\"}', '2025-03-25', NULL, 'active', 'per_inspection', 0.00, 30.00, 5.00, 5000.00, 15, 50, '{\"inspection_requests\":true,\"property_management\":true,\"reports\":true,\"analytics\":true,\"user_management\":true,\"billing\":true}', NULL, '2025-06-25 07:00:01', '2025-06-25 07:00:01'),
(3, 'Kigali Microfinance', 'kmf', NULL, 'info@kmf.rw', '+250788345678', 'https://www.kmf.rw', 'microfinance', 'KMF001', 'REG003', 'Alice Johnson', 'alice.johnson@kmf.rw', '+250788345679', 'KG 456 Ave, Kigali', 'Kigali', 'Rwanda', NULL, NULL, '#dc2626', '#4b5563', '{\"show_logo\":true,\"show_favicon\":true,\"welcome_message\":\"Welcome to Kigali Microfinance Portal\",\"footer_text\":\"Powered by Fundi Portal\"}', '2025-05-25', NULL, 'active', 'monthly', 300.00, 20.00, 15.00, 3000.00, 10, 30, '{\"inspection_requests\":true,\"property_management\":true,\"reports\":true,\"analytics\":false,\"user_management\":true,\"billing\":true}', NULL, '2025-06-25 07:00:01', '2025-06-25 07:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `institutional_partner_users`
--

CREATE TABLE `institutional_partner_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institutional_partner_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `position` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `access_level` enum('admin','manager','user','viewer') NOT NULL DEFAULT 'user',
  `is_primary_contact` tinyint(1) NOT NULL DEFAULT 0,
  `added_by` bigint(20) UNSIGNED DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `institutional_partner_users`
--

INSERT INTO `institutional_partner_users` (`id`, `institutional_partner_id`, `user_id`, `position`, `department`, `access_level`, `is_primary_contact`, `added_by`, `added_at`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'Administrator', 'IT', 'admin', 1, 1, '2025-06-25 07:00:01', '2025-06-25 07:00:01', '2025-06-25 07:00:01'),
(2, 2, 5, 'Administrator', 'IT', 'admin', 1, 1, '2025-06-25 07:00:01', '2025-06-25 07:00:01', '2025-06-25 07:00:01'),
(3, 3, 6, 'Administrator', 'IT', 'admin', 1, 1, '2025-06-25 07:00:01', '2025-06-25 07:00:01', '2025-06-25 07:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_13_135203_create_personal_access_tokens_table', 1),
(5, '2025_06_13_140553_create_complete_fundi_system', 1),
(6, '2025_06_15_090708_add_missing_columns_to_business_partners_table', 1),
(7, '2025_06_15_091420_create_partner_billings_table', 1),
(8, '2025_06_15_125320_fix_business_partner_users_table', 1),
(9, '2025_06_20_144005_create_inspection_reports_table', 1),
(10, '2025_06_24_000000_create_institutional_partners_table', 1),
(11, '2025_06_24_235557_update_inspection_status_history_to_use_names', 1),
(12, '2025_06_25_000710_create_institutional_partner_users_table', 1),
(13, '2025_06_25_000726_add_institutional_partner_id_to_inspection_requests_table', 1),
(14, '2025_06_25_000740_add_institutional_partner_id_to_properties_table', 1),
(15, '2025_06_25_000753_add_institutional_partner_id_to_partner_billings_table', 1),
(16, '2025_06_25_003538_add_created_by_to_users_table', 1),
(17, '2025_06_25_225258_add_subdomain_to_business_partners_table', 2),
(18, '2025_06_26_000000_create_tiers_table', 3),
(19, '2025_07_06_000001_create_partner_tiers_table', 4),
(20, '2025_07_06_000002_create_partner_tier_payments_table', 4),
(21, '2025_07_06_000003_add_pending_to_partner_tiers_status', 5),
(22, '2025_07_06_113813_add_client_national_id_to_properties_table', 6),
(23, '2025_07_06_114010_add_business_partner_id_to_properties_table', 7),
(24, '2025_07_06_123528_add_status_to_properties_table', 8),
(25, '2025_07_06_184343_alter_request_number_length_on_inspection_requests_table', 9),
(26, '2025_07_06_184654_add_client_id_to_inspection_requests_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('email','sms','push','whatsapp') NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `recipient` varchar(255) NOT NULL,
  `status` enum('pending','sent','delivered','failed','bounced') NOT NULL DEFAULT 'pending',
  `delivery_attempts` int(11) NOT NULL DEFAULT 0,
  `last_attempt_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `failure_reason` text DEFAULT NULL,
  `related_type` varchar(100) DEFAULT NULL,
  `related_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

CREATE TABLE `notification_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('email','sms','push','whatsapp') NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text NOT NULL,
  `variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`variables`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_services`
--

CREATE TABLE `package_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `package_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_services`
--

INSERT INTO `package_services` (`id`, `package_id`, `service_id`, `is_mandatory`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 2, 7, 1, 0, NULL, NULL),
(2, 2, 1, 1, 0, NULL, NULL),
(3, 2, 8, 1, 0, NULL, NULL),
(4, 2, 2, 1, 0, NULL, NULL),
(5, 2, 9, 1, 0, NULL, NULL),
(6, 2, 3, 1, 0, NULL, NULL),
(7, 2, 6, 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `partner_billings`
--

CREATE TABLE `partner_billings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_partner_id` bigint(20) UNSIGNED NOT NULL,
  `billing_reference` varchar(100) NOT NULL,
  `billing_period_type` enum('monthly','quarterly','annually','custom') NOT NULL,
  `billing_period_start` date NOT NULL,
  `billing_period_end` date NOT NULL,
  `total_inspections` int(11) NOT NULL DEFAULT 0,
  `base_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT 18.00,
  `final_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(3) NOT NULL DEFAULT 'RWF',
  `status` enum('draft','pending','sent','paid','overdue','cancelled') NOT NULL DEFAULT 'draft',
  `due_date` date DEFAULT NULL,
  `sent_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_reference` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `inspection_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`inspection_details`)),
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `institutional_partner_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partner_tiers`
--

CREATE TABLE `partner_tiers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_partner_id` bigint(20) UNSIGNED NOT NULL,
  `tier_id` bigint(20) UNSIGNED NOT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','active','expired','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `partner_tiers`
--

INSERT INTO `partner_tiers` (`id`, `business_partner_id`, `tier_id`, `started_at`, `expires_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2025-07-06 05:20:42', '2025-08-06 05:20:42', 'pending', '2025-07-06 05:20:42', '2025-07-06 05:20:42'),
(2, 2, 1, '2025-07-06 05:20:48', '2025-08-06 05:20:48', 'pending', '2025-07-06 05:20:48', '2025-07-06 05:20:48'),
(3, 2, 3, '2025-07-06 05:20:52', '2025-08-06 05:20:52', 'pending', '2025-07-06 05:20:52', '2025-07-06 05:20:52'),
(4, 2, 1, '2025-07-06 05:21:53', '2025-08-06 05:21:53', 'pending', '2025-07-06 05:21:53', '2025-07-06 05:21:53'),
(5, 2, 1, '2025-07-06 05:22:46', '2025-08-06 05:22:46', 'pending', '2025-07-06 05:22:46', '2025-07-06 05:22:46'),
(6, 2, 1, '2025-07-06 05:27:19', '2025-08-06 05:27:19', 'pending', '2025-07-06 05:27:19', '2025-07-06 05:27:19'),
(7, 2, 1, '2025-07-06 05:33:17', '2025-08-06 05:33:17', 'pending', '2025-07-06 05:33:17', '2025-07-06 05:33:17'),
(8, 3, 1, '2025-07-06 05:48:00', '2025-08-06 05:48:00', 'pending', '2025-07-06 05:48:00', '2025-07-06 05:48:00'),
(9, 3, 1, '2025-07-06 05:54:04', '2025-08-06 05:54:04', 'pending', '2025-07-06 05:54:04', '2025-07-06 05:54:04'),
(10, 3, 1, '2025-07-06 07:20:28', '2025-08-06 07:20:28', 'pending', '2025-07-06 07:20:28', '2025-07-06 07:20:28'),
(11, 3, 1, '2025-07-06 07:24:30', '2025-08-06 07:24:30', 'active', '2025-07-06 07:23:41', '2025-07-06 07:24:30');

-- --------------------------------------------------------

--
-- Table structure for table `partner_tier_payments`
--

CREATE TABLE `partner_tier_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `partner_tier_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `status` enum('paid','failed','pending') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `partner_tier_payments`
--

INSERT INTO `partner_tier_payments` (`id`, `partner_tier_id`, `amount`, `paid_at`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1000000.00, NULL, NULL, 'pending', '2025-07-06 05:20:42', '2025-07-06 05:20:42'),
(2, 2, 1000000.00, NULL, NULL, 'pending', '2025-07-06 05:20:48', '2025-07-06 05:20:48'),
(3, 3, 2000000.00, NULL, NULL, 'pending', '2025-07-06 05:20:52', '2025-07-06 05:20:52'),
(4, 4, 1000000.00, NULL, 'flutterwave', 'pending', '2025-07-06 05:21:53', '2025-07-06 05:21:54'),
(5, 5, 10000.00, NULL, 'flutterwave', 'pending', '2025-07-06 05:22:46', '2025-07-06 05:22:47'),
(6, 6, 10000.00, NULL, 'flutterwave', 'pending', '2025-07-06 05:27:19', '2025-07-06 05:27:21'),
(7, 7, 10000.00, NULL, 'flutterwave', 'pending', '2025-07-06 05:33:17', '2025-07-06 05:33:19'),
(8, 8, 10000.00, NULL, 'flutterwave', 'pending', '2025-07-06 05:48:00', '2025-07-06 05:48:01'),
(9, 9, 10000.00, NULL, 'flutterwave', 'pending', '2025-07-06 05:54:04', '2025-07-06 05:54:06'),
(10, 10, 10000.00, NULL, 'flutterwave', 'pending', '2025-07-06 07:20:28', '2025-07-06 07:20:30'),
(11, 11, 10000.00, '2025-07-06 07:24:30', 'flutterwave', 'paid', '2025-07-06 07:23:41', '2025-07-06 07:24:30');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inspection_request_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_reference` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'RWF',
  `payment_method` enum('mtn_momo','airtel_money','visa','mastercard','bank_transfer') NOT NULL,
  `gateway_provider` varchar(50) DEFAULT NULL,
  `gateway_transaction_id` varchar(255) DEFAULT NULL,
  `gateway_reference` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `failure_reason` text DEFAULT NULL,
  `payer_name` varchar(255) DEFAULT NULL,
  `payer_phone` varchar(20) DEFAULT NULL,
  `payer_email` varchar(255) DEFAULT NULL,
  `initiated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `failed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

CREATE TABLE `payment_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`request_data`)),
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_data`)),
  `status_before` varchar(255) DEFAULT NULL,
  `status_after` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_partner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `property_code` varchar(20) NOT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `owner_phone` varchar(20) DEFAULT NULL,
  `owner_email` varchar(255) DEFAULT NULL,
  `property_type` enum('residential','commercial','industrial','mixed') NOT NULL,
  `property_subtype` varchar(100) DEFAULT NULL,
  `address` text NOT NULL,
  `district` varchar(100) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `cell` varchar(100) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `built_year` int(11) DEFAULT NULL,
  `total_area_sqm` decimal(10,2) DEFAULT NULL,
  `floors_count` int(11) NOT NULL DEFAULT 1,
  `bedrooms_count` int(11) DEFAULT NULL,
  `bathrooms_count` int(11) DEFAULT NULL,
  `market_value` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `last_inspection_date` date DEFAULT NULL,
  `property_photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`property_photos`)),
  `additional_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `institutional_partner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_national_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `business_partner_id`, `property_code`, `owner_name`, `owner_phone`, `owner_email`, `property_type`, `property_subtype`, `address`, `district`, `sector`, `cell`, `latitude`, `longitude`, `built_year`, `total_area_sqm`, `floors_count`, `bedrooms_count`, `bathrooms_count`, `market_value`, `status`, `last_inspection_date`, `property_photos`, `additional_notes`, `created_at`, `updated_at`, `institutional_partner_id`, `client_national_id`) VALUES
(2, 3, 'PROP81991', 'kanakuze', '07866635654', 'dative@gmail.com', 'residential', NULL, 'kacyiru', 'Gasabo', 'Bumbogo', 'kamuta', -1.94400000, 30.06200000, NULL, NULL, 1, NULL, NULL, NULL, 'active', NULL, NULL, NULL, '2025-07-06 10:12:44', '2025-07-06 10:12:44', NULL, '112343423423'),
(3, 3, 'PROP22868', 'vuguzigal', '0809890', 'dtive@gmail.com', 'residential', NULL, 'mugisa', 'Kicukiro', 'Gatenga', 'kamuta', -1.94450710, 30.06626487, 2023, 202.00, 1, 1, 3, NULL, 'active', NULL, NULL, NULL, '2025-07-06 10:32:26', '2025-07-06 10:48:25', NULL, '87387837');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'System Administrator', 'Full system access and management', NULL, '2025-06-25 06:59:58', '2025-06-25 06:59:58'),
(2, 'individual_client', 'Individual Client', 'Property owners and individual users', NULL, '2025-06-25 06:59:58', '2025-06-25 06:59:58'),
(3, 'business_partner', 'Business Partner', 'Financial institutions and corporate clients', NULL, '2025-06-25 06:59:58', '2025-06-25 06:59:58'),
(4, 'head_technician', 'Head of Technician', 'Operations manager for inspections', NULL, '2025-06-25 06:59:58', '2025-06-25 06:59:58'),
(5, 'inspector', 'Certified Inspector', 'Field inspection personnel', NULL, '2025-06-25 06:59:58', '2025-06-25 06:59:58'),
(6, 'client', 'Client', NULL, NULL, '2025-06-25 06:59:59', '2025-06-25 06:59:59'),
(7, 'institutional_partner', 'Institutional Partner', NULL, NULL, '2025-06-25 06:59:59', '2025-06-25 06:59:59'),
(8, 'business_partner_admin', 'Business Partner Admin', 'Administrator within a business partner organization. Can manage users, view all data, and manage settings.', '\"[\\\"manage_users\\\",\\\"view_reports\\\",\\\"manage_billing\\\",\\\"create_inspection_requests\\\",\\\"view_all_data\\\",\\\"manage_settings\\\"]\"', '2025-06-25 20:35:26', '2025-06-25 20:35:26'),
(9, 'loan_officer', 'Loan Officer', 'Can create and manage inspection requests for loan applications.', '\"[\\\"create_inspection_requests\\\",\\\"view_own_requests\\\",\\\"view_properties\\\",\\\"download_reports\\\"]\"', '2025-06-25 20:35:27', '2025-06-25 20:35:27'),
(10, 'billing_manager', 'Billing Manager', 'Can view and manage billing information, payments, and financial reports.', '\"[\\\"view_billing\\\",\\\"manage_payments\\\",\\\"view_financial_reports\\\",\\\"view_inspection_requests\\\"]\"', '2025-06-25 20:35:27', '2025-06-25 20:35:27'),
(11, 'property_manager', 'Property Manager', 'Can manage properties and view property-related inspection reports.', '\"[\\\"manage_properties\\\",\\\"view_property_reports\\\",\\\"create_inspection_requests\\\",\\\"view_own_requests\\\"]\"', '2025-06-25 20:35:27', '2025-06-25 20:35:27'),
(12, 'viewer', 'Viewer', 'Read-only access to inspection requests and reports.', '\"[\\\"view_inspection_requests\\\",\\\"view_reports\\\",\\\"download_reports\\\"]\"', '2025-06-25 20:35:27', '2025-06-25 20:35:27');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key_name` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `data_type` enum('string','integer','decimal','boolean','json') NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `group_name` varchar(50) NOT NULL DEFAULT 'general',
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tiers`
--

CREATE TABLE `tiers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `request_quota` int(10) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tiers`
--

INSERT INTO `tiers` (`id`, `name`, `request_quota`, `price`, `created_at`, `updated_at`) VALUES
(1, 'Bronze', 20, 10000.00, '2025-07-06 04:18:07', '2025-07-06 05:22:32'),
(3, 'Premium', 35, 2000000.00, '2025-07-06 05:03:52', '2025-07-06 05:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `tier_inspection_package`
--

CREATE TABLE `tier_inspection_package` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tier_id` bigint(20) UNSIGNED NOT NULL,
  `inspection_package_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tier_inspection_package`
--

INSERT INTO `tier_inspection_package` (`id`, `tier_id`, `inspection_package_id`, `created_at`, `updated_at`) VALUES
(3, 1, 2, NULL, NULL),
(4, 3, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `email_verified_at`, `password`, `phone`, `first_name`, `last_name`, `profile_photo`, `status`, `last_login_at`, `remember_token`, `created_at`, `updated_at`, `created_by`) VALUES
(1, 'admin@fundi.com', NULL, '$2y$12$PVE/xh3/JkjhGcBSWhuwle0SO5JFsw0gRNrsSGreXCDjdeHbN5QJO', NULL, 'Admin', 'User', NULL, 'active', NULL, 'CcWlGCDuG9shp65CACC5MLcPXJKAOIZ8yR6uYDq5PyWzwJSS8J6UpJnY5boV', '2025-06-25 07:00:00', '2025-06-25 07:00:00', NULL),
(2, 'headtech@fundi.com', NULL, '$2y$12$Ldi9cUOimivslUa34TKLZeMFWXpYDDViocWqqc.D3.Z6uebNtvqzm', NULL, 'Head', 'Technician', NULL, 'active', NULL, NULL, '2025-06-25 07:00:00', '2025-07-06 17:06:29', NULL),
(3, 'client@example.com', NULL, '$2y$12$g.Ccq806w0nWzPp.PVRzuONZangZ0cqoPg732wSm2fHjiZbccklbq', '+250788123456', 'John', 'Doe', NULL, 'active', NULL, NULL, '2025-06-25 07:00:00', '2025-07-06 08:27:46', NULL),
(4, 'john.doe@bpr.rw', NULL, '$2y$12$TIJ2kSApF0rTJncJYq1koux5ObHDmzIXv27lM03zkEar9AUTC0I3O', NULL, 'John Doe', 'Admin', NULL, 'active', NULL, NULL, '2025-06-25 07:00:01', '2025-06-25 07:00:01', NULL),
(5, 'jane.smith@ric.rw', NULL, '$2y$12$6hIwFuoKaTJtnI72wdMaH.FCDXcfhU.gs/taWagx4fTbaO9vp0L5O', NULL, 'Jane Smith', 'Admin', NULL, 'active', NULL, NULL, '2025-06-25 07:00:01', '2025-06-25 07:00:01', NULL),
(6, 'alice.johnson@kmf.rw', NULL, '$2y$12$fjnAj1XTPLLwvme5OGBleuivpGYJijc3RnrW510/IfcfU7BCXI7pW', NULL, 'Alice Johnson', 'Admin', NULL, 'active', NULL, NULL, '2025-06-25 07:00:01', '2025-06-25 07:00:01', NULL),
(7, 'bk@fundi.rw', NULL, '$2y$12$eOa2FI8b2VfrtKC4vzSolu.lp4KDzCoMGCyXT9eT0ybEUGMQU51me', '07889798', 'Jean pierree', 'NDAGIJIMANA', NULL, 'active', NULL, NULL, '2025-06-25 22:43:00', '2025-06-27 08:47:43', NULL),
(8, 'kamali@fundi.info', NULL, '$2y$12$zU6LX7CQdiZL.22P3EcwfOsa63OFSb6Om663wu542BNDrjuDHEl5G', '078889798', 'kamali', 'Bugingo', NULL, 'active', NULL, NULL, '2025-06-27 08:31:32', '2025-06-27 08:31:32', NULL),
(9, 'user@bk.rw', NULL, '$2y$12$dtK4TCsrSKzKIuid8xIhYeFtYqtxlCh5HNOck0d6bcJ2KKRx0sjCW', '07889798888', 'Jean pierree', 'NDAGIJIMANA', NULL, 'active', NULL, NULL, '2025-07-04 10:24:06', '2025-07-04 10:24:06', NULL),
(10, 'user@demopartner.com', NULL, '$2y$12$edi1pp/arz8JHLmW1Ogozea.f/9pqBSPTOuvq5KY2boMZBOMh5lDG', NULL, 'Demo', 'User', NULL, 'active', NULL, NULL, '2025-07-05 14:17:35', '2025-07-05 14:17:35', NULL),
(11, 'dative@demo.com', NULL, '$2y$12$ha67S7tt6uFN2vidZgubbukf.Y2hMIZqV3uZEqg8j5HcmXsW99yO.', '078848844', 'mukamusoni', 'datice', NULL, 'active', NULL, NULL, '2025-07-05 16:21:23', '2025-07-05 16:21:23', NULL),
(12, 'view@fundi.info', NULL, '$2y$12$vwhG9rqVFGnueBC5cL8sIO8VQ8mdYEv//fO.Fvg5u4sa9Snf5WUMS', '989898989', 'view', 'view', NULL, 'active', NULL, NULL, '2025-07-05 17:08:05', '2025-07-05 17:08:05', NULL),
(13, 'jos@gmail.com', NULL, '$2y$12$YrpZVxQqavxAYRVEZKCpdeUdaFVZQFmDjnfFJRVeHQE74yv5ztoj2', '0020202', 'Josue', 'MUGIRANEZA', NULL, 'active', NULL, NULL, '2025-07-05 17:08:51', '2025-07-05 17:08:51', NULL),
(19, 'dative@gmail.com', NULL, '$2y$12$9UHKjHVabPQl9kkIsm18tuZlD2xObulJ8HZWHgZAJGe3AZoihMwby', '07866635654', 'kanakuze', '', NULL, 'active', NULL, NULL, '2025-07-06 16:47:44', '2025-07-06 16:47:44', 9),
(20, 'eric@fundi.info', NULL, '$2y$12$TNnOgoMJzKMIk6k65f1bz.UmktoVuIzoUatMKBdniQIjGr61y4nxS', '07888282', 'Habimana', 'Eric', NULL, 'active', NULL, NULL, '2025-07-06 17:13:38', '2025-07-06 17:13:38', NULL),
(21, 'user@bankofkigali.com', NULL, '$2y$12$OlIUDn5Q9tPNQq76WcH1uecTQ/pMVu6HTdo1CL8E5dYYPXcZIOO9m', NULL, 'Bank', 'User', NULL, 'active', NULL, NULL, '2025-07-06 21:30:08', '2025-07-06 21:30:08', NULL),
(22, 'dtive@gmail.com', NULL, '$2y$12$sEKTqUSbQ9q5N/OG5Rphgu37bMzYz9p7c1mgukk.5NKwKIBu8f7Ha', '0809890', 'vuguzigal', '', NULL, 'active', NULL, NULL, '2025-07-06 21:58:06', '2025-07-06 21:58:06', 9);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `assigned_at`, `assigned_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-06-25 09:00:00', NULL, '2025-06-25 07:00:00', '2025-06-25 07:00:00'),
(4, 4, 7, '2025-06-25 09:00:01', NULL, '2025-06-25 07:00:01', '2025-06-25 07:00:01'),
(5, 5, 7, '2025-06-25 09:00:01', NULL, '2025-06-25 07:00:01', '2025-06-25 07:00:01'),
(6, 6, 7, '2025-06-25 09:00:01', NULL, '2025-06-25 07:00:01', '2025-06-25 07:00:01'),
(8, 8, 3, '2025-06-27 08:31:32', 1, '2025-06-27 08:31:32', '2025-06-27 08:31:32'),
(10, 7, 3, '2025-06-27 08:47:43', 1, '2025-06-27 08:47:43', '2025-06-27 08:47:43'),
(11, 9, 3, '2025-07-04 10:24:06', 1, '2025-07-04 10:24:06', '2025-07-04 10:24:06'),
(12, 3, 2, '2025-07-06 08:27:46', 1, '2025-07-06 08:27:46', '2025-07-06 08:27:46'),
(13, 2, 4, '2025-07-06 17:06:29', 1, '2025-07-06 17:06:29', '2025-07-06 17:06:29'),
(14, 20, 5, '2025-07-06 17:13:38', 1, '2025-07-06 17:13:38', '2025-07-06 17:13:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `business_partners`
--
ALTER TABLE `business_partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_partners_subdomain_unique` (`subdomain`),
  ADD KEY `business_partners_type_index` (`type`),
  ADD KEY `business_partners_partnership_status_index` (`partnership_status`);

--
-- Indexes for table `business_partner_users`
--
ALTER TABLE `business_partner_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_partner_users_business_partner_id_user_id_unique` (`business_partner_id`,`user_id`),
  ADD KEY `business_partner_users_user_id_foreign` (`user_id`),
  ADD KEY `business_partner_users_added_by_foreign` (`added_by`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `inspection_packages`
--
ALTER TABLE `inspection_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspection_packages_target_client_type_index` (`target_client_type`),
  ADD KEY `inspection_packages_is_active_index` (`is_active`);

--
-- Indexes for table `inspection_reports`
--
ALTER TABLE `inspection_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inspection_reports_inspection_request_id_inspector_id_unique` (`inspection_request_id`,`inspector_id`),
  ADD KEY `inspection_reports_inspector_id_foreign` (`inspector_id`);

--
-- Indexes for table `inspection_requests`
--
ALTER TABLE `inspection_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inspection_requests_request_number_unique` (`request_number`),
  ADD KEY `inspection_requests_requester_user_id_foreign` (`requester_user_id`),
  ADD KEY `inspection_requests_business_partner_id_foreign` (`business_partner_id`),
  ADD KEY `inspection_requests_property_id_foreign` (`property_id`),
  ADD KEY `inspection_requests_package_id_foreign` (`package_id`),
  ADD KEY `inspection_requests_assigned_by_foreign` (`assigned_by`),
  ADD KEY `inspection_requests_status_index` (`status`),
  ADD KEY `inspection_requests_requester_type_requester_user_id_index` (`requester_type`,`requester_user_id`),
  ADD KEY `inspection_requests_assigned_inspector_id_index` (`assigned_inspector_id`),
  ADD KEY `inspection_requests_scheduled_date_index` (`scheduled_date`),
  ADD KEY `inspection_requests_institutional_partner_id_index` (`institutional_partner_id`),
  ADD KEY `inspection_requests_client_id_foreign` (`client_id`);

--
-- Indexes for table `inspection_services`
--
ALTER TABLE `inspection_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspection_services_category_index` (`category`);

--
-- Indexes for table `inspection_status_history`
--
ALTER TABLE `inspection_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspection_status_history_inspection_request_id_foreign` (`inspection_request_id`),
  ADD KEY `inspection_status_history_changed_by_foreign` (`changed_by`);

--
-- Indexes for table `inspectors`
--
ALTER TABLE `inspectors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inspectors_inspector_code_unique` (`inspector_code`),
  ADD KEY `inspectors_user_id_foreign` (`user_id`),
  ADD KEY `inspectors_availability_status_index` (`availability_status`),
  ADD KEY `inspectors_rating_index` (`rating`),
  ADD KEY `inspectors_current_location_lat_current_location_lng_index` (`current_location_lat`,`current_location_lng`);

--
-- Indexes for table `inspector_certifications`
--
ALTER TABLE `inspector_certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inspector_certifications_inspector_id_foreign` (`inspector_id`);

--
-- Indexes for table `institutional_partners`
--
ALTER TABLE `institutional_partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `institutional_partners_subdomain_unique` (`subdomain`),
  ADD UNIQUE KEY `institutional_partners_email_unique` (`email`),
  ADD KEY `institutional_partners_subdomain_status_index` (`subdomain`,`status`),
  ADD KEY `institutional_partners_institution_type_index` (`institution_type`);

--
-- Indexes for table `institutional_partner_users`
--
ALTER TABLE `institutional_partner_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_user_unique` (`institutional_partner_id`,`user_id`),
  ADD KEY `institutional_partner_users_added_by_foreign` (`added_by`),
  ADD KEY `ip_access_idx` (`institutional_partner_id`,`access_level`),
  ADD KEY `user_access_idx` (`user_id`,`access_level`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`),
  ADD KEY `notifications_template_id_foreign` (`template_id`);

--
-- Indexes for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_templates_name_unique` (`name`);

--
-- Indexes for table `package_services`
--
ALTER TABLE `package_services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_services_package_id_service_id_unique` (`package_id`,`service_id`),
  ADD KEY `package_services_service_id_foreign` (`service_id`);

--
-- Indexes for table `partner_billings`
--
ALTER TABLE `partner_billings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `partner_billings_billing_reference_unique` (`billing_reference`),
  ADD KEY `partner_billings_created_by_foreign` (`created_by`),
  ADD KEY `partner_billings_approved_by_foreign` (`approved_by`),
  ADD KEY `partner_billings_business_partner_id_status_index` (`business_partner_id`,`status`),
  ADD KEY `partner_billings_billing_period_start_billing_period_end_index` (`billing_period_start`,`billing_period_end`),
  ADD KEY `partner_billings_status_index` (`status`),
  ADD KEY `partner_billings_due_date_index` (`due_date`),
  ADD KEY `partner_billings_institutional_partner_id_index` (`institutional_partner_id`);

--
-- Indexes for table `partner_tiers`
--
ALTER TABLE `partner_tiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partner_tiers_business_partner_id_foreign` (`business_partner_id`),
  ADD KEY `partner_tiers_tier_id_foreign` (`tier_id`);

--
-- Indexes for table `partner_tier_payments`
--
ALTER TABLE `partner_tier_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `partner_tier_payments_partner_tier_id_foreign` (`partner_tier_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_transaction_reference_unique` (`transaction_reference`),
  ADD KEY `payments_inspection_request_id_foreign` (`inspection_request_id`),
  ADD KEY `payments_status_index` (`status`),
  ADD KEY `payments_transaction_reference_index` (`transaction_reference`),
  ADD KEY `payments_gateway_transaction_id_index` (`gateway_transaction_id`);

--
-- Indexes for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_logs_payment_id_foreign` (`payment_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `properties_property_code_unique` (`property_code`),
  ADD KEY `properties_property_type_index` (`property_type`),
  ADD KEY `properties_district_sector_index` (`district`,`sector`),
  ADD KEY `properties_latitude_longitude_index` (`latitude`,`longitude`),
  ADD KEY `properties_institutional_partner_id_index` (`institutional_partner_id`),
  ADD KEY `properties_business_partner_id_foreign` (`business_partner_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_name_unique` (`key_name`),
  ADD KEY `system_settings_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `tiers`
--
ALTER TABLE `tiers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tier_inspection_package`
--
ALTER TABLE `tier_inspection_package`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tier_inspection_package_tier_id_foreign` (`tier_id`),
  ADD KEY `tier_inspection_package_inspection_package_id_foreign` (`inspection_package_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD KEY `users_email_index` (`email`),
  ADD KEY `users_phone_index` (`phone`),
  ADD KEY `users_status_index` (`status`),
  ADD KEY `users_created_by_index` (`created_by`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`),
  ADD KEY `user_roles_assigned_by_foreign` (`assigned_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `business_partners`
--
ALTER TABLE `business_partners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `business_partner_users`
--
ALTER TABLE `business_partner_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_packages`
--
ALTER TABLE `inspection_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inspection_reports`
--
ALTER TABLE `inspection_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inspection_requests`
--
ALTER TABLE `inspection_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inspection_services`
--
ALTER TABLE `inspection_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `inspection_status_history`
--
ALTER TABLE `inspection_status_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `inspectors`
--
ALTER TABLE `inspectors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inspector_certifications`
--
ALTER TABLE `inspector_certifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `institutional_partners`
--
ALTER TABLE `institutional_partners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `institutional_partner_users`
--
ALTER TABLE `institutional_partner_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_templates`
--
ALTER TABLE `notification_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package_services`
--
ALTER TABLE `package_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `partner_billings`
--
ALTER TABLE `partner_billings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `partner_tiers`
--
ALTER TABLE `partner_tiers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `partner_tier_payments`
--
ALTER TABLE `partner_tier_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tiers`
--
ALTER TABLE `tiers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tier_inspection_package`
--
ALTER TABLE `tier_inspection_package`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `business_partner_users`
--
ALTER TABLE `business_partner_users`
  ADD CONSTRAINT `business_partner_users_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `business_partner_users_business_partner_id_foreign` FOREIGN KEY (`business_partner_id`) REFERENCES `business_partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `business_partner_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inspection_reports`
--
ALTER TABLE `inspection_reports`
  ADD CONSTRAINT `inspection_reports_inspection_request_id_foreign` FOREIGN KEY (`inspection_request_id`) REFERENCES `inspection_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inspection_reports_inspector_id_foreign` FOREIGN KEY (`inspector_id`) REFERENCES `inspectors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inspection_requests`
--
ALTER TABLE `inspection_requests`
  ADD CONSTRAINT `inspection_requests_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inspection_requests_assigned_inspector_id_foreign` FOREIGN KEY (`assigned_inspector_id`) REFERENCES `inspectors` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inspection_requests_business_partner_id_foreign` FOREIGN KEY (`business_partner_id`) REFERENCES `business_partners` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inspection_requests_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inspection_requests_institutional_partner_id_foreign` FOREIGN KEY (`institutional_partner_id`) REFERENCES `institutional_partners` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inspection_requests_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `inspection_packages` (`id`),
  ADD CONSTRAINT `inspection_requests_property_id_foreign` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inspection_requests_requester_user_id_foreign` FOREIGN KEY (`requester_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inspection_status_history`
--
ALTER TABLE `inspection_status_history`
  ADD CONSTRAINT `inspection_status_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inspection_status_history_inspection_request_id_foreign` FOREIGN KEY (`inspection_request_id`) REFERENCES `inspection_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inspectors`
--
ALTER TABLE `inspectors`
  ADD CONSTRAINT `inspectors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inspector_certifications`
--
ALTER TABLE `inspector_certifications`
  ADD CONSTRAINT `inspector_certifications_inspector_id_foreign` FOREIGN KEY (`inspector_id`) REFERENCES `inspectors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `institutional_partner_users`
--
ALTER TABLE `institutional_partner_users`
  ADD CONSTRAINT `institutional_partner_users_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `institutional_partner_users_institutional_partner_id_foreign` FOREIGN KEY (`institutional_partner_id`) REFERENCES `institutional_partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `institutional_partner_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `notification_templates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `package_services`
--
ALTER TABLE `package_services`
  ADD CONSTRAINT `package_services_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `inspection_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `package_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `inspection_services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `partner_billings`
--
ALTER TABLE `partner_billings`
  ADD CONSTRAINT `partner_billings_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `partner_billings_business_partner_id_foreign` FOREIGN KEY (`business_partner_id`) REFERENCES `business_partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `partner_billings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `partner_billings_institutional_partner_id_foreign` FOREIGN KEY (`institutional_partner_id`) REFERENCES `institutional_partners` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `partner_tiers`
--
ALTER TABLE `partner_tiers`
  ADD CONSTRAINT `partner_tiers_business_partner_id_foreign` FOREIGN KEY (`business_partner_id`) REFERENCES `business_partners` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `partner_tiers_tier_id_foreign` FOREIGN KEY (`tier_id`) REFERENCES `tiers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `partner_tier_payments`
--
ALTER TABLE `partner_tier_payments`
  ADD CONSTRAINT `partner_tier_payments_partner_tier_id_foreign` FOREIGN KEY (`partner_tier_id`) REFERENCES `partner_tiers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_inspection_request_id_foreign` FOREIGN KEY (`inspection_request_id`) REFERENCES `inspection_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD CONSTRAINT `payment_logs_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_business_partner_id_foreign` FOREIGN KEY (`business_partner_id`) REFERENCES `business_partners` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `properties_institutional_partner_id_foreign` FOREIGN KEY (`institutional_partner_id`) REFERENCES `institutional_partners` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tier_inspection_package`
--
ALTER TABLE `tier_inspection_package`
  ADD CONSTRAINT `tier_inspection_package_inspection_package_id_foreign` FOREIGN KEY (`inspection_package_id`) REFERENCES `inspection_packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tier_inspection_package_tier_id_foreign` FOREIGN KEY (`tier_id`) REFERENCES `tiers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
