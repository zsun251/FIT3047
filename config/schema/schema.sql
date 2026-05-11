-- =============================================================================
-- RespiraCare Online — Database Schema
-- =============================================================================
-- Project:     RespiraCare Online
-- Description: Complete database schema including tables, constraints, and triggers
--
-- Usage:
--   mysql -u username -p database_name < config/schema/schema.sql
--
-- Notes:
--   - This file contains ONLY table structures, constraints, and triggers
--   - No data inserts - see seed.sql for development data
--   - Safe to run multiple times (uses DROP TABLE IF EXISTS)
-- =============================================================================

-- =============================================================================
-- DROP EXISTING TABLES
-- =============================================================================

-- 1. Drop junction/linking tables and tables with most dependencies first
DROP TABLE IF EXISTS vital_data_symptoms;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS appointments_reminders;
DROP TABLE IF EXISTS vitals_reminders;

-- 2. Drop tables that depend on patients/clinicians/appointments
DROP TABLE IF EXISTS reminders;
DROP TABLE IF EXISTS vital_data;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS clinicians_patients;
DROP TABLE IF EXISTS symptoms;

-- 5. Drop type-specific tables (admins, clinicians, patients)
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS clinicians;
DROP TABLE IF EXISTS patients;

-- 6. Drop users last
DROP TABLE IF EXISTS users;



-- =============================================================================
-- USER & USER TYPES
-- =============================================================================

