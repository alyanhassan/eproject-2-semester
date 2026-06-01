-- ============================================================
-- VAXORA — Reviews Table Patch
-- Run this in phpMyAdmin on your vaxora_db database
-- (Only needed if you already imported vaxora.sql before)
-- ============================================================

USE `vaxora_db`;

CREATE TABLE IF NOT EXISTS `reviews` (
  `id`          INT(11)     NOT NULL AUTO_INCREMENT,
  `parent_id`   INT(11)     DEFAULT NULL,
  `name`        VARCHAR(100) NOT NULL,
  `email`       VARCHAR(150) NOT NULL,
  `location`    VARCHAR(100) DEFAULT NULL,
  `rating`      TINYINT(1)  NOT NULL DEFAULT 5,
  `review_text` TEXT        NOT NULL,
  `status`      ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at`  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`parent_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample approved reviews
INSERT INTO `reviews` (`name`, `email`, `location`, `rating`, `review_text`, `status`) VALUES
('Ayesha Mahmood',  'ayesha.m@example.com',  'Karachi',   5, 'VAXORA made scheduling my children''s MMR vaccines incredibly easy. The hospital was professional, staff was kind, and the whole process was seamless from booking to the actual vaccination.', 'approved'),
('Dr. Usman Tariq', 'usman.t@example.com',   'Islamabad', 5, 'As a general practitioner, I actively recommend VAXORA to all my patients. The vaccine information is accurate and up-to-date, and the hospital network is genuinely excellent.', 'approved'),
('Sara Ahmed',      'sara.a@example.com',    'Lahore',    5, 'Got my Hepatitis B booster through VAXORA. Booked online in two minutes and walked into the clinic with zero waiting time. An incredibly professional service.', 'approved'),
('Bilal Hussain',   'bilal.h@example.com',   'Karachi',   4, 'Great platform overall. The hospital search and filtering made it very easy to find a nearby certified center. Appointment was approved within hours.', 'approved');
