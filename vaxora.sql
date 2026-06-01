-- ============================================================
-- VAXORA — Vaccination Booking System
-- Database: vaxora_db
-- phpMyAdmin compatible SQL dump
-- ============================================================

CREATE DATABASE IF NOT EXISTS `vaxora_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vaxora_db`;

-- ----------------------------
-- Table: users
-- ----------------------------
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','parent','hospital') NOT NULL DEFAULT 'parent',
  `phone` VARCHAR(20) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table: hospitals
-- ----------------------------
CREATE TABLE `hospitals` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `name` VARCHAR(200) NOT NULL,
  `address` TEXT DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `rating` DECIMAL(2,1) NOT NULL DEFAULT '4.5',
  `services` TEXT DEFAULT NULL,
  `hours` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table: children
-- ----------------------------
CREATE TABLE `children` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `parent_id` INT(11) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `dob` DATE NOT NULL,
  `gender` ENUM('male','female','other') NOT NULL,
  `blood_group` VARCHAR(10) DEFAULT NULL,
  `weight` DECIMAL(5,2) DEFAULT NULL,
  `height` DECIMAL(5,2) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`parent_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table: vaccines
-- ----------------------------
CREATE TABLE `vaccines` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `type` VARCHAR(100) DEFAULT NULL,
  `doses` INT(11) NOT NULL DEFAULT '1',
  `age_group` VARCHAR(100) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `side_effects` TEXT DEFAULT NULL,
  `duration` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('available','unavailable') NOT NULL DEFAULT 'available',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table: appointments
-- ----------------------------
CREATE TABLE `appointments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `parent_id` INT(11) NOT NULL,
  `child_id` INT(11) NOT NULL,
  `hospital_id` INT(11) NOT NULL,
  `vaccine_id` INT(11) NOT NULL,
  `appointment_date` DATE NOT NULL,
  `appointment_time` TIME DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `status` ENUM('pending','approved','rejected','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`parent_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`child_id`) REFERENCES `children`(`id`),
  FOREIGN KEY (`hospital_id`) REFERENCES `hospitals`(`id`),
  FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table: vaccination_records
-- ----------------------------
CREATE TABLE `vaccination_records` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `appointment_id` INT(11) DEFAULT NULL,
  `child_id` INT(11) NOT NULL,
  `vaccine_id` INT(11) NOT NULL,
  `hospital_id` INT(11) DEFAULT NULL,
  `date_given` DATE DEFAULT NULL,
  `next_due_date` DATE DEFAULT NULL,
  `dose_number` INT(11) NOT NULL DEFAULT '1',
  `status` ENUM('completed','missed','pending') NOT NULL DEFAULT 'pending',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`appointment_id`) REFERENCES `appointments`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`child_id`) REFERENCES `children`(`id`),
  FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines`(`id`),
  FOREIGN KEY (`hospital_id`) REFERENCES `hospitals`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table: contacts
-- ----------------------------
CREATE TABLE `contacts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SAMPLE DATA
-- ============================================================