CREATE TABLE users (
                       id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                       username        VARCHAR(51)     NOT NULL,
                       password        VARCHAR(255)    NOT NULL,
                       first_name      VARCHAR(100)    NOT NULL,
                       last_name       VARCHAR(100)    NOT NULL,
                       email           VARCHAR(255)    NOT NULL,
                       phone           VARCHAR(15)     NULL,
                       address         VARCHAR(500)    NULL,
                       date_of_birth   DATE            NULL,
                       type            VARCHAR(20)     NOT NULL,
                       reset_token VARCHAR(255) NULL,
                       reset_token_expires DATETIME NULL,
                       created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

                       PRIMARY KEY (id),
                       UNIQUE KEY uq_users_username (username),
                       UNIQUE KEY uq_users_email (email),
                       CONSTRAINT chk_users_type
                           CHECK (type IN ('admin','clinician','patient'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE admins (
                        id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                        user_id     INT UNSIGNED    NOT NULL,

                        PRIMARY KEY (id),
                        UNIQUE KEY uq_admins_user_id (user_id),
                        KEY user_id (user_id),

                        CONSTRAINT fk_admins_user
                            FOREIGN KEY (user_id)
                                REFERENCES users(id)
                                ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE clinicians (
                            id                  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                            user_id             INT UNSIGNED    NOT NULL,

                            PRIMARY KEY (id),
                            UNIQUE KEY uq_clinicians_user_id (user_id),
                            KEY user_id (user_id),

                            CONSTRAINT fk_clinicians_user
                                FOREIGN KEY (user_id)
                                    REFERENCES users(id)
                                    ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE patients (
                          id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                          user_id     INT UNSIGNED    NOT NULL,

                          PRIMARY KEY (id),
                          UNIQUE KEY uq_patients_user_id (user_id),
                          KEY user_id (user_id),

                          CONSTRAINT fk_patients_user
                              FOREIGN KEY (user_id)
                                  REFERENCES users(id)
                                  ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================================================
-- DROP OLD TRIGGERS
-- =============================================================================
DROP TRIGGER IF EXISTS tr_users_after_insert;
DROP TRIGGER IF EXISTS tr_users_after_update;
DROP TRIGGER IF EXISTS tr_admins_before_delete;
DROP TRIGGER IF EXISTS tr_clinicians_before_delete;
DROP TRIGGER IF EXISTS tr_patients_before_delete;
DROP TRIGGER IF EXISTS tr_admins_before_insert;
DROP TRIGGER IF EXISTS tr_admins_before_update;
DROP TRIGGER IF EXISTS tr_clinicians_before_insert;
DROP TRIGGER IF EXISTS tr_clinicians_before_update;
DROP TRIGGER IF EXISTS tr_patients_before_insert;
DROP TRIGGER IF EXISTS tr_patients_before_update;

-- =============================================================================
-- TRIGGER SET - users, admins, patients and clinicians.
-- THESE MAY NEED TO BE REVISED AS REQUIREMENTS CHANGE
-- The associated tables may also need to be revised is requirements remain simple
-- i.e no extra attributes specific to a patient, admin or clinician. However, this is unlikely. - Mark
-- =============================================================================
DELIMITER $$

-- 1. Auto-create type record when user is created
CREATE TRIGGER tr_users_after_insert
    AFTER INSERT ON users
    FOR EACH ROW
BEGIN
    CASE NEW.type
        WHEN 'admin' THEN
            INSERT INTO admins (user_id) VALUES (NEW.id);
    WHEN 'clinician' THEN
            INSERT INTO clinicians (user_id) VALUES (NEW.id);
    WHEN 'patient' THEN
            INSERT INTO patients (user_id) VALUES (NEW.id);
END CASE;
END$$

-- 2. Handle type changes when user is updated
CREATE TRIGGER tr_users_after_update
    AFTER UPDATE ON users
    FOR EACH ROW
BEGIN
    IF OLD.type != NEW.type THEN
        -- Delete old type record
        CASE OLD.type
            WHEN 'admin' THEN
    DELETE FROM admins WHERE user_id = OLD.id;
    WHEN 'clinician' THEN
    DELETE FROM clinicians WHERE user_id = OLD.id;
    WHEN 'patient' THEN
    DELETE FROM patients WHERE user_id = OLD.id;
END CASE;

-- Create new type record
CASE NEW.type
            WHEN 'admin' THEN
                INSERT INTO admins (user_id) VALUES (NEW.id);
WHEN 'clinician' THEN
                INSERT INTO clinicians (user_id) VALUES (NEW.id);
WHEN 'patient' THEN
                INSERT INTO patients (user_id) VALUES (NEW.id);
END CASE;
END IF;
END$$

-- 3. Validate type match on admin insert
CREATE TRIGGER tr_admins_before_insert
    BEFORE INSERT ON admins
    FOR EACH ROW
BEGIN
    DECLARE user_type VARCHAR(20);
    SELECT type INTO user_type FROM users WHERE id = NEW.user_id;

    IF user_type IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot create admin record: User does not exist';
    ELSEIF user_type != 'admin' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot create admin record: User type is not admin';
END IF;
END$$

-- 4. Validate type match on clinician insert
CREATE TRIGGER tr_clinicians_before_insert
    BEFORE INSERT ON clinicians
    FOR EACH ROW
BEGIN
    DECLARE user_type VARCHAR(20);
    SELECT type INTO user_type FROM users WHERE id = NEW.user_id;

    IF user_type IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot create clinician record: User does not exist';
    ELSEIF user_type != 'clinician' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot create clinician record: User type is not clinician';
END IF;
END$$

-- 5. Validate type match on patient insert
CREATE TRIGGER tr_patients_before_insert
    BEFORE INSERT ON patients
    FOR EACH ROW
BEGIN
    DECLARE user_type VARCHAR(20);
    SELECT type INTO user_type FROM users WHERE id = NEW.user_id;

    IF user_type IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot create patient record: User does not exist';
    ELSEIF user_type != 'patient' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot create patient record: User type is not patient';
END IF;
END$$

DELIMITER ;

DELIMITER $$

-- Prevent updating user_id in type tables
CREATE TRIGGER tr_admins_before_update
    BEFORE UPDATE ON admins
    FOR EACH ROW
BEGIN
    IF OLD.user_id != NEW.user_id THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot change user_id. Delete this record and create a new one instead.';
END IF;
END$$

CREATE TRIGGER tr_clinicians_before_update
    BEFORE UPDATE ON clinicians
    FOR EACH ROW
BEGIN
    IF OLD.user_id != NEW.user_id THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot change user_id. Delete this record and create a new one instead.';
END IF;
END$$

CREATE TRIGGER tr_patients_before_update
    BEFORE UPDATE ON patients
    FOR EACH ROW
BEGIN
    IF OLD.user_id != NEW.user_id THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot change user_id. Delete this record and create a new one instead.';
END IF;
END$$

DELIMITER ;

-- =============================================================================
-- CLINICIAN PATIENT JOIN
-- =============================================================================

CREATE TABLE clinicians_patients (
                                     id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                                     clinician_id    INT UNSIGNED    NOT NULL,
                                     patient_id      INT UNSIGNED    NOT NULL,
                                     assigned_at     DATE            NOT NULL DEFAULT (CURRENT_DATE),
                                     unassigned_at   DATE            NULL,
                                     status          VARCHAR(10)     NOT NULL DEFAULT 'active',

                                     PRIMARY KEY (id),

                                     KEY clinician_id (clinician_id),
                                     KEY patient_id (patient_id),

                                     CONSTRAINT chk_clinicians_patients_status
                                         CHECK (status IN ('active','inactive')),

                                     CONSTRAINT fk_cp_clinician
                                         FOREIGN KEY (clinician_id)
                                             REFERENCES clinicians(id),

                                     CONSTRAINT fk_cp_patient
                                         FOREIGN KEY (patient_id)
                                             REFERENCES patients(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- =============================================================================
-- APPOINTMENTS
-- =============================================================================

CREATE TABLE appointments (
                              id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                              patient_id      INT UNSIGNED    NOT NULL,
                              clinician_id    INT UNSIGNED    NOT NULL,
                              scheduled_at    DATETIME        NOT NULL,
                              duration        SMALLINT        NOT NULL,
                              status          VARCHAR(20)     NOT NULL DEFAULT 'upcoming',

                              PRIMARY KEY (id),

                              KEY patient_id (patient_id),
                              KEY clinician_id (clinician_id),

                              CONSTRAINT chk_appointments_duration
                                  CHECK (duration > 0),

                              CONSTRAINT chk_appointments_status
                                  CHECK (status IN ('upcoming','completed','cancelled')),

                              CONSTRAINT fk_appointments_patient
                                  FOREIGN KEY (patient_id)
                                      REFERENCES patients(id),

                              CONSTRAINT fk_appointments_clinician
                                  FOREIGN KEY (clinician_id)
                                      REFERENCES clinicians(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- =============================================================================
-- VITAL DATA & SYMPTOMS
-- =============================================================================

-- The way each vital is store will need to be refined
CREATE TABLE vital_data (
                            id                  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                            patient_id          INT UNSIGNED    NOT NULL,
                            oxygen_saturation   DECIMAL(5, 2)   NULL,
                            respiratory_rate    SMALLINT        NULL,             -- breaths per minute
                            blood_pressure      VARCHAR(10)     NULL,             -- 'systolic/diastolic' e.g. '120/80'
                            pulse_oximetry      DECIMAL(5, 2)   NULL,
                            weight              DECIMAL(6, 2)   NULL,             -- kilograms
                            recorded_at         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

                            PRIMARY KEY (id),
                            KEY patient_id (patient_id),

                            CONSTRAINT chk_oxygen_saturation
                                CHECK (oxygen_saturation IS NULL OR oxygen_saturation BETWEEN 0 AND 100),
                            CONSTRAINT chk_respiratory_rate
                                CHECK (respiratory_rate IS NULL OR respiratory_rate > 0),
                            CONSTRAINT chk_pulse_oximetry
                                CHECK (pulse_oximetry IS NULL OR pulse_oximetry > 0),
                            CONSTRAINT chk_weight
                                CHECK (weight IS NULL OR weight > 0),

                            CONSTRAINT fk_vital_data_patient
                                FOREIGN KEY (patient_id)
                                    REFERENCES patients(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE symptoms (
                          id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                          symptom_name    VARCHAR(150)    NOT NULL,
                          description     TEXT            NULL,

                          PRIMARY KEY (id),
                          UNIQUE KEY uq_symptoms_name (symptom_name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE vital_data_symptoms (

                                     id             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                                     vital_data_id  INT UNSIGNED    NOT NULL,
                                     symptom_id     INT UNSIGNED    NOT NULL,
                                     severity       VARCHAR(10)     NOT NULL,
                                     notes          TEXT,

                                     PRIMARY KEY (id),

                                     KEY vital_data_id (vital_data_id),
                                     KEY symptom_id (symptom_id),

                                     CONSTRAINT fk_vds_vital
                                         FOREIGN KEY (vital_data_id)
                                             REFERENCES vital_data(id)
                                             ON DELETE CASCADE,

                                     CONSTRAINT fk_vds_symptom
                                         FOREIGN KEY (symptom_id)
                                             REFERENCES symptoms(id),

                                     CONSTRAINT chk_vds_severity
                                         CHECK (severity IN ('mild','moderate','severe'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================================================
-- REMINDERS
-- =============================================================================

CREATE TABLE reminders (
                           id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                           message         TEXT            NOT NULL,
                           scheduled_at    DATETIME        NOT NULL,
                           sent_at         DATETIME        NULL,
                           created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           status          VARCHAR(10)     NOT NULL DEFAULT 'pending',
                           delivery_method VARCHAR(10)     NOT NULL,

                           interacted_at   DATETIME        NULL,
                           interaction_type VARCHAR(20)    NULL,
                           vital_data_id   INT UNSIGNED    NULL,

                           PRIMARY KEY (id),
                           KEY vital_data_id (vital_data_id),

                           CONSTRAINT chk_reminders_status
                               CHECK (status IN ('pending','sent')),

                           CONSTRAINT chk_reminders_delivery
                               CHECK (delivery_method IN ('sms','email','system')),

                           CONSTRAINT chk_reminder_interaction
                               CHECK (interaction_type IS NULL OR interaction_type IN ('dismissed', 'completed')),

                           CONSTRAINT fk_reminders_vital_data
                               FOREIGN KEY (vital_data_id)
                                   REFERENCES vital_data(id)
                                   ON DELETE SET NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE appointments_reminders (
                                        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                                        reminder_id     INT UNSIGNED    NOT NULL,
                                        appointment_id  INT UNSIGNED    NOT NULL,

                                        PRIMARY KEY (id),

                                        KEY reminder_id (reminder_id),
                                        KEY appointment_id (appointment_id),

                                        CONSTRAINT fk_ar_reminder
                                            FOREIGN KEY (reminder_id)
                                                REFERENCES reminders(id)
                                                ON DELETE CASCADE,

                                        CONSTRAINT fk_ar_appointment
                                            FOREIGN KEY (appointment_id)
                                                REFERENCES appointments(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE vitals_reminders (
                                  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                                  reminder_id     INT UNSIGNED    NOT NULL,
                                  patient_id      INT UNSIGNED    NOT NULL,

                                  recurrence_pattern VARCHAR(20)  NULL,
                                  time_slot_1     TIME            NULL,
                                  time_slot_2     TIME            NULL,
                                  time_slot_3     TIME            NULL,
                                  time_slot_4     TIME            NULL,

                                  is_active       BOOLEAN         NOT NULL DEFAULT TRUE,
                                  email_enabled   BOOLEAN         NOT NULL DEFAULT TRUE,

                                  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                                  PRIMARY KEY (id),

                                  KEY reminder_id (reminder_id),
                                  KEY patient_id (patient_id),

                                  CONSTRAINT fk_vr_reminder
                                      FOREIGN KEY (reminder_id)
                                          REFERENCES reminders(id)
                                          ON DELETE CASCADE,

                                  CONSTRAINT fk_vr_patient
                                      FOREIGN KEY (patient_id)
                                          REFERENCES patients(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================================================
-- RESOURCES
-- =============================================================================

CREATE TABLE resources (
                           id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                           clinician_id    INT UNSIGNED    NOT NULL,
                           title           VARCHAR(255)    NOT NULL,
                           type            VARCHAR(20)     NOT NULL,
                           body            TEXT            NULL,                              -- NULL for link/video types
                           url             TEXT            NULL,                              -- NULL for article/blog types
                           thumbnail_url   TEXT            NULL,
                           status          VARCHAR(10)     NOT NULL DEFAULT 'draft',
                           published_at    DATETIME        NULL,                              -- NULL until published
                           created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                           PRIMARY KEY (id),
                           KEY clinician_id (clinician_id),

                           CONSTRAINT chk_resource_type
                               CHECK (type IN ('article', 'video', 'blog', 'link', 'news')),
                           CONSTRAINT chk_resource_status
                               CHECK (status IN ('draft', 'published')),

                           CONSTRAINT fk_resources_clinician
                               FOREIGN KEY (clinician_id)
                                   REFERENCES clinicians(id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================================================
-- CONTACT MESSAGES
-- =============================================================================

CREATE TABLE contact_messages (
                                  id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
                                  first_name     VARCHAR(100) NOT NULL,
                                  last_name      VARCHAR(100) NULL,
                                  contact_number VARCHAR(15) NULL,
                                  email           VARCHAR(255)    NOT NULL,
                                  message         TEXT            NOT NULL,
                                  status          VARCHAR(10)     NOT NULL DEFAULT 'unread',
                                  submitted_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  reviewed_by     INT UNSIGNED    NULL,                              -- NULL until an admin reviews
                                  reviewed_at     DATETIME        NULL,                              -- NULL until reviewed

                                  PRIMARY KEY (id),
                                  KEY reviewed_by (reviewed_by),

                                  CONSTRAINT chk_contact_status
                                      CHECK (status IN ('unread', 'read', 'archived')),
                                  CONSTRAINT chk_reviewed_consistency
                                      CHECK (
                                          (reviewed_by IS NULL AND reviewed_at IS NULL) OR
                                          (reviewed_by IS NOT NULL AND reviewed_at IS NOT NULL)
                                          ),

                                  CONSTRAINT fk_contact_user
                                      FOREIGN KEY (reviewed_by)
                                          REFERENCES users(id)
);

