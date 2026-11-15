-- JobBridge | Local development database (MySQL 8+ / MariaDB)
CREATE DATABASE IF NOT EXISTS jobbridge CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jobbridge;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('candidate','employer') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS jobs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employer_id INT UNSIGNED NOT NULL,
  title VARCHAR(120) NOT NULL,
  company VARCHAR(120) NOT NULL,
  location VARCHAR(120) NOT NULL,
  category ENUM('Technology','Design','Marketing','Finance','Business','Operations','Other') NOT NULL,
  job_type ENUM('Full-time','Part-time','Internship','Contract') NOT NULL,
  work_mode ENUM('On-site','Hybrid','Remote') NOT NULL,
  salary_min INT UNSIGNED DEFAULT NULL,
  salary_max INT UNSIGNED DEFAULT NULL,
  description TEXT NOT NULL,
  requirements TEXT NULL,
  status ENUM('open','closed') NOT NULL DEFAULT 'open',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_job_employer FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_jobs_status_date(status, created_at),
  INDEX idx_jobs_category(category),
  INDEX idx_jobs_employer(employer_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS applications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  job_id INT UNSIGNED NOT NULL,
  candidate_id INT UNSIGNED NOT NULL,
  cover_letter TEXT NULL,
  status ENUM('submitted','reviewed','shortlisted','rejected') NOT NULL DEFAULT 'submitted',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_app_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
  CONSTRAINT fk_app_candidate FOREIGN KEY (candidate_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_application(job_id, candidate_id),
  INDEX idx_app_candidate(candidate_id),
  INDEX idx_app_job(job_id)
) ENGINE=InnoDB;