-- Admin user (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `city`) VALUES
('VAXORA Admin', 'admin@vaxora.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '0800-829672', 'Karachi');

-- Hospital users (password: hospital123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `city`, `address`) VALUES
('Aga Khan University Hospital', 'agakhan@hospital.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hospital', '(021) 111-911-911', 'Karachi', 'Stadium Road, Karachi'),
('Liaquat National Hospital', 'liaquat@hospital.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hospital', '(021) 111-456-789', 'Karachi', 'Karachi'),
('Shaukat Khanum Memorial', 'shaukat@hospital.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hospital', '042-35945100', 'Lahore', 'Johar Town, Lahore'),
('Shifa International Hospital', 'shifa@hospital.pk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hospital', '051-846-3000', 'Islamabad', 'H-8, Islamabad');

-- Parent users (password: parent123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`, `city`, `address`) VALUES
('Ayesha Khan', 'ayesha@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent', '0300-1234567', 'Karachi', 'Block 5, Clifton, Karachi'),
('Sara Ahmed', 'sara@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent', '0311-9876543', 'Lahore', 'DHA Phase 5, Lahore');

-- Hospitals
INSERT INTO `hospitals` (`user_id`, `name`, `address`, `city`, `phone`, `email`, `rating`, `services`, `hours`, `status`) VALUES
(2, 'Aga Khan University Hospital', 'Stadium Road, Karachi', 'Karachi', '(021) 111-911-911', 'agakhan@hospital.pk', 4.9, 'All vaccines, Pediatric & Adult, Travel vaccines, HPV, COVID booster', '24/7', 'active'),
(3, 'Liaquat National Hospital', 'National Stadium Road, Karachi', 'Karachi', '(021) 111-456-789', 'liaquat@hospital.pk', 4.7, 'Pediatric vaccines, Routine immunization', 'Mon-Sat 8am-8pm', 'active'),
(4, 'Shaukat Khanum Memorial Cancer Hospital', 'Johar Town, Lahore', 'Lahore', '042-35945100', 'shaukat@hospital.pk', 4.8, 'HPV & cancer-prevention vaccines, Pediatric vaccines', 'Mon-Sat 8am-6pm', 'active'),
(5, 'Shifa International Hospital', 'H-8, Islamabad', 'Islamabad', '051-846-3000', 'shifa@hospital.pk', 4.8, 'Full vaccine menu, International travel clinic', 'Mon-Sun 8am-10pm', 'active'),
(NULL, 'Indus Hospital', 'Korangi, Karachi', 'Karachi', '(021) 35112709', 'indus@hospital.pk', 4.8, 'Free vaccines for children, EPI vaccines', 'Mon-Sat 8am-4pm', 'active'),
(NULL, 'South City Hospital', 'Clifton, Karachi', 'Karachi', '(021) 35361601', 'southcity@hospital.pk', 4.6, 'Travel vaccines, Adult immunization', 'Mon-Sat 9am-7pm', 'active'),
(NULL, 'PIMS Hospital', 'G-8, Islamabad', 'Islamabad', '051-9261170', 'pims@hospital.pk', 4.6, 'All EPI vaccines, Travel vaccines', 'Mon-Fri 8am-2pm', 'active'),
(NULL, 'Doctors Hospital Lahore', 'Canal Road, Lahore', 'Lahore', '042-35761000', 'doctors@hospital.pk', 4.7, 'Full pediatric & adult vaccination', 'Mon-Sat 9am-8pm', 'active');

-- Vaccines
INSERT INTO `vaccines` (`name`, `type`, `doses`, `age_group`, `description`, `side_effects`, `duration`, `status`) VALUES
('COVID-19 Vaccine (Pfizer/Moderna)', 'mRNA', 3, '12+ years', 'Protection against COVID-19 virus. 2 primary doses plus a booster recommended.', 'Mild arm soreness, fatigue, low fever', '6-12 months with booster', 'available'),
('Influenza (Flu) Vaccine', 'Inactivated', 1, '6 months+', 'Annual seasonal flu vaccine recommended Oct-March. Especially for elderly, children, pregnant women.', 'Mild soreness at injection site', 'Seasonal (annual)', 'available'),
('MMR Vaccine (Measles, Mumps, Rubella)', 'Live-attenuated', 2, '12 months+', 'Prevents measles, mumps and rubella. First dose 12-15 months, second dose 4-6 years.', 'Mild rash, low-grade fever', 'Lifelong after 2 doses', 'available'),
('Hepatitis B Vaccine', 'Recombinant', 3, 'Newborns - Adults', 'Three-dose series at 0, 1 and 6 months. Critical for healthcare workers, travelers, newborns.', 'Soreness at injection site', 'Lifelong', 'available'),
('Typhoid Vaccine (TCV)', 'Conjugate', 1, '9 months+', 'Especially important in Pakistan endemic regions. Single dose provides 3-5 years protection.', 'Mild fever, headache', '3-5 years', 'available'),
('Varicella (Chickenpox) Vaccine', 'Live-attenuated', 2, '12-18 months+', 'First dose 12-18 months, second dose 4-6 years.', 'Mild rash, fever', 'Lifelong after 2 doses', 'available'),
('HPV Vaccine (Gardasil 9)', 'Recombinant', 3, '9-45 years', 'Prevents cervical cancer and genital warts. 2-3 dose schedule depending on age.', 'Injection site pain, mild fever', 'Lifelong', 'available'),
('Meningococcal Vaccine', 'Conjugate', 2, '2 months+', 'Critical for Hajj pilgrims. 1-2 doses depending on age.', 'Injection site pain', '3-5 years', 'available'),
('Rabies Vaccine', 'Inactivated', 5, 'All ages', 'Pre and post-exposure prophylaxis. Required after animal bites or travel to endemic regions.', 'Headache, nausea, dizziness', 'Varies', 'available'),
('Pneumococcal Vaccine (PCV13)', 'Conjugate', 4, '2 months - elderly', 'Prevents pneumonia, meningitis, bloodstream infections. 4 doses for infants, 1-2 for adults.', 'Mild fever, soreness', 'Lifelong', 'available'),
('BCG Vaccine', 'Live-attenuated', 1, 'Newborns', 'Tuberculosis prevention. Given at birth.', 'Small scar at injection site', 'Lifelong', 'available'),
('OPV (Oral Polio Vaccine)', 'Live-attenuated', 4, '0-5 years', 'Oral polio drops. Given at birth, 6, 10, 14 weeks and boosters.', 'Rarely mild fever', 'Lifelong after full schedule', 'available'),
('DTP Vaccine', 'Inactivated', 5, '6 weeks+', 'Diphtheria, Tetanus, Pertussis. Given at 6, 10, 14 weeks plus boosters.', 'Fever, swelling at site', 'Boosters required', 'available'),
('Rotavirus Vaccine', 'Live-attenuated', 2, '6-24 weeks', 'Prevents severe diarrhea. 2-3 oral doses.', 'Mild diarrhea, vomiting', 'Childhood protection', 'available');

-- Children (linked to parent users: user_id 6 = Ayesha, user_id 7 = Sara)
INSERT INTO `children` (`parent_id`, `name`, `dob`, `gender`, `blood_group`, `weight`, `height`) VALUES
(6, 'Ali Khan', '2023-03-15', 'male', 'B+', 12.5, 80.0),
(6, 'Fatima Khan', '2021-07-20', 'female', 'O+', 18.0, 95.0),
(7, 'Omar Ahmed', '2022-11-05', 'male', 'A+', 14.0, 82.0);

-- Sample Appointments
INSERT INTO `appointments` (`parent_id`, `child_id`, `hospital_id`, `vaccine_id`, `appointment_date`, `appointment_time`, `status`, `notes`) VALUES
(6, 1, 1, 3, '2026-05-20', '10:00:00', 'approved', 'MMR first dose'),
(6, 2, 2, 2, '2026-05-22', '11:30:00', 'pending', 'Annual flu shot'),
(7, 3, 3, 5, '2026-05-25', '09:00:00', 'approved', 'Typhoid vaccine'),
(6, 1, 1, 4, '2026-06-10', '10:00:00', 'completed', 'Hepatitis B first dose');

-- Vaccination Records
INSERT INTO `vaccination_records` (`appointment_id`, `child_id`, `vaccine_id`, `hospital_id`, `date_given`, `next_due_date`, `dose_number`, `status`, `notes`) VALUES
(4, 1, 4, 1, '2026-04-10', '2026-05-10', 1, 'completed', 'First dose given successfully'),
(1, 1, 3, 1, '2026-03-01', NULL, 1, 'completed', 'MMR dose 1 completed');

-- Reviews
CREATE TABLE IF NOT EXISTS `reviews` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `parent_id`   INT(11)      DEFAULT NULL,
  `name`        VARCHAR(100) NOT NULL,
  `email`       VARCHAR(150) NOT NULL,
  `location`    VARCHAR(100) DEFAULT NULL,
  `rating`      TINYINT(1)   NOT NULL DEFAULT 5,
  `review_text` TEXT         NOT NULL,
  `status`      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`parent_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample approved reviews
INSERT INTO `reviews` (`name`, `email`, `location`, `rating`, `review_text`, `status`) VALUES
('Ayesha Mahmood',  'ayesha.m@example.com',  'Karachi',   5, 'VAXORA made scheduling my children''s MMR vaccines incredibly easy. The hospital was professional, staff was kind, and the whole process was seamless from booking to the actual vaccination.', 'approved'),
('Dr. Usman Tariq', 'usman.t@example.com',   'Islamabad', 5, 'As a general practitioner, I actively recommend VAXORA to all my patients. The vaccine information is accurate and up-to-date, and the hospital network is genuinely excellent.', 'approved'),
('Sara Ahmed',      'sara.a@example.com',    'Lahore',    5, 'Got my Hepatitis B booster through VAXORA. Booked online in two minutes and walked into the clinic with zero waiting time. An incredibly professional service.', 'approved'),
('Bilal Hussain',   'bilal.h@example.com',   'Karachi',   4, 'Great platform overall. The hospital search and filtering made it very easy to find a nearby certified center. Appointment was approved within hours.', 'approved');
