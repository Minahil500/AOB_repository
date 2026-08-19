-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Aug 13, 2026 at 06:57 PM
-- Server version: 5.7.24
-- PHP Version: 8.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aobrepository`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `module` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `record_reference` varchar(150) DEFAULT NULL,
  `previous_value` text,
  `new_value` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `role_id`, `module`, `action`, `record_reference`, `previous_value`, `new_value`, `ip_address`, `description`, `created_at`) VALUES
(1, 1, NULL, 'Activity Logs', 'TEST', 'TEST-1', '', '', '::1', 'Activity logger test', '2026-08-12 11:43:50'),
(2, 1, 2, 'Legal Documents', 'CREATE', 'Document ID: 4', '', '{\"document_number\":\"DOC\\/AOB\\/2026\\/005\",\"document_name\":\"Audit\",\"document_type_id\":2,\"firm_id\":1,\"case_id\":1,\"version\":\"1.0\",\"ocr_status\":\"Pending\"}', '::1', 'New legal document created.', '2026-08-12 12:20:40'),
(3, 1, 2, 'Legal Documents', 'CREATE', 'Document #2', '', '{\"document_number\":\"DOC\\/AOB\\/2026\\/005\",\"document_name\":\"Audit\",\"document_type_id\":2,\"firm_id\":1,\"case_id\":1}', '::1', 'New legal document created', '2026-08-12 12:20:40'),
(4, 1, 2, 'Legal Documents', 'CREATE', 'Document ID: 6', '', '{\"document_number\":\"DOC\\/AOB\\/2026\\/006\",\"document_name\":\"abc\",\"document_type_id\":3,\"firm_id\":1,\"case_id\":1,\"version\":\"1.5\",\"ocr_status\":\"Pending\"}', '::1', 'New legal document created.', '2026-08-12 12:28:03'),
(5, 1, 2, 'Legal Documents', 'CREATE', 'Document #4', '', '{\"document_number\":\"DOC\\/AOB\\/2026\\/006\",\"document_name\":\"abc\",\"document_type_id\":3,\"firm_id\":1,\"case_id\":1}', '::1', 'New legal document created', '2026-08-12 12:28:03'),
(6, 1, 2, 'Legal Documents', 'UPDATE', 'Document ID: 2', '{\"document_number\":\"DOC\\/AOB\\/2026\\/001\",\"document_name\":\"Audit compliance review order\",\"document_type_id\":\"1\",\"firm_id\":\"4\",\"case_id\":\"1\",\"court_id\":\"1\",\"version\":\"1.0\",\"ocr_status\":\"Completed\",\"document_date\":\"2026-08-11\",\"description\":\"Review document\"}', '{\"document_number\":\"DOC\\/AOB\\/2026\\/001\",\"document_name\":\"Eeview order\",\"document_type_id\":1,\"firm_id\":4,\"case_id\":1,\"court_id\":1,\"version\":\"1.0\",\"ocr_status\":\"Completed\",\"document_date\":\"2026-08-11\",\"description\":\"Review document\"}', '::1', 'Legal document updated.', '2026-08-12 12:39:42'),
(7, 1, 2, 'Legal Documents', 'UPDATE', 'Document ID: 2', '{\"document_number\":\"DOC\\/AOB\\/2026\\/001\",\"document_name\":\"Eeview order\",\"document_type_id\":\"1\",\"firm_id\":\"4\",\"case_id\":\"1\",\"court_id\":\"1\",\"version\":\"1.0\",\"ocr_status\":\"Completed\",\"document_date\":\"2026-08-11\",\"description\":\"Review document\"}', '{\"document_number\":\"DOC\\/AOB\\/2026\\/001\",\"document_name\":\"Review order\",\"document_type_id\":1,\"firm_id\":4,\"case_id\":1,\"court_id\":1,\"version\":\"1.0\",\"ocr_status\":\"Completed\",\"document_date\":\"2026-08-11\",\"description\":\"Review document\"}', '::1', 'Legal document updated.', '2026-08-12 12:39:56'),
(8, 1, 2, 'Legal Documents', 'DELETE', 'Document ID: 4', '{\"document_number\":\"DOC\\/AOB\\/2026\\/005\",\"document_name\":\"Audit\",\"document_type_id\":\"2\",\"firm_id\":\"1\",\"case_id\":\"1\",\"court_id\":\"3\",\"version\":\"1.5\",\"ocr_status\":\"Pending\",\"file_name\":\"1786537240_2edb4d4c28709025.pdf\",\"document_date\":\"2026-08-11\",\"description\":\"testing activity log functionality\"}', '', '::1', 'Legal document deleted.', '2026-08-12 12:46:25'),
(9, 1, 2, 'Legal Documents', 'DETECT', 'Document ID: 3', '{\"ocr_status\":\"Not Required\"}', '{\"pdf_type\":\"Searchable PDF\",\"ocr_status\":\"Not Required\",\"extracted_text_length\":6108}', '::1', 'PDF type detected as Searchable PDF.', '2026-08-12 12:48:15'),
(10, 1, 2, 'Legal Documents', 'DETECT', 'Document ID: 6', '{\"ocr_status\":\"Pending\"}', '{\"pdf_type\":\"Searchable PDF\",\"ocr_status\":\"Not Required\",\"extracted_text_length\":3126}', '::1', 'PDF type detected as Searchable PDF.', '2026-08-12 12:49:39'),
(11, 1, 2, 'Legal Documents', 'DETECT', 'Document ID: 3', '{\"ocr_status\":\"Not Required\"}', '{\"pdf_type\":\"Searchable PDF\",\"ocr_status\":\"Not Required\",\"extracted_text_length\":6108}', '::1', 'PDF type detected as Searchable PDF.', '2026-08-12 12:49:45'),
(12, 1, 2, 'Legal Documents', 'CREATE', 'Document #7', '', '{\"document_number\":\"DOC\\/AOB\\/2026\\/007\",\"document_name\":\"abc\",\"document_type_id\":6,\"firm_id\":4,\"case_id\":1}', '::1', 'New legal document created', '2026-08-12 12:54:27'),
(13, 1, 2, 'Legal Documents', 'DETECT', 'Document ID: 7', '{\"ocr_status\":\"Pending\"}', '{\"pdf_type\":\"Scanned PDF\",\"ocr_status\":\"Pending\",\"extracted_text_length\":0}', '::1', 'PDF type detected as Scanned PDF.', '2026-08-12 12:54:31'),
(14, 1, 2, 'Legal Documents', 'DETECT', 'Document ID: 7', '{\"ocr_status\":\"Completed\"}', '{\"pdf_type\":\"Scanned PDF\",\"ocr_status\":\"Pending\",\"extracted_text_length\":0}', '::1', 'PDF type detected as Scanned PDF.', '2026-08-12 12:56:01'),
(15, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Processing', 'Processing', '::1', 'OCR processing started.', '2026-08-12 13:01:48'),
(16, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Processing', 'Failed', '::1', 'OCR failed during PDF to image conversion. Return code: -1073741819', '2026-08-12 13:01:55'),
(17, 1, 2, 'Legal Documents', 'DETECT', 'Document ID: 7', '{\"ocr_status\":\"Failed\"}', '{\"pdf_type\":\"Scanned PDF\",\"ocr_status\":\"Pending\",\"extracted_text_length\":0}', '::1', 'PDF type detected as Scanned PDF.', '2026-08-12 13:02:10'),
(18, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Pending', 'Processing', '::1', 'OCR processing started.', '2026-08-12 13:02:15'),
(19, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Processing', 'Failed', '::1', 'OCR failed during PDF to image conversion. Return code: -1073741819', '2026-08-12 13:02:21'),
(20, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Failed', 'Processing', '::1', 'OCR processing started for legal document.', '2026-08-12 13:06:24'),
(21, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Processing', 'Failed', '::1', 'PDF to image conversion failed using Imagick: FailedToExecuteCommand `\"gswin64c.exe\" -q -dQUIET -dSAFER -dBATCH -dNOPAUSE -dNOPROMPT -dMaxBitmap=500000000 -dAlignToPixels=0 -dGridFitTT=2 \"-sDEVICE=pngalpha\" -dTextAlphaBits=4 -dGraphicsAlphaBits=4 \"-r200x200\"  \"-sOutputFile=C:/Users/hp/AppData/Local/Temp/magick-KsKI4kyXu_Jg7TqD97BE6JWRGAWhLv3E%d\" \"-fC:/Users/hp/AppData/Local/Temp/magick-PfCZ7NIj23w3shqyEjpI0EB6lASdVsLj\" \"-fC:/Users/hp/AppData/Local/Temp/magick-v-MwHjItgFELL_UBMphqU54UoWZv0p_L\"\' (The system cannot find the file specified.\r\n) @ error/delegate.c/ExternalDelegateCommand/510', '2026-08-12 13:06:24'),
(22, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Failed', 'Processing', '::1', 'OCR processing started for legal document.', '2026-08-12 13:07:46'),
(23, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Processing', 'Failed', '::1', 'PDF to image conversion failed using Imagick: Undefined constant Imagick::COMPRESSION_PNG', '2026-08-12 13:07:48'),
(24, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Failed', 'Processing', '::1', 'OCR processing started for legal document.', '2026-08-12 13:10:24'),
(25, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Processing', 'Completed', '::1', 'OCR processing completed successfully. 1 page(s) processed.', '2026-08-12 13:10:27'),
(26, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Completed', 'Processing', '::1', 'OCR processing started for legal document.', '2026-08-12 18:31:20'),
(27, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Processing', 'Completed', '::1', 'OCR processing completed successfully. 1 page(s) processed.', '2026-08-12 18:31:23'),
(28, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Completed', 'Processing', '::1', 'OCR processing started for legal document.', '2026-08-12 18:31:32'),
(29, 1, 2, 'Legal Documents', 'OCR', 'Document ID: 7', 'Processing', 'Completed', '::1', 'OCR processing completed successfully. 1 page(s) processed.', '2026-08-12 18:31:35'),
(30, 1, 2, 'System Settings', 'UPDATE', 'System Settings', '{\"id\":\"1\",\"organisation_name\":\"AOB Legal Repository\",\"short_name\":\"AOB\",\"official_email\":\"admin@aob.com\",\"contact_number\":\"12345678\",\"time_zone\":\"Asia\\/Karachi\",\"default_department_id\":null,\"registered_address\":\"AOB Head Office, Karachi\",\"minimum_password_length\":\"12\",\"password_expiry_days\":\"90\",\"failed_attempts_before_lockout\":\"3\",\"session_timeout_minutes\":\"20\",\"default_role_id\":null,\"require_two_factor_auth\":\"0\",\"restrict_network_access\":\"0\",\"maximum_file_size_mb\":\"20\",\"allowed_file_types\":\"pdf\",\"default_document_type_id\":null,\"record_retention_years\":\"7\",\"run_ocr_on_upload\":\"0\",\"keep_version_history\":\"1\",\"case_assignment_alerts\":\"1\",\"followup_due_reminders\":\"1\",\"daily_digest_email\":\"0\",\"created_at\":\"2026-08-12 23:59:49\",\"updated_at\":\"2026-08-13 00:00:22\"}', '{\"organisation_name\":\"AOB\",\"short_name\":\"AOB\",\"official_email\":\"admin@aob.com\",\"contact_number\":\"12345678\",\"time_zone\":\"Asia\\/Karachi\",\"default_department_id\":null,\"minimum_password_length\":12,\"password_expiry_days\":90,\"failed_attempts_before_lockout\":3,\"session_timeout_minutes\":20,\"require_two_factor_auth\":0,\"restrict_network_access\":0,\"maximum_file_size_mb\":20,\"allowed_file_types\":\"pdf\",\"record_retention_years\":7,\"run_ocr_on_upload\":0,\"keep_version_history\":1,\"case_assignment_alerts\":1,\"followup_due_reminders\":1,\"daily_digest_email\":0}', '::1', 'System settings updated.', '2026-08-12 19:01:57'),
(31, 1, 2, 'System Settings', 'UPDATE', 'System Settings', '{\"id\":\"1\",\"organisation_name\":\"AOB\",\"short_name\":\"AOB\",\"official_email\":\"admin@aob.com\",\"contact_number\":\"12345678\",\"time_zone\":\"Asia\\/Karachi\",\"default_department_id\":null,\"registered_address\":\"AOB Head Office, Karachi\",\"minimum_password_length\":\"12\",\"password_expiry_days\":\"90\",\"failed_attempts_before_lockout\":\"3\",\"session_timeout_minutes\":\"20\",\"default_role_id\":null,\"require_two_factor_auth\":\"0\",\"restrict_network_access\":\"0\",\"maximum_file_size_mb\":\"20\",\"allowed_file_types\":\"0\",\"default_document_type_id\":null,\"record_retention_years\":\"7\",\"run_ocr_on_upload\":\"0\",\"keep_version_history\":\"1\",\"case_assignment_alerts\":\"1\",\"followup_due_reminders\":\"1\",\"daily_digest_email\":\"0\",\"created_at\":\"2026-08-12 23:59:49\",\"updated_at\":\"2026-08-13 00:01:57\"}', '{\"organisation_name\":\"ABC\",\"short_name\":\"AOB\",\"official_email\":\"admin@aob.com\",\"contact_number\":\"12345678\",\"time_zone\":\"Asia\\/Karachi\",\"default_department_id\":null,\"registered_address\":\"AOB Head Office, Karachi\",\"minimum_password_length\":12,\"password_expiry_days\":90,\"failed_attempts_before_lockout\":3,\"session_timeout_minutes\":20,\"default_role_id\":null,\"require_two_factor_auth\":0,\"restrict_network_access\":0,\"maximum_file_size_mb\":20,\"allowed_file_types\":\"0\",\"default_document_type_id\":null,\"record_retention_years\":7,\"run_ocr_on_upload\":0,\"keep_version_history\":1,\"case_assignment_alerts\":1,\"followup_due_reminders\":1,\"daily_digest_email\":0}', '::1', 'System settings updated.', '2026-08-12 19:05:00'),
(32, 1, 2, 'System Settings', 'UPDATE', 'System Settings', '{\"id\":\"1\",\"organisation_name\":\"ABC\",\"short_name\":\"AOB\",\"official_email\":\"admin@aob.com\",\"contact_number\":\"12345678\",\"time_zone\":\"Asia\\/Karachi\",\"default_department_id\":null,\"registered_address\":\"AOB Head Office, Karachi\",\"minimum_password_length\":\"12\",\"password_expiry_days\":\"90\",\"failed_attempts_before_lockout\":\"3\",\"session_timeout_minutes\":\"20\",\"default_role_id\":null,\"require_two_factor_auth\":\"0\",\"restrict_network_access\":\"0\",\"maximum_file_size_mb\":\"20\",\"allowed_file_types\":\"0\",\"default_document_type_id\":null,\"record_retention_years\":\"7\",\"run_ocr_on_upload\":\"0\",\"keep_version_history\":\"1\",\"case_assignment_alerts\":\"1\",\"followup_due_reminders\":\"1\",\"daily_digest_email\":\"0\",\"created_at\":\"2026-08-12 23:59:49\",\"updated_at\":\"2026-08-13 00:05:00\"}', '{\"organisation_name\":\"XYZ\",\"short_name\":\"AOB\",\"official_email\":\"admin@aob.com\",\"contact_number\":\"12345678\",\"time_zone\":\"Asia\\/Karachi\",\"default_department_id\":null,\"registered_address\":\"AOB Head Office, Karachi\",\"minimum_password_length\":12,\"password_expiry_days\":90,\"failed_attempts_before_lockout\":3,\"session_timeout_minutes\":20,\"default_role_id\":null,\"require_two_factor_auth\":0,\"restrict_network_access\":0,\"maximum_file_size_mb\":20,\"allowed_file_types\":\"0\",\"default_document_type_id\":null,\"record_retention_years\":7,\"run_ocr_on_upload\":0,\"keep_version_history\":1,\"case_assignment_alerts\":1,\"followup_due_reminders\":1,\"daily_digest_email\":0}', '::1', 'System settings updated.', '2026-08-12 19:07:12'),
(33, 1, 2, 'System Settings', 'UPDATE', 'System Settings', '{\"id\":\"1\",\"organisation_name\":\"XYZ\",\"short_name\":\"AOB\",\"official_email\":\"admin@aob.com\",\"contact_number\":\"12345678\",\"time_zone\":\"Asia\\/Karachi\",\"default_department_id\":null,\"registered_address\":\"AOB Head Office, Karachi\",\"minimum_password_length\":\"12\",\"password_expiry_days\":\"90\",\"failed_attempts_before_lockout\":\"3\",\"session_timeout_minutes\":\"20\",\"default_role_id\":null,\"require_two_factor_auth\":\"0\",\"restrict_network_access\":\"0\",\"maximum_file_size_mb\":\"20\",\"allowed_file_types\":\"0\",\"default_document_type_id\":null,\"record_retention_years\":\"7\",\"run_ocr_on_upload\":\"0\",\"keep_version_history\":\"1\",\"case_assignment_alerts\":\"1\",\"followup_due_reminders\":\"1\",\"daily_digest_email\":\"0\",\"created_at\":\"2026-08-12 23:59:49\",\"updated_at\":\"2026-08-13 00:07:12\"}', '{\"organisation_name\":\"Audit oversight board\",\"short_name\":\"AOB\",\"official_email\":\"admin@aob.com\",\"contact_number\":\"12345678\",\"time_zone\":\"Asia\\/Karachi\",\"default_department_id\":null,\"registered_address\":\"AOB Head Office, Karachi\",\"minimum_password_length\":12,\"password_expiry_days\":90,\"failed_attempts_before_lockout\":3,\"session_timeout_minutes\":20,\"default_role_id\":null,\"require_two_factor_auth\":0,\"restrict_network_access\":0,\"maximum_file_size_mb\":20,\"allowed_file_types\":\"0\",\"default_document_type_id\":null,\"record_retention_years\":7,\"run_ocr_on_upload\":0,\"keep_version_history\":1,\"case_assignment_alerts\":1,\"followup_due_reminders\":1,\"daily_digest_email\":0}', '::1', 'System settings updated.', '2026-08-12 19:09:58'),
(34, 1, 2, 'System Settings', 'UPDATE', 'System Settings', '{\"id\":\"1\",\"organisation_name\":\"Audit oversight board\",\"short_name\":\"AOB\",\"official_email\":\"admin@aob.com\",\"contact_number\":\"12345678\",\"time_zone\":\"Asia\\/Karachi\",\"default_department_id\":null,\"registered_address\":\"AOB Head Office, Karachi\",\"minimum_password_length\":\"12\",\"password_expiry_days\":\"90\",\"failed_attempts_before_lockout\":\"3\",\"session_timeout_minutes\":\"20\",\"default_role_id\":null,\"require_two_factor_auth\":\"0\",\"restrict_network_access\":\"0\",\"maximum_file_size_mb\":\"20\",\"allowed_file_types\":\"0\",\"default_document_type_id\":null,\"record_retention_years\":\"7\",\"run_ocr_on_upload\":\"0\",\"keep_version_history\":\"1\",\"case_assignment_alerts\":\"1\",\"followup_due_reminders\":\"1\",\"daily_digest_email\":\"0\",\"created_at\":\"2026-08-12 23:59:49\",\"updated_at\":\"2026-08-13 00:09:58\"}', '{\"organisation_name\":\"Audit oversight board\",\"short_name\":\"AOB\",\"official_email\":\"admin@aob.com\",\"contact_number\":\"12345678\",\"time_zone\":\"Asia\\/Karachi\",\"default_department_id\":null,\"registered_address\":\"AOB Head Office, Karachi\",\"minimum_password_length\":12,\"password_expiry_days\":90,\"failed_attempts_before_lockout\":3,\"session_timeout_minutes\":20,\"default_role_id\":null,\"require_two_factor_auth\":0,\"restrict_network_access\":0,\"maximum_file_size_mb\":20,\"allowed_file_types\":\"0\",\"default_document_type_id\":null,\"record_retention_years\":7,\"run_ocr_on_upload\":0,\"keep_version_history\":1,\"case_assignment_alerts\":1,\"followup_due_reminders\":1,\"daily_digest_email\":0}', '::1', 'System settings updated.', '2026-08-12 19:12:18'),
(35, 1, 2, 'Case Stages', 'CREATE', 'Stage #1', NULL, '{\"stage_name\":\"initial\",\"description\":\"testing\"}', '::1', 'New case stage created', '2026-08-12 19:34:25'),
(36, 1, 2, 'Case Stages', 'CREATE', 'Stage #5', NULL, '{\"stage_name\":\"Initial Review\",\"description\":\"Testing\"}', '::1', 'New case stage created', '2026-08-12 19:40:42'),
(37, 1, 2, 'Case Stages', 'CREATE', 'Stage #6', NULL, '{\"stage_name\":\"New stage\",\"description\":\"investigation\"}', '::1', 'New case stage created', '2026-08-12 19:41:27'),
(38, 1, 2, 'Case Stages', 'UPDATE', 'Stage #1', '{\"stage_name\":\"initial\",\"description\":\"testing\"}', '{\"stage_name\":\"initial\",\"description\":\"testing\"}', '::1', 'Case stage updated', '2026-08-12 19:42:55'),
(39, 1, 2, 'Case Stages', 'UPDATE', 'Stage #1', '{\"stage_name\":\"initial\",\"description\":\"testing\"}', '{\"stage_name\":\"Initial\",\"description\":\"testing\"}', '::1', 'Case stage updated', '2026-08-12 19:43:08'),
(40, 1, 2, 'Case Stages', 'DELETE', 'Stage #6', '{\"stage_name\":\"New stage\",\"description\":\"investigation\"}', NULL, '::1', 'Case stage deleted', '2026-08-12 19:45:06'),
(41, 1, 2, 'Case Statuses', 'CREATE', 'Status #1', NULL, '{\"status_name\":\"Under Review\",\"description\":\"Testing\"}', '::1', 'New case status created', '2026-08-12 19:51:37'),
(42, 1, 2, 'Case Statuses', 'UPDATE', 'Status #1', '{\"status_name\":\"Under Review\",\"description\":\"Testing\"}', '{\"status_name\":\"Under Review\",\"description\":\"Testing functionality\"}', '::1', 'Case status updated', '2026-08-12 19:51:47'),
(43, 1, 2, 'Case Statuses', 'CREATE', 'Status #2', NULL, '{\"status_name\":\"Open\",\"description\":\"Investigation\"}', '::1', 'New case status created', '2026-08-12 19:52:23'),
(44, 1, 2, 'Case Statuses', 'DELETE', 'Status #2', '{\"status_name\":\"Open\",\"description\":\"Investigation\"}', NULL, '::1', 'Case status deleted', '2026-08-12 19:52:36'),
(45, 1, 2, 'Case Types', 'CREATE', 'Case Type #1', NULL, '{\"type_name\":\"Audit appeal\",\"description\":\"testing\"}', '::1', 'New case type created.', '2026-08-12 19:58:21'),
(46, 1, 2, 'Case Types', 'CREATE', 'Case Type #2', NULL, '{\"type_name\":\"Regulatory violation\",\"description\":\"test\"}', '::1', 'New case type created.', '2026-08-12 20:04:08'),
(47, 1, 2, 'Case Types', 'UPDATE', 'Case Type #2', '{\"id\":2,\"type_name\":\"Regulatory violation\",\"description\":\"test\"}', '{\"type_name\":\"Regulatory violation\",\"description\":\"tests\"}', '::1', 'Case type updated.', '2026-08-12 20:04:14'),
(48, 1, 2, 'Case Types', 'DELETE', 'Case Type #2', '{\"id\":2,\"type_name\":\"Regulatory violation\",\"description\":\"tests\"}', NULL, '::1', 'Case type deleted.', '2026-08-12 20:04:27'),
(49, 1, 2, 'Regulations', 'CREATE', 'Regulation #1', NULL, '{\"regulation_name\":\"AOB rules\",\"description\":\"testing\"}', '::1', 'New regulation created.', '2026-08-12 20:11:36'),
(50, 1, 2, 'Regulations', 'UPDATE', 'Regulation #1', '{\"id\":1,\"regulation_name\":\"AOB rules\",\"description\":\"testing\"}', '{\"regulation_name\":\"AOB rules\",\"description\":\"test\"}', '::1', 'Regulation updated.', '2026-08-12 20:11:43'),
(51, 1, 2, 'Regulations', 'CREATE', 'Regulation #2', NULL, '{\"regulation_name\":\"Audit regulations\",\"description\":\"testing\"}', '::1', 'New regulation created.', '2026-08-12 20:12:02'),
(52, 1, 2, 'Regulations', 'DELETE', 'Regulation #1', '{\"id\":1,\"regulation_name\":\"AOB rules\",\"description\":\"test\"}', NULL, '::1', 'Regulation deleted.', '2026-08-12 20:12:22'),
(53, 1, 2, 'Case Regulations', 'CREATE', 'Case Regulation #1', NULL, '{\"case_id\":1,\"regulation_id\":2}', '::1', 'Regulation linked to case.', '2026-08-12 20:17:41'),
(54, 1, 2, 'Case Attachments', 'CREATE', 'Attachment #1', NULL, '{\"case_id\":1,\"file_name\":\"test.pdf\",\"category\":\"Correspondence\",\"document_date\":\"2026-08-13\",\"version\":\"1.0\"}', '::1', 'Case attachment uploaded.', '2026-08-12 20:24:02'),
(55, 1, 2, 'Case Status History', 'CREATE', 'History #1', NULL, '{\"case_id\":1,\"old_status_id\":null,\"new_status_id\":1,\"remarks\":\"\",\"changed_by\":1}', '::1', 'Case status history entry created.', '2026-08-12 20:27:47'),
(56, 1, 2, 'Users', 'CREATE', 'User #4', NULL, '{\"username\":\"ali@gmail.com\",\"official_email\":\"ali@gmail.com\",\"status\":\"pending\"}', '::1', 'New user account created.', '2026-08-13 13:00:23'),
(57, 1, 2, 'Users', 'UPDATE', 'User #4', '{\"id\":4,\"user_code\":\"1005\",\"full_name\":\"ali\",\"username\":\"ali@gmail.com\",\"official_email\":\"ali@gmail.com\",\"mobile_number\":\"12345678\",\"designation\":\"ABC\",\"department_id\":5,\"role_id\":1,\"reporting_officer_id\":1,\"password_hash\":\"$2y$10$Hnozhh3I7OTjyUFfKgG8\\/ODKqj8tFMH3RjEZiS5gv254tDiZa8G2W\",\"status\":\"pending\",\"last_login_at\":null,\"created_at\":\"2026-08-13 18:00:23\",\"updated_at\":\"2026-08-13 18:00:23\",\"account_expiry_date\":null,\"must_change_password\":1,\"send_activation_email\":0,\"two_factor_enabled\":0,\"office_location\":\"\",\"signature_block\":\"\",\"last_password_changed_at\":null}', '{\"username\":\"ali@gmail.com\",\"official_email\":\"ali@gmail.com\",\"status\":\"active\",\"role_id\":1}', '::1', 'User account updated.', '2026-08-13 13:03:42'),
(58, 1, 2, 'Case Stages', 'UPDATE', 'Stage #1', '{\"stage_name\":\"Initial\",\"description\":\"testing\"}', '{\"stage_name\":\"Initial\",\"description\":\"testing\"}', '::1', 'Case stage updated', '2026-08-13 18:20:30');

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `id` int(11) NOT NULL,
  `case_number` varchar(100) NOT NULL,
  `firm_id` int(11) NOT NULL,
  `case_title` varchar(255) NOT NULL,
  `case_type` varchar(100) DEFAULT NULL,
  `regulation_violated` text,
  `assigned_officer` varchar(150) DEFAULT NULL,
  `court_name` varchar(200) DEFAULT NULL,
  `priority` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `status` enum('Draft','Open','Under Review','Show Cause Issued','Order Issued','Referred to High Court','Referred to Supreme Court','Closed') DEFAULT 'Draft',
  `has_court_order` tinyint(1) DEFAULT '0',
  `next_followup_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cases`
--

INSERT INTO `cases` (`id`, `case_number`, `firm_id`, `case_title`, `case_type`, `regulation_violated`, `assigned_officer`, `court_name`, `priority`, `status`, `has_court_order`, `next_followup_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'AOB/TEST/2026/001', 1, 'Test Case - Audit Documentation Review', 'Enforcement', 'AOB Act and applicable audit regulations', 'javed', 'Islamabad High Court', 'High', 'Open', 0, '2026-08-15', 1, '2026-08-08 21:02:11', '2026-08-10 23:57:27');

-- --------------------------------------------------------

--
-- Table structure for table `case_attachments`
--

CREATE TABLE `case_attachments` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `description` text,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `case_attachments`
--

INSERT INTO `case_attachments` (`id`, `case_id`, `file_name`, `file_path`, `category`, `document_date`, `version`, `description`, `uploaded_by`, `uploaded_at`) VALUES
(1, 1, 'test.pdf', 'uploads/case_attachments/1786566242_6a7cd662b125d_test.pdf', 'Correspondence', '2026-08-13', '1.0', 'testing', 1, '2026-08-12 20:24:02');

-- --------------------------------------------------------

--
-- Table structure for table `case_followups`
--

CREATE TABLE `case_followups` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `followup_date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `assigned_officer` varchar(150) DEFAULT NULL,
  `priority` enum('Low','Medium','High','Critical') DEFAULT 'Medium',
  `status` enum('Pending','In Progress','Completed','Overdue') DEFAULT 'Pending',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `case_followups`
--

INSERT INTO `case_followups` (`id`, `case_id`, `followup_date`, `title`, `description`, `assigned_officer`, `priority`, `status`, `created_by`, `created_at`) VALUES
(1, 1, '2026-08-10', 'Test Follow-up Updated', 'Review submitted audit documents and prepare observation', 'ali', 'High', 'Pending', 1, '2026-08-10 18:39:49');

-- --------------------------------------------------------

--
-- Table structure for table `case_regulations`
--

CREATE TABLE `case_regulations` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `regulation_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `case_regulations`
--

INSERT INTO `case_regulations` (`id`, `case_id`, `regulation_id`, `created_at`) VALUES
(1, 1, 2, '2026-08-12 20:17:41');

-- --------------------------------------------------------

--
-- Table structure for table `case_stages`
--

CREATE TABLE `case_stages` (
  `id` int(11) NOT NULL,
  `stage_name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `case_stages`
--

INSERT INTO `case_stages` (`id`, `stage_name`, `description`, `created_at`) VALUES
(1, 'Initial', 'testing', '2026-08-12 19:34:25'),
(5, 'Initial Review', 'Testing', '2026-08-12 19:40:42');

-- --------------------------------------------------------

--
-- Table structure for table `case_statuses`
--

CREATE TABLE `case_statuses` (
  `id` int(11) NOT NULL,
  `status_name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `case_statuses`
--

INSERT INTO `case_statuses` (`id`, `status_name`, `description`, `created_at`) VALUES
(1, 'Under Review', 'Testing functionality', '2026-08-12 19:51:37');

-- --------------------------------------------------------

--
-- Table structure for table `case_status_history`
--

CREATE TABLE `case_status_history` (
  `id` int(11) NOT NULL,
  `case_id` int(11) NOT NULL,
  `old_status_id` int(11) DEFAULT NULL,
  `new_status_id` int(11) NOT NULL,
  `remarks` text,
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `case_status_history`
--

INSERT INTO `case_status_history` (`id`, `case_id`, `old_status_id`, `new_status_id`, `remarks`, `changed_by`, `changed_at`) VALUES
(1, 1, NULL, 1, '', 1, '2026-08-12 20:27:47');

-- --------------------------------------------------------

--
-- Table structure for table `case_types`
--

CREATE TABLE `case_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `case_types`
--

INSERT INTO `case_types` (`id`, `type_name`, `description`, `created_at`) VALUES
(1, 'Audit appeal', 'testing', '2026-08-12 19:58:21');

-- --------------------------------------------------------

--
-- Table structure for table `courts`
--

CREATE TABLE `courts` (
  `id` int(11) NOT NULL,
  `court_name` varchar(150) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `courts`
--

INSERT INTO `courts` (`id`, `court_name`, `city`, `created_at`) VALUES
(1, 'Lahore High Court', 'Lahore', '2026-08-10 19:09:08'),
(2, 'Islamabad High Court', 'Islamabad', '2026-08-10 19:09:08'),
(3, 'Sindh High Court', 'Karachi', '2026-08-10 19:09:08'),
(4, 'Peshawar High Court', 'Peshawar', '2026-08-10 19:09:08'),
(5, 'Balochistan High Court', 'Quetta', '2026-08-10 19:09:08');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(150) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `description`, `created_at`) VALUES
(1, 'Legal Affairs', 'Handles legal matters and court proceedings', '2026-08-05 12:54:46'),
(2, 'Enforcement', 'Manages enforcement cases and regulatory actions', '2026-08-05 12:54:46'),
(3, 'Inspections', 'Handles audit firm inspections', '2026-08-05 12:54:46'),
(4, 'Audit Quality Review', 'Manages audit quality reviews', '2026-08-05 12:54:46'),
(5, 'Registration', 'Handles firm registration and related activities', '2026-08-05 12:54:46');

-- --------------------------------------------------------

--
-- Table structure for table `document_tags`
--

CREATE TABLE `document_tags` (
  `id` int(11) NOT NULL,
  `tag_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `document_tags`
--

INSERT INTO `document_tags` (`id`, `tag_name`, `created_at`) VALUES
(1, 'Court Order', '2026-08-10 20:21:28'),
(2, 'Judgment', '2026-08-10 20:21:28'),
(3, 'Notice', '2026-08-10 20:21:28'),
(4, 'Evidence', '2026-08-10 20:21:28'),
(5, 'Petition', '2026-08-10 20:21:28'),
(6, 'Contract', '2026-08-10 20:21:28'),
(7, 'Correspondence', '2026-08-10 20:21:28');

-- --------------------------------------------------------

--
-- Table structure for table `document_tag_mapping`
--

CREATE TABLE `document_tag_mapping` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `document_tag_mapping`
--

INSERT INTO `document_tag_mapping` (`id`, `document_id`, `tag_id`) VALUES
(1, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`id`, `type_name`, `description`, `created_at`) VALUES
(1, 'Court Order', 'Orders issued by a court', '2026-08-10 19:08:24'),
(2, 'Judgment', 'Court judgments and decisions', '2026-08-10 19:08:24'),
(3, 'Legal Notice', 'Formal legal notices', '2026-08-10 19:08:24'),
(4, 'Case Document', 'General documents related to a legal case', '2026-08-10 19:08:24'),
(5, 'Evidence', 'Evidence submitted in a legal case', '2026-08-10 19:08:24'),
(6, 'Correspondence', 'Official legal correspondence', '2026-08-10 19:08:24');

-- --------------------------------------------------------

--
-- Table structure for table `document_versions`
--

CREATE TABLE `document_versions` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `version` varchar(50) NOT NULL DEFAULT '1.0',
  `version_number` varchar(20) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `remarks` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `document_versions`
--

INSERT INTO `document_versions` (`id`, `document_id`, `version`, `version_number`, `file_name`, `file_path`, `uploaded_by`, `remarks`, `created_at`) VALUES
(1, 3, '1.1', '2', 'Model of Engagement.pdf', 'uploads/documents/versions/1786392750_b029f8269a724d92.pdf', 1, 'legal document', '2026-08-10 20:12:30');

-- --------------------------------------------------------

--
-- Table structure for table `firms`
--

CREATE TABLE `firms` (
  `id` int(11) NOT NULL,
  `firm_code` varchar(50) NOT NULL,
  `ntn_number` varchar(50) NOT NULL,
  `firm_name` varchar(255) NOT NULL,
  `official_email` varchar(255) NOT NULL,
  `landline` varchar(50) DEFAULT NULL,
  `principal_contact_person` varchar(150) NOT NULL,
  `aob_representative` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `status` enum('Under Review','Active','Inactive') DEFAULT 'Under Review',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `firms`
--

INSERT INTO `firms` (`id`, `firm_code`, `ntn_number`, `firm_name`, `official_email`, `landline`, `principal_contact_person`, `aob_representative`, `city`, `province`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'FRM-0001', '1111111-1', 'ABC Firm', 'abc@gmail.com', '042-1111111', 'Ali', 'Ahmed', 'Lahore', 'Punjab', 'Active', 1, '2026-08-03 19:19:23', '2026-08-03 19:19:23'),
(2, 'FRM-0002', '2222222-2', 'XYZ Firm', 'xyz@gmail.com', '051-2222222', 'Sara', 'Usman', 'Islamabad', 'Islamabad', 'Under Review', 1, '2026-08-03 19:19:23', '2026-08-03 19:19:23'),
(4, 'FRM-0004', '1234567-8', 'Test Audit & Co.', 'testaudit@gmail.com', '041-12345678', 'Sara', 'Ali', 'Lahore', 'Punjab', 'Active', 1, '2026-08-10 19:06:55', '2026-08-10 19:06:55');

-- --------------------------------------------------------

--
-- Table structure for table `legal_documents`
--

CREATE TABLE `legal_documents` (
  `id` int(11) NOT NULL,
  `document_number` varchar(100) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `document_type_id` int(11) NOT NULL,
  `firm_id` int(11) DEFAULT NULL,
  `case_id` int(11) DEFAULT NULL,
  `court_id` int(11) DEFAULT NULL,
  `version` varchar(20) DEFAULT 'v1.0',
  `ocr_status` enum('Pending','Processing','Completed','Requires Review','Failed','Not Required') DEFAULT 'Pending',
  `extracted_text` longtext,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` varchar(50) DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `extracted_json` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `legal_documents`
--

INSERT INTO `legal_documents` (`id`, `document_number`, `document_name`, `document_type_id`, `firm_id`, `case_id`, `court_id`, `version`, `ocr_status`, `extracted_text`, `file_name`, `file_path`, `file_size`, `document_date`, `uploaded_by`, `description`, `created_at`, `updated_at`, `extracted_json`) VALUES
(2, 'DOC/AOB/2026/001', 'Review order', 1, 4, 1, 1, '1.0', 'Completed', '===== PAGE 1 =====\n\nWHAT IS A TEXT\n\nA text is a set of signs whose\npurpose is to convey a message,\na statement or a coherent set of\nstatements, whether oral or\nwritten.', '1786390633_6a7a2869a6b45.pdf', '../uploads/documents/1786390633_6a7a2869a6b45.pdf', '44841', '2026-08-11', NULL, 'Review document', '2026-08-10 19:37:13', '2026-08-12 12:39:56', NULL),
(3, 'DOC/AOB/2026/002', 'Audit', 5, 2, 1, 2, '1.1', 'Not Required', '1. Community Development and Social Cohesion \nMeaning of Community Development \nCommunity development is a process in which community members come \ntogether to take collective action and generate solutions to common problems. \nIt focuses on improving the social, economic, cultural, and environmental \nconditions of a community. \nIt encourages: \n• Participation of local people \n• Capacity building \n• Sustainable development \n• Empowerment of marginalized groups \nKey Objectives \n1. Improve quality of life \n2. Reduce poverty and inequality \n3. Strengthen community participation \n4. Promote sustainable development \n5. Encourage collective decision-making \n \nMeaning of Social Cohesion \nSocial cohesion refers to the strength of relationships and the sense of solidarity \namong members of a community or society. \nA socially cohesive society has: \n• Mutual trust \n• Respect for diversity \n• Equality of opportunities\n\n• Peaceful coexistence \nStrong social cohesion helps societies reduce conflict and build unity. \n \nRelationship between Community Development and Social Cohesion \nCommunity development and social cohesion are closely connected. \nCommunity development: \n• Encourages participation \n• Builds trust \n• Creates shared goals \nThese elements help build social cohesion in society. \nExample: \nWhen people work together to solve community problems, their relationships \nbecome stronger, which leads to unity and cooperation. \n \nKey Elements of Community Development \n1. Participation \nActive involvement of community members in decision-making and development \nactivities. \nExample: \nIn Bangladesh, the Grameen Bank involves poor women in microfinance programs \nwhere they participate in financial decisions and community activities. \n \n2. Empowerment \nEmpowerment means giving people the ability and confidence to control their own \nlives.\n\nExample: \nIn India, women’s Self-Help Groups (SHGs) empower rural women by providing \nmicrocredit and training. \n \n3. Capacity Building \nDeveloping the skills, knowledge, and resources of individuals and communities. \nExample: \nIn Pakistan, the Akhuwat Foundation provides interest-free loans and training to \nhelp communities start businesses. \n \n4. Sustainability \nEnsuring development initiatives continue to benefit communities in the long term. \nExample: \nIn Kenya, community-based water projects are managed by local residents to \nensure sustainable access to clean water. \n \n5. Inclusiveness \nCommunity development should include all groups, especially marginalized \npeople. \nExample: \nIn Canada, community development policies promote inclusion of indigenous \ncommunities and immigrants. \n \nBenefits of Community Development \n1. Reduces poverty \n2. Improves education and health services \n3. Strengthens social relationships\n\n4. Promotes democracy and participation \n5. Enhances social cohesion \nExample: \nCommunity development programs in Rwanda helped rebuild trust and unity after \nthe Rwandan Genocide by encouraging community cooperation. \n \n2. Approaches to Effective Community Engagement \nMeaning of Community Engagement \nCommunity engagement is the process of involving community members in \nplanning, decision-making, and implementing projects that affect their lives. \nIt creates a partnership between communities, organizations, and governments. \n \nMajor Approaches to Community Engagement \n1. Participatory Approach \nThis approach encourages community members to actively participate in \nidentifying problems and creating solutions. \nKey Features: \n• Shared decision making \n• Community ownership \n• Empowerment \nExample: \nParticipatory Rural Appraisal (PRA) methods are widely used in Nepal to involve \nvillagers in development planning.\n\n2. Asset-Based Community Development (ABCD) \nThis approach focuses on existing strengths and resources within a community \nrather than problems. \nAssets may include: \n• Skills of residents \n• Local organizations \n• Natural resources \n• Cultural traditions \nExample: \nIn United States, many neighborhoods use ABCD to improve local services by \nmobilizing community volunteers and organizations. \n \n3. Community-Based Approach \nThis approach ensures that development projects are designed and managed by the \ncommunity itself. \nExample: \nIn Pakistan, the Rural Support Programs Network helps rural communities \norganize village organizations to manage development projects. \n \n4. Collaborative Approach \nIn this approach, government, NGOs, private sector, and communities work \ntogether. \nBenefits: \n• Shared resources \n• Better decision-making \n• Stronger implementation\n\nExample: \nIn United Kingdom, local councils collaborate with community groups to improve \nurban neighborhoods. \n \n5. Advocacy Approach \nAdvocacy involves raising awareness and influencing policies to protect \ncommunity rights. \nExample: \nEnvironmental organizations in Brazil advocate for the protection of the Amazon \nRainforest and indigenous communities. \n \n6. Digital Community Engagement \nTechnology is increasingly used to involve communities in decision-making. \nExamples: \n• Online surveys \n• Social media campaigns \n• Virtual community meetings \nExample: \nDuring the COVID-19 Pandemic, governments in South Korea used digital \nplatforms to communicate with citizens and involve them in public health \nmeasures. \n \nChallenges in Community Engagement \n1. Lack of trust between communities and authorities \n2. Limited resources \n3. Cultural differences \n4. Low participation levels\n\n5. Political interference \nExample: \nIn many developing countries, rural communities face limited access to \ninformation, which affects their participation in development projects. \n \nStrategies to Improve Community Engagement \n1. Build trust with communities \n2. Ensure transparency in decision-making \n3. Provide education and awareness programs \n4. Encourage inclusive participation \n5. Strengthen partnerships between government and NGOs \nExample: \nIn Finland, local governments regularly involve citizens in policymaking through \npublic consultations and participatory budgeting.', '1786391434_e139f53d52a94eba.pdf', 'uploads/documents/1786391434_e139f53d52a94eba.pdf', '157054', '2026-08-11', NULL, 'review document', '2026-08-10 19:50:34', '2026-08-10 20:12:30', NULL),
(6, 'DOC/AOB/2026/006', 'abc', 3, 1, 1, 3, '1.5', 'Completed', '===== PAGE 1 =====\n\nModel of Engagement\n1. INFORM (Lowest Level of Engagement)\nDefinition\n\nProviding information to the community so they understand an issue, decision, or\nproject.\n\nKey Characteristics\n\n¢ One-way communication\n\n¢ No feedback required\n\n« Community has no role in decision-making\nTools & Methods\n\n« Posters\n\n« Social media announcements\n\n« TV/radio messages\n\n« Public notices\nExample (Pakistan)\n\n- Government awareness campaigns about dengue prevention\n\ne« COVID-19 SOPs shared through media\nAdvantages\n\n¢ Quick and cost-effective\n\n¢ Useful in emergencies\nLimitations\n\n« Nocommunity participation\n\n¢ People may ignore or misunderstand information\n\n\n===== PAGE 2 =====\n\n2. CONSULT\nDefinition\nSeeking opinions, feedback, or suggestions from the community.\nKey Characteristics\n¢ Two-way communication (limited)\n¢ Final decision still made by authorities\nTools & Methods\ne Surveys\n¢ Public meetings\n« Interviews\n« Feedback forms\nExample\n¢ Education departments taking feedback from teachers and parents\n¢ Local government asking citizens about development priorities\nAdvantages\n« Community feels heard\n¢ Better decision-making\nLimitations\n¢ Feedback may not be used\n\n¢ Can create frustration if ignored\n\n\n===== PAGE 3 =====\n\n3. INVOLVE\nDefinition\nWorking directly with the community during planning and implementation.\nKey Characteristics\n¢ Active participation\n¢« Community input is considered\n¢ Shared responsibility (partial)\nTools & Methods\ne Workshops\n« Focus groups\n- Community meetings\n¢ Participatory planning\nExample\n¢ The Citizens Foundation\no Involves parents in school management\n¢ Local communities helping design water supply systems\nAdvantages\n¢ Builds trust\n« Better solutions\nLimitations\n¢ Time-consuming\n\n¢ Requires coordination\n\n\n===== PAGE 4 =====\n\n4. COLLABORATE\nDefinition\n\nA partnership where community and organizations share decision-making\npower.\n\nKey Characteristics\n¢ Strong two-way communication\n¢ Joint planning and implementation\ne Shared responsibility\nTools & Methods\n¢ Joint committees\n« Partnerships (NGOs + community)\n- Co-management systems\nExample\ne National Rural Support Programme\no Works with village organizations for development\n¢ NGOs working with communities on sanitation and education projects\nAdvantages\n¢ High trust and ownership\n« Sustainable outcomes\nLimitations\n¢ Requires strong coordination\n\n¢ Possible conflicts between stakeholders\n\n\n===== PAGE 5 =====\n\n5. EMPOWER (Highest Level of Engagement)\nDefinition\n\nGiving full decision-making power to the community.\n\nKey Characteristics\n¢« Community controls decisions\n« Authorities act as facilitators\n¢ Maximum participation\nTools & Methods\n¢« Community-led organizations\n- Local committees managing funds\n¢ Self-governance\nExample\n« Akhuwat Foundation\no Communities manage loan distribution\n¢ Village committees deciding development priorities\nAdvantages\n¢ Strong ownership\n- Long-term sustainability\n¢ Builds leadership\nLimitations\n¢ Requires capacity building\n\n¢ Risk of mismanagement if training is lacking', '1786537683_5e9a0dec41abce00.pdf', 'uploads/documents/1786537683_5e9a0dec41abce00.pdf', '223072', '2026-08-11', NULL, 'testing', '2026-08-12 12:28:03', '2026-08-12 12:51:12', NULL),
(7, 'DOC/AOB/2026/007', 'abc', 6, 4, 1, 4, '1.0', 'Completed', '===== PAGE 1 =====\n\nWHAT IS A TEXT\nA text is a set of signs whose\npurpose is to convey a message,\na statement or a coherent set of\nstatements, whether oral or\nwritten.', '1786539267_8d3159615c9f167c.pdf', 'uploads/documents/1786539267_8d3159615c9f167c.pdf', '44841', '2026-08-11', NULL, '', '2026-08-12 12:54:27', '2026-08-12 18:31:35', '{\n    \"document_id\": 7,\n    \"file_name\": \"1786539267_8d3159615c9f167c.pdf\",\n    \"document_type\": \"Scanned PDF\",\n    \"ocr_applied\": true,\n    \"ocr_status\": \"Completed\",\n    \"pages_processed\": 1,\n    \"extracted_text\": \"===== PAGE 1 =====\\n\\nWHAT IS A TEXT\\nA text is a set of signs whose\\npurpose is to convey a message,\\na statement or a coherent set of\\nstatements, whether oral or\\nwritten.\"\n}');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `permission_name` varchar(150) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_name`, `description`, `created_at`) VALUES
(1, 'View Dashboard', 'Access dashboard information', '2026-08-05 12:55:17'),
(2, 'Manage Users', 'Create, update and manage users', '2026-08-05 12:55:17'),
(3, 'Manage Firms', 'Create and update audit firms', '2026-08-05 12:55:17'),
(4, 'Manage Cases', 'Create and manage legal cases', '2026-08-05 12:55:17'),
(5, 'Manage Follow-ups', 'Manage case follow-ups', '2026-08-05 12:55:17'),
(6, 'View Documents', 'View legal documents', '2026-08-05 12:55:17'),
(7, 'Upload Documents', 'Upload legal documents', '2026-08-05 12:55:17'),
(8, 'Manage Documents', 'Update and delete documents', '2026-08-05 12:55:17'),
(9, 'View Reports', 'Access reports and exports', '2026-08-05 12:55:17');

-- --------------------------------------------------------

--
-- Table structure for table `regulations`
--

CREATE TABLE `regulations` (
  `id` int(11) NOT NULL,
  `regulation_name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `regulations`
--

INSERT INTO `regulations` (`id`, `regulation_name`, `description`, `created_at`) VALUES
(2, 'Audit regulations', 'testing', '2026-08-12 20:12:02');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `created_at`) VALUES
(1, 'Super Administrator', 'Full system access with all permissions', '2026-08-05 12:55:05'),
(2, 'Administrator', 'Manages users, firms and system data', '2026-08-05 12:55:05'),
(3, 'Legal Officer', 'Handles legal cases and documents', '2026-08-05 12:55:05'),
(4, 'Case Officer', 'Manages assigned cases and follow-ups', '2026-08-05 12:55:05'),
(5, 'Reviewer', 'Reviews cases and documents', '2026-08-05 12:55:05'),
(6, 'Read-only User', 'Can only view permitted information', '2026-08-05 12:55:05');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`) VALUES
(1, 1, 4, '2026-08-05 12:55:33'),
(2, 1, 8, '2026-08-05 12:55:33'),
(3, 1, 3, '2026-08-05 12:55:33'),
(4, 1, 5, '2026-08-05 12:55:33'),
(5, 1, 2, '2026-08-05 12:55:33'),
(6, 1, 7, '2026-08-05 12:55:33'),
(7, 1, 1, '2026-08-05 12:55:33'),
(8, 1, 6, '2026-08-05 12:55:33'),
(9, 1, 9, '2026-08-05 12:55:33'),
(16, 2, 1, '2026-08-05 12:56:37'),
(17, 2, 2, '2026-08-05 12:56:37'),
(18, 2, 3, '2026-08-05 12:56:37'),
(19, 2, 4, '2026-08-05 12:56:37'),
(20, 2, 6, '2026-08-05 12:56:37'),
(21, 2, 7, '2026-08-05 12:56:37'),
(22, 2, 8, '2026-08-05 12:56:37'),
(23, 2, 9, '2026-08-05 12:56:37'),
(24, 3, 1, '2026-08-05 12:56:58'),
(25, 3, 4, '2026-08-05 12:56:58'),
(26, 3, 5, '2026-08-05 12:56:58'),
(27, 3, 6, '2026-08-05 12:56:58'),
(28, 3, 7, '2026-08-05 12:56:58'),
(29, 4, 1, '2026-08-05 12:57:53'),
(30, 4, 4, '2026-08-05 12:57:53'),
(31, 4, 5, '2026-08-05 12:57:53'),
(32, 4, 6, '2026-08-05 12:57:53'),
(33, 5, 1, '2026-08-05 12:58:05'),
(34, 5, 6, '2026-08-05 12:58:05'),
(35, 5, 9, '2026-08-05 12:58:05'),
(36, 6, 1, '2026-08-05 12:58:19'),
(37, 6, 6, '2026-08-05 12:58:19');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `organisation_name` varchar(255) NOT NULL,
  `short_name` varchar(50) DEFAULT NULL,
  `official_email` varchar(150) DEFAULT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `time_zone` varchar(100) DEFAULT NULL,
  `default_department_id` int(11) DEFAULT NULL,
  `registered_address` text,
  `minimum_password_length` int(11) DEFAULT '12',
  `password_expiry_days` int(11) DEFAULT '90',
  `failed_attempts_before_lockout` int(11) DEFAULT '3',
  `session_timeout_minutes` int(11) DEFAULT '20',
  `default_role_id` int(11) DEFAULT NULL,
  `require_two_factor_auth` tinyint(1) DEFAULT '0',
  `restrict_network_access` tinyint(1) DEFAULT '0',
  `maximum_file_size_mb` int(11) DEFAULT '25',
  `allowed_file_types` varchar(255) DEFAULT NULL,
  `default_document_type_id` int(11) DEFAULT NULL,
  `record_retention_years` int(11) DEFAULT '7',
  `run_ocr_on_upload` tinyint(1) DEFAULT '1',
  `keep_version_history` tinyint(1) DEFAULT '1',
  `case_assignment_alerts` tinyint(1) DEFAULT '1',
  `followup_due_reminders` tinyint(1) DEFAULT '1',
  `daily_digest_email` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `organisation_name`, `short_name`, `official_email`, `contact_number`, `time_zone`, `default_department_id`, `registered_address`, `minimum_password_length`, `password_expiry_days`, `failed_attempts_before_lockout`, `session_timeout_minutes`, `default_role_id`, `require_two_factor_auth`, `restrict_network_access`, `maximum_file_size_mb`, `allowed_file_types`, `default_document_type_id`, `record_retention_years`, `run_ocr_on_upload`, `keep_version_history`, `case_assignment_alerts`, `followup_due_reminders`, `daily_digest_email`, `created_at`, `updated_at`) VALUES
(1, 'Audit oversight board', 'AOB', 'admin@aob.com', '12345678', 'Asia/Karachi', NULL, 'AOB Head Office, Karachi', 12, 90, 3, 20, NULL, 0, 0, 20, '0', NULL, 7, 0, 1, 1, 1, 0, '2026-08-12 18:59:49', '2026-08-12 19:12:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_code` varchar(50) DEFAULT NULL,
  `full_name` varchar(200) DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `official_email` varchar(150) NOT NULL,
  `mobile_number` varchar(30) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `reporting_officer_id` int(11) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('active','inactive','locked','pending') DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `account_expiry_date` date DEFAULT NULL,
  `must_change_password` tinyint(1) DEFAULT '1',
  `send_activation_email` tinyint(1) DEFAULT '0',
  `two_factor_enabled` tinyint(1) DEFAULT '0',
  `office_location` varchar(255) DEFAULT NULL,
  `signature_block` text,
  `last_password_changed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_code`, `full_name`, `username`, `official_email`, `mobile_number`, `designation`, `department_id`, `role_id`, `reporting_officer_id`, `password_hash`, `status`, `last_login_at`, `created_at`, `updated_at`, `account_expiry_date`, `must_change_password`, `send_activation_email`, `two_factor_enabled`, `office_location`, `signature_block`, `last_password_changed_at`) VALUES
(1, NULL, NULL, 'admin', 'admin@aob.com', NULL, NULL, NULL, 2, NULL, '$2y$10$0ufwS3hHGRPhJIggOadij.h9DebuN7rqKtXwx1ffu6Wwqar5ooYlK', 'active', NULL, '2026-08-03 14:09:23', '2026-08-12 12:04:13', NULL, 1, 0, 0, NULL, NULL, NULL),
(2, 'USR-1001', 'Faisal Mehmood', 'faisal', 'faisal@aob.gov.pk', '03001234567', 'Deputy Director (Legal)', 1, 1, NULL, 'hashed_password_here', 'active', NULL, '2026-08-05 13:00:13', '2026-08-05 13:00:13', NULL, 1, 0, 0, NULL, NULL, NULL),
(3, 'USR-1002', 'Bilal Ahmed Qureshi', 'bilal', 'bilal@aob.gov.pk', '03007654321', 'Assistant Director (Legal)', 1, 3, NULL, 'hashed_password_here', 'active', NULL, '2026-08-05 13:00:13', '2026-08-05 13:00:13', NULL, 1, 0, 0, NULL, NULL, NULL),
(4, '1005', 'ali', 'ali@gmail.com', 'ali@gmail.com', '12345678', 'ABC', 5, 1, 1, '$2y$10$Hnozhh3I7OTjyUFfKgG8/ODKqj8tFMH3RjEZiS5gv254tDiZa8G2W', 'active', NULL, '2026-08-13 13:00:23', '2026-08-13 13:03:42', NULL, 1, 0, 0, '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_activity_logs_role` (`role_id`);

--
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_number` (`case_number`),
  ADD KEY `firm_id` (`firm_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `case_attachments`
--
ALTER TABLE `case_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `case_followups`
--
ALTER TABLE `case_followups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `case_regulations`
--
ALTER TABLE `case_regulations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `regulation_id` (`regulation_id`);

--
-- Indexes for table `case_stages`
--
ALTER TABLE `case_stages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stage_name` (`stage_name`);

--
-- Indexes for table `case_statuses`
--
ALTER TABLE `case_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_name` (`status_name`);

--
-- Indexes for table `case_status_history`
--
ALTER TABLE `case_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `old_status_id` (`old_status_id`),
  ADD KEY `new_status_id` (`new_status_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `case_types`
--
ALTER TABLE `case_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `courts`
--
ALTER TABLE `courts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `court_name` (`court_name`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `document_tags`
--
ALTER TABLE `document_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tag_name` (`tag_name`);

--
-- Indexes for table `document_tag_mapping`
--
ALTER TABLE `document_tag_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `firms`
--
ALTER TABLE `firms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `firm_code` (`firm_code`),
  ADD UNIQUE KEY `ntn_number` (`ntn_number`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_number` (`document_number`),
  ADD KEY `document_type_id` (`document_type_id`),
  ADD KEY `firm_id` (`firm_id`),
  ADD KEY `case_id` (`case_id`),
  ADD KEY `court_id` (`court_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_name` (`permission_name`);

--
-- Indexes for table `regulations`
--
ALTER TABLE `regulations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `regulation_name` (`regulation_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `default_department_id` (`default_department_id`),
  ADD KEY `default_role_id` (`default_role_id`),
  ADD KEY `default_document_type_id` (`default_document_type_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`official_email`),
  ADD UNIQUE KEY `official_email` (`official_email`),
  ADD UNIQUE KEY `user_code` (`user_code`),
  ADD KEY `fk_users_department` (`department_id`),
  ADD KEY `fk_users_role` (`role_id`),
  ADD KEY `fk_users_reporting_officer` (`reporting_officer_id`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `case_attachments`
--
ALTER TABLE `case_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `case_followups`
--
ALTER TABLE `case_followups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `case_regulations`
--
ALTER TABLE `case_regulations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `case_stages`
--
ALTER TABLE `case_stages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `case_statuses`
--
ALTER TABLE `case_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `case_status_history`
--
ALTER TABLE `case_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `case_types`
--
ALTER TABLE `case_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courts`
--
ALTER TABLE `courts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `document_tags`
--
ALTER TABLE `document_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `document_tag_mapping`
--
ALTER TABLE `document_tag_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `firms`
--
ALTER TABLE `firms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `legal_documents`
--
ALTER TABLE `legal_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `regulations`
--
ALTER TABLE `regulations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_activity_logs_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `cases`
--
ALTER TABLE `cases`
  ADD CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`firm_id`) REFERENCES `firms` (`id`),
  ADD CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `case_attachments`
--
ALTER TABLE `case_attachments`
  ADD CONSTRAINT `case_attachments_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  ADD CONSTRAINT `case_attachments_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `case_followups`
--
ALTER TABLE `case_followups`
  ADD CONSTRAINT `case_followups_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  ADD CONSTRAINT `case_followups_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `case_regulations`
--
ALTER TABLE `case_regulations`
  ADD CONSTRAINT `case_regulations_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  ADD CONSTRAINT `case_regulations_ibfk_2` FOREIGN KEY (`regulation_id`) REFERENCES `regulations` (`id`);

--
-- Constraints for table `case_status_history`
--
ALTER TABLE `case_status_history`
  ADD CONSTRAINT `case_status_history_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  ADD CONSTRAINT `case_status_history_ibfk_2` FOREIGN KEY (`old_status_id`) REFERENCES `case_statuses` (`id`),
  ADD CONSTRAINT `case_status_history_ibfk_3` FOREIGN KEY (`new_status_id`) REFERENCES `case_statuses` (`id`),
  ADD CONSTRAINT `case_status_history_ibfk_4` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `document_tag_mapping`
--
ALTER TABLE `document_tag_mapping`
  ADD CONSTRAINT `document_tag_mapping_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `legal_documents` (`id`),
  ADD CONSTRAINT `document_tag_mapping_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `document_tags` (`id`);

--
-- Constraints for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD CONSTRAINT `document_versions_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `legal_documents` (`id`),
  ADD CONSTRAINT `document_versions_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `firms`
--
ALTER TABLE `firms`
  ADD CONSTRAINT `firms_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `legal_documents`
--
ALTER TABLE `legal_documents`
  ADD CONSTRAINT `legal_documents_ibfk_1` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`),
  ADD CONSTRAINT `legal_documents_ibfk_2` FOREIGN KEY (`firm_id`) REFERENCES `firms` (`id`),
  ADD CONSTRAINT `legal_documents_ibfk_3` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`),
  ADD CONSTRAINT `legal_documents_ibfk_4` FOREIGN KEY (`court_id`) REFERENCES `courts` (`id`),
  ADD CONSTRAINT `legal_documents_ibfk_5` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD CONSTRAINT `system_settings_ibfk_1` FOREIGN KEY (`default_department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `system_settings_ibfk_2` FOREIGN KEY (`default_role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `system_settings_ibfk_3` FOREIGN KEY (`default_document_type_id`) REFERENCES `document_types` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `fk_users_reporting_officer` FOREIGN KEY (`reporting_officer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
