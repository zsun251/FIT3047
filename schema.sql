-- RespiraCare Online — Database Schema

-- =============================================================================
-- DROP
-- =============================================================================

DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS resources;
DROP TABLE IF EXISTS vital_data_symptoms;
DROP TABLE IF EXISTS symptoms;
DROP TABLE IF EXISTS vital_data;
DROP TABLE IF EXISTS vitals_reminders;
DROP TABLE IF EXISTS appointment_reminders;
DROP TABLE IF EXISTS appointments;
DROP TABLE IF EXISTS admins;
DROP TABLE IF EXISTS clinician_patients;
DROP TABLE IF EXISTS patients;
DROP TABLE IF EXISTS clinicians;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS reminders;



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
    phone           VARCHAR(20)     NULL,
    address         TEXT            NULL,
    date_of_birth   DATE            NULL,
    type            VARCHAR(20)     NOT NULL,
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
     KEY user_id (user_id),

     CONSTRAINT fk_patients_user
         FOREIGN KEY (user_id)
             REFERENCES users(id)
             ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


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
-- REMINDERS
-- =============================================================================

CREATE TABLE reminders (
      id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
      message         TEXT            NOT NULL,
      scheduled_at    DATETIME        NOT NULL,
      sent_at         DATETIME        NULL,               -- NULL until delivered
      created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
      status          VARCHAR(10)     NOT NULL DEFAULT 'pending',
      delivery_method VARCHAR(10)     NOT NULL,

      PRIMARY KEY (id),

      CONSTRAINT chk_reminders_status
          CHECK (status IN ('pending','sent')),

      CONSTRAINT chk_reminders_delivery
          CHECK (delivery_method IN ('sms','email','system'))

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
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    reminder_id     INT UNSIGNED    NOT NULL,
    patient_id      INT UNSIGNED    NOT NULL,
    -- Add attributes to facilitate recurring reminders.


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
-- VITAL DATA & SYMPTOMS
-- =============================================================================

-- The way each vital is store will need to be refined
CREATE TABLE vital_data (
       id                  INT UNSIGNED    NOT NULL AUTO_INCREMENT,
       patient_id          INT UNSIGNED    NOT NULL,
       oxygen_saturation   DECIMAL(5, 2)   NULL,
       respiratory_rate    SMALLINT        NULL,             -- breaths per minute
       lung_function       DECIMAL(5, 2)   NULL,
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
       CONSTRAINT chk_lung_function
           CHECK (lung_function IS NULL OR lung_function BETWEEN 0 AND 100),
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
        contact_number VARCHAR(50) NULL,
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


-- =============================================================================
-- USER INSERTS
-- =============================================================================
-- These inserts use cakephp default hasher for password.
-- The first insert is as login credentials is as follows:
-- username: admin1
-- password: password
-- The other inserts use the same password.

INSERT INTO users
(username, password, first_name, last_name, email, phone, address, date_of_birth, type)
VALUES
    (
        'admin1',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'System',
        'Admin',
        'admin@example.com',
        '0400000000',
        '1 Admin Street',
        '1990-01-01',
        'admin'
    ),
    (
        'clinician1',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'John',
        'Doctor',
        'clinician@example.com',
        '0411111111',
        '2 Medical Ave',
        '1985-05-10',
        'clinician'
    ),
    (
        'patient1',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'Jane',
        'Doe',
        'patient@example.com',
        '0422222222',
        '3 Patient Road',
        '2000-09-15',
        'patient'
    );


-- =============================================================================
-- CONTACT MESSAGE INSERTS
-- =============================================================================
INSERT INTO contact_messages
(first_name, last_name, contact_number, email, message, status, submitted_at, reviewed_by, reviewed_at)
VALUES
    ('Michael', 'Green', '0411222333', 'michael.green@example.com', 'I need help with my account.', 'unread', NOW(), NULL, NULL),
    ('Samantha', 'White', '0422333444', 'samantha.white@example.com', 'Can you provide details about your pricing?', 'unread', NOW(), NULL, NULL),
    ('David', 'Taylor', '0433444555', 'david.taylor@example.com', 'I am interested in your premium package.', 'unread', NOW(), NULL, NULL),
    ('Emma', 'Harris', '0444555666', 'emma.harris@example.com', 'How do I reset my password?', 'unread', NOW(), NULL, NULL),
    ('Oliver', 'Martin', '0455666777', 'oliver.martin@example.com', 'I have a billing question.', 'unread', NOW(), NULL, NULL),
    ('Sophia', 'Lee', '0466777888', 'sophia.lee@example.com', 'Can I get a demo of your software?', 'unread', NOW(), NULL, NULL),
    ('Liam', 'Walker', '0477888999', 'liam.walker@example.com', 'Do you offer discounts for students?', 'unread', NOW(), NULL, NULL),
    ('Isabella', 'King', '0488999000', 'isabella.king@example.com', 'I would like to know your office hours.', 'unread', NOW(), NULL, NULL),
    ('Noah', 'Wright', '0412341122', 'noah.wright@example.com', 'Can you help me with an error I received?', 'unread', NOW(), NULL, NULL),
    ('Mia', 'Scott', '0423452233', 'mia.scott@example.com', 'I want to change my subscription plan.', 'unread', NOW(), NULL, NULL),
    ('Ethan', 'Adams', '0434563344', 'ethan.adams@example.com', 'How long does shipping take?', 'unread', NOW(), NULL, NULL),
    ('Ava', 'Baker', '0445674455', 'ava.baker@example.com', 'Can you provide a quote for services?', 'unread', NOW(), NULL, NULL),
    ('James', 'Carter', '0456785566', 'james.carter@example.com', 'I need technical support.', 'unread', NOW(), NULL, NULL),
    ('Charlotte', 'Mitchell', '0467896677', 'charlotte.mitchell@example.com', 'Do you offer corporate plans?', 'unread', NOW(), NULL, NULL),
    ('Benjamin', 'Perez', '0478907788', 'benjamin.perez@example.com', 'I have feedback on your website.', 'unread', NOW(), NULL, NULL);
INSERT INTO contact_messages
(first_name, last_name, contact_number, email, message, status, submitted_at, reviewed_by, reviewed_at)
VALUES
    ('Daniel', 'Evans', '0411223344', 'daniel.evans@example.com', 'I would like information about your services.', 'unread', NOW(), NULL, NULL),
    ('Grace', 'Collins', '0422334455', 'grace.collins@example.com', 'Can you explain the subscription options?', 'unread', NOW(), NULL, NULL),
    ('Matthew', 'Turner', '0433445566', 'matthew.turner@example.com', 'I need help accessing my account.', 'unread', NOW(), NULL, NULL),
    ('Chloe', 'Parker', '0444556677', 'chloe.parker@example.com', 'How do I cancel my subscription?', 'unread', NOW(), NULL, NULL),
    ('Ryan', 'Murphy', '0455667788', 'ryan.murphy@example.com', 'I received an invoice, can you clarify?', 'unread', NOW(), NULL, NULL),
    ('Ella', 'Bell', '0466778899', 'ella.bell@example.com', 'Do you have an enterprise plan?', 'unread', NOW(), NULL, NULL),
    ('Joshua', 'Cooper', '0477889900', 'joshua.cooper@example.com', 'I need to update my payment details.', 'unread', NOW(), NULL, NULL),
    ('Lily', 'Reed', '0488990011', 'lily.reed@example.com', 'How can I track my order?', 'unread', NOW(), NULL, NULL),
    ('Andrew', 'Howard', '0412334455', 'andrew.howard@example.com', 'Can you help me reset my password?', 'unread', NOW(), NULL, NULL),
    ('Zoe', 'Ward', '0423445566', 'zoe.ward@example.com', 'I would like to request a demo.', 'unread', NOW(), NULL, NULL),
    ('Nathan', 'Cox', '0434556677', 'nathan.cox@example.com', 'Is there a discount for bulk purchases?', 'unread', NOW(), NULL, NULL),
    ('Hannah', 'Gray', '0445667788', 'hannah.gray@example.com', 'I am having issues with login.', 'unread', NOW(), NULL, NULL),
    ('Ryan', 'Price', '0456778899', 'ryan.price@example.com', 'Can you provide a breakdown of fees?', 'unread', NOW(), NULL, NULL),
    ('Madison', 'Ross', '0467889900', 'madison.ross@example.com', 'I want to upgrade my subscription.', 'unread', NOW(), NULL, NULL),
    ('Kevin', 'Cook', '0478990011', 'kevin.cook@example.com', 'I would like to schedule a consultation.', 'unread', NOW(), NULL, NULL);
