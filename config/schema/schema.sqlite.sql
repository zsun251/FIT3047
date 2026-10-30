-- =============================================================================
-- RespiraCare Online — SQLite Schema
-- =============================================================================
-- SQLite version of schema.sql, used by `bin/cake setup_sqlite` so the app can
-- run without a MySQL server. Keep it in sync with schema.sql.
--
-- Differences from the MySQL schema:
--   - INTEGER PRIMARY KEY AUTOINCREMENT instead of INT UNSIGNED AUTO_INCREMENT
--   - Triggers rewritten in SQLite syntax (RAISE instead of SIGNAL)
--   - updated_at is refreshed by triggers instead of ON UPDATE CURRENT_TIMESTAMP
--   - username and email are case-insensitive (MySQL's utf8mb4_general_ci)
--   - Defaults use local time, like MySQL's NOW() (SQLite's CURRENT_TIMESTAMP is UTC)
-- =============================================================================

PRAGMA foreign_keys = ON;

-- =============================================================================
-- USER & USER TYPES
-- =============================================================================

CREATE TABLE users (
    id                  INTEGER         PRIMARY KEY AUTOINCREMENT,
    username            VARCHAR(51)     NOT NULL COLLATE NOCASE,
    password            VARCHAR(255)    NOT NULL,
    first_name          VARCHAR(100)    NOT NULL,
    last_name           VARCHAR(100)    NOT NULL,
    email               VARCHAR(255)    NOT NULL COLLATE NOCASE,
    phone               VARCHAR(15)     NULL,
    address             VARCHAR(500)    NULL,
    date_of_birth       DATE            NULL,
    type                VARCHAR(20)     NOT NULL,
    reset_token         VARCHAR(255)    NULL,
    reset_token_expires DATETIME        NULL,
    created_at          DATETIME        NOT NULL DEFAULT (datetime('now', 'localtime')),

    CONSTRAINT uq_users_username UNIQUE (username),
    CONSTRAINT uq_users_email UNIQUE (email),
    CONSTRAINT chk_users_type CHECK (type IN ('admin','clinician','patient'))
);

CREATE TABLE admins (
    id          INTEGER     PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER     NOT NULL,

    CONSTRAINT uq_admins_user_id UNIQUE (user_id),
    CONSTRAINT fk_admins_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE clinicians (
    id          INTEGER     PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER     NOT NULL,

    CONSTRAINT uq_clinicians_user_id UNIQUE (user_id),
    CONSTRAINT fk_clinicians_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE patients (
    id          INTEGER     PRIMARY KEY AUTOINCREMENT,
    user_id     INTEGER     NOT NULL,

    CONSTRAINT uq_patients_user_id UNIQUE (user_id),
    CONSTRAINT fk_patients_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================================================
-- CLINICIAN PATIENT JOIN
-- =============================================================================

CREATE TABLE clinicians_patients (
    id              INTEGER         PRIMARY KEY AUTOINCREMENT,
    clinician_id    INTEGER         NOT NULL,
    patient_id      INTEGER         NOT NULL,
    assigned_at     DATE            NOT NULL DEFAULT (date('now', 'localtime')),
    unassigned_at   DATE            NULL,
    status          VARCHAR(10)     NOT NULL DEFAULT 'active',

    CONSTRAINT chk_clinicians_patients_status CHECK (status IN ('active','inactive')),
    CONSTRAINT fk_cp_clinician FOREIGN KEY (clinician_id) REFERENCES clinicians(id),
    CONSTRAINT fk_cp_patient FOREIGN KEY (patient_id) REFERENCES patients(id)
);

CREATE INDEX idx_cp_clinician_id ON clinicians_patients (clinician_id);
CREATE INDEX idx_cp_patient_id ON clinicians_patients (patient_id);

-- =============================================================================
-- APPOINTMENTS
-- =============================================================================

CREATE TABLE appointments (
    id              INTEGER         PRIMARY KEY AUTOINCREMENT,
    patient_id      INTEGER         NOT NULL,
    clinician_id    INTEGER         NOT NULL,
    scheduled_at    DATETIME        NOT NULL,
    duration        SMALLINT        NOT NULL,
    status          VARCHAR(20)     NOT NULL DEFAULT 'upcoming',

    CONSTRAINT chk_appointments_duration CHECK (duration > 0),
    CONSTRAINT chk_appointments_status CHECK (status IN ('upcoming','completed','cancelled')),
    CONSTRAINT fk_appointments_patient FOREIGN KEY (patient_id) REFERENCES patients(id),
    CONSTRAINT fk_appointments_clinician FOREIGN KEY (clinician_id) REFERENCES clinicians(id)
);

CREATE INDEX idx_appointments_patient_id ON appointments (patient_id);
CREATE INDEX idx_appointments_clinician_id ON appointments (clinician_id);

-- =============================================================================
-- VITAL DATA & SYMPTOMS
-- =============================================================================

CREATE TABLE vital_data (
    id                  INTEGER         PRIMARY KEY AUTOINCREMENT,
    patient_id          INTEGER         NOT NULL,
    oxygen_saturation   DECIMAL(5, 2)   NULL,
    respiratory_rate    SMALLINT        NULL,             -- breaths per minute
    blood_pressure      VARCHAR(10)     NULL,             -- 'systolic/diastolic' e.g. '120/80'
    pulse_oximetry      DECIMAL(5, 2)   NULL,
    weight              DECIMAL(6, 2)   NULL,             -- kilograms
    recorded_at         DATETIME        NOT NULL DEFAULT (datetime('now', 'localtime')),

    CONSTRAINT chk_oxygen_saturation CHECK (oxygen_saturation IS NULL OR oxygen_saturation BETWEEN 0 AND 100),
    CONSTRAINT chk_respiratory_rate CHECK (respiratory_rate IS NULL OR respiratory_rate > 0),
    CONSTRAINT chk_pulse_oximetry CHECK (pulse_oximetry IS NULL OR pulse_oximetry > 0),
    CONSTRAINT chk_weight CHECK (weight IS NULL OR weight > 0),
    CONSTRAINT fk_vital_data_patient FOREIGN KEY (patient_id) REFERENCES patients(id)
);

CREATE INDEX idx_vital_data_patient_id ON vital_data (patient_id);

CREATE TABLE symptoms (
    id              INTEGER         PRIMARY KEY AUTOINCREMENT,
    symptom_name    VARCHAR(150)    NOT NULL COLLATE NOCASE,
    description     TEXT            NULL,

    CONSTRAINT uq_symptoms_name UNIQUE (symptom_name)
);

CREATE TABLE vital_data_symptoms (
    id             INTEGER         PRIMARY KEY AUTOINCREMENT,
    vital_data_id  INTEGER         NOT NULL,
    symptom_id     INTEGER         NOT NULL,
    severity       VARCHAR(10)     NOT NULL,
    notes          TEXT,

    CONSTRAINT fk_vds_vital FOREIGN KEY (vital_data_id) REFERENCES vital_data(id) ON DELETE CASCADE,
    CONSTRAINT fk_vds_symptom FOREIGN KEY (symptom_id) REFERENCES symptoms(id),
    CONSTRAINT chk_vds_severity CHECK (severity IN ('mild','moderate','severe'))
);

CREATE INDEX idx_vds_vital_data_id ON vital_data_symptoms (vital_data_id);
CREATE INDEX idx_vds_symptom_id ON vital_data_symptoms (symptom_id);

-- =============================================================================
-- REMINDERS
-- =============================================================================

CREATE TABLE reminders (
    id               INTEGER         PRIMARY KEY AUTOINCREMENT,
    message          TEXT            NOT NULL,
    scheduled_at     DATETIME        NOT NULL,
    sent_at          DATETIME        NULL,
    created_at       DATETIME        NOT NULL DEFAULT (datetime('now', 'localtime')),
    status           VARCHAR(10)     NOT NULL DEFAULT 'unsent',
    delivery_method  VARCHAR(10)     NOT NULL,
    interacted_at    DATETIME        NULL,
    interaction_type VARCHAR(20)     NULL,
    vital_data_id    INTEGER         NULL,

    CONSTRAINT chk_reminders_status CHECK (status IN ('unsent','sent','skipped')),
    CONSTRAINT chk_reminders_delivery CHECK (delivery_method IN ('sms','email','system')),
    CONSTRAINT chk_reminder_interaction CHECK (interaction_type IS NULL OR interaction_type IN ('dismissed', 'completed')),
    CONSTRAINT fk_reminders_vital_data FOREIGN KEY (vital_data_id) REFERENCES vital_data(id) ON DELETE SET NULL
);

CREATE INDEX idx_reminders_vital_data_id ON reminders (vital_data_id);
CREATE INDEX idx_scheduled ON reminders (scheduled_at, status);
CREATE INDEX idx_interaction ON reminders (interaction_type, interacted_at);

CREATE TABLE appointments_reminders (
    id              INTEGER     PRIMARY KEY AUTOINCREMENT,
    reminder_id     INTEGER     NOT NULL,
    appointment_id  INTEGER     NOT NULL,

    CONSTRAINT fk_ar_reminder FOREIGN KEY (reminder_id) REFERENCES reminders(id) ON DELETE CASCADE,
    CONSTRAINT fk_ar_appointment FOREIGN KEY (appointment_id) REFERENCES appointments(id)
);

CREATE INDEX idx_ar_reminder_id ON appointments_reminders (reminder_id);
CREATE INDEX idx_ar_appointment_id ON appointments_reminders (appointment_id);

CREATE TABLE vitals_reminders (
    id              INTEGER     PRIMARY KEY AUTOINCREMENT,
    reminder_id     INTEGER     NOT NULL,
    patient_id      INTEGER     NOT NULL,

    CONSTRAINT uq_vitals_reminders UNIQUE (reminder_id, patient_id),
    CONSTRAINT fk_vr_reminder FOREIGN KEY (reminder_id) REFERENCES reminders(id) ON DELETE CASCADE,
    CONSTRAINT fk_vr_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

CREATE INDEX idx_vr_patient_id ON vitals_reminders (patient_id);

-- =============================================================================
-- REMINDER SETTINGS
-- =============================================================================

CREATE TABLE reminder_settings (
    id                 INTEGER         PRIMARY KEY AUTOINCREMENT,
    patient_id         INTEGER         NOT NULL,
    recurrence_pattern VARCHAR(20)     NOT NULL DEFAULT 'daily',
    time_slot_1        TIME            NULL,
    time_slot_2        TIME            NULL,
    time_slot_3        TIME            NULL,
    time_slot_4        TIME            NULL,
    is_active          BOOLEAN         NOT NULL DEFAULT 1,
    email_enabled      BOOLEAN         NOT NULL DEFAULT 1,
    sms_enabled        BOOLEAN         NOT NULL DEFAULT 0,
    created_at         DATETIME        NOT NULL DEFAULT (datetime('now', 'localtime')),
    updated_at         DATETIME        NOT NULL DEFAULT (datetime('now', 'localtime')),

    CONSTRAINT uq_reminder_settings_patient UNIQUE (patient_id),
    CONSTRAINT fk_reminder_settings_patient FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
);

-- =============================================================================
-- RESOURCES
-- =============================================================================

CREATE TABLE resources (
    id              INTEGER         PRIMARY KEY AUTOINCREMENT,
    clinician_id    INTEGER         NOT NULL,
    title           VARCHAR(255)    NOT NULL,
    type            VARCHAR(20)     NOT NULL,
    body            TEXT            NULL,      -- NULL for link/video types
    url             TEXT            NULL,      -- NULL for article/blog types
    thumbnail_url   TEXT            NULL,
    status          VARCHAR(10)     NOT NULL DEFAULT 'draft',
    published_at    DATETIME        NULL,      -- NULL until published
    created_at      DATETIME        NOT NULL DEFAULT (datetime('now', 'localtime')),
    updated_at      DATETIME        NOT NULL DEFAULT (datetime('now', 'localtime')),

    CONSTRAINT chk_resource_type CHECK (type IN ('article', 'video', 'blog', 'link', 'news')),
    CONSTRAINT chk_resource_status CHECK (status IN ('draft', 'published')),
    CONSTRAINT fk_resources_clinician FOREIGN KEY (clinician_id) REFERENCES clinicians(id)
);

CREATE INDEX idx_resources_clinician_id ON resources (clinician_id);

-- =============================================================================
-- CONTACT MESSAGES
-- =============================================================================

CREATE TABLE contact_messages (
    id              INTEGER         PRIMARY KEY AUTOINCREMENT,
    first_name      VARCHAR(100)    NOT NULL,
    last_name       VARCHAR(100)    NULL,
    contact_number  VARCHAR(15)     NULL,
    email           VARCHAR(255)    NOT NULL,
    message         TEXT            NOT NULL,
    status          VARCHAR(10)     NOT NULL DEFAULT 'unread',
    submitted_at    DATETIME        NOT NULL DEFAULT (datetime('now', 'localtime')),
    reviewed_by     INTEGER         NULL,      -- NULL until an admin reviews
    reviewed_at     DATETIME        NULL,      -- NULL until reviewed

    CONSTRAINT chk_contact_status CHECK (status IN ('unread', 'read', 'archived')),
    CONSTRAINT chk_reviewed_consistency CHECK (
        (reviewed_by IS NULL AND reviewed_at IS NULL) OR
        (reviewed_by IS NOT NULL AND reviewed_at IS NOT NULL)
    ),
    CONSTRAINT fk_contact_user FOREIGN KEY (reviewed_by) REFERENCES users(id)
);

CREATE INDEX idx_contact_reviewed_by ON contact_messages (reviewed_by);

-- =============================================================================
-- TRIGGERS - users, admins, patients and clinicians
-- Same rules as the MySQL triggers in schema.sql
-- =============================================================================

-- 1. Auto-create type record when user is created
CREATE TRIGGER tr_users_after_insert_admin AFTER INSERT ON users
    WHEN NEW.type = 'admin'
BEGIN
    INSERT INTO admins (user_id) VALUES (NEW.id);
END;

CREATE TRIGGER tr_users_after_insert_clinician AFTER INSERT ON users
    WHEN NEW.type = 'clinician'
BEGIN
    INSERT INTO clinicians (user_id) VALUES (NEW.id);
END;

CREATE TRIGGER tr_users_after_insert_patient AFTER INSERT ON users
    WHEN NEW.type = 'patient'
BEGIN
    INSERT INTO patients (user_id) VALUES (NEW.id);
END;

-- 2. Handle type changes when user is updated
CREATE TRIGGER tr_users_after_update AFTER UPDATE OF type ON users
    WHEN OLD.type != NEW.type
BEGIN
    DELETE FROM admins WHERE user_id = OLD.id AND OLD.type = 'admin';
    DELETE FROM clinicians WHERE user_id = OLD.id AND OLD.type = 'clinician';
    DELETE FROM patients WHERE user_id = OLD.id AND OLD.type = 'patient';

    INSERT INTO admins (user_id) SELECT NEW.id WHERE NEW.type = 'admin';
    INSERT INTO clinicians (user_id) SELECT NEW.id WHERE NEW.type = 'clinician';
    INSERT INTO patients (user_id) SELECT NEW.id WHERE NEW.type = 'patient';
END;

-- 3-5. Validate type match on insert
CREATE TRIGGER tr_admins_before_insert BEFORE INSERT ON admins
BEGIN
    SELECT CASE
        WHEN (SELECT type FROM users WHERE id = NEW.user_id) IS NULL
            THEN RAISE(ABORT, 'Cannot create admin record: User does not exist')
        WHEN (SELECT type FROM users WHERE id = NEW.user_id) != 'admin'
            THEN RAISE(ABORT, 'Cannot create admin record: User type is not admin')
    END;
END;

CREATE TRIGGER tr_clinicians_before_insert BEFORE INSERT ON clinicians
BEGIN
    SELECT CASE
        WHEN (SELECT type FROM users WHERE id = NEW.user_id) IS NULL
            THEN RAISE(ABORT, 'Cannot create clinician record: User does not exist')
        WHEN (SELECT type FROM users WHERE id = NEW.user_id) != 'clinician'
            THEN RAISE(ABORT, 'Cannot create clinician record: User type is not clinician')
    END;
END;

CREATE TRIGGER tr_patients_before_insert BEFORE INSERT ON patients
BEGIN
    SELECT CASE
        WHEN (SELECT type FROM users WHERE id = NEW.user_id) IS NULL
            THEN RAISE(ABORT, 'Cannot create patient record: User does not exist')
        WHEN (SELECT type FROM users WHERE id = NEW.user_id) != 'patient'
            THEN RAISE(ABORT, 'Cannot create patient record: User type is not patient')
    END;
END;

-- Prevent updating user_id in type tables
CREATE TRIGGER tr_admins_before_update BEFORE UPDATE OF user_id ON admins
    WHEN OLD.user_id != NEW.user_id
BEGIN
    SELECT RAISE(ABORT, 'Cannot change user_id. Delete this record and create a new one instead.');
END;

CREATE TRIGGER tr_clinicians_before_update BEFORE UPDATE OF user_id ON clinicians
    WHEN OLD.user_id != NEW.user_id
BEGIN
    SELECT RAISE(ABORT, 'Cannot change user_id. Delete this record and create a new one instead.');
END;

CREATE TRIGGER tr_patients_before_update BEFORE UPDATE OF user_id ON patients
    WHEN OLD.user_id != NEW.user_id
BEGIN
    SELECT RAISE(ABORT, 'Cannot change user_id. Delete this record and create a new one instead.');
END;

-- =============================================================================
-- TRIGGER - Auto-create default reminder settings for new patients
-- Default: 4 times daily (8am, 12pm, 4pm, 8pm), email on, SMS off
-- =============================================================================

CREATE TRIGGER tr_patients_after_insert_create_reminder_config AFTER INSERT ON patients
BEGIN
    INSERT INTO reminder_settings (
        patient_id, recurrence_pattern,
        time_slot_1, time_slot_2, time_slot_3, time_slot_4,
        is_active, email_enabled, sms_enabled, created_at, updated_at
    ) VALUES (
        NEW.id, 'daily',
        '08:00:00', '12:00:00', '16:00:00', '20:00:00',
        1, 1, 0, datetime('now', 'localtime'), datetime('now', 'localtime')
    );
END;

-- =============================================================================
-- TRIGGERS - updated_at (replaces MySQL's ON UPDATE CURRENT_TIMESTAMP)
-- Only fires when the update did not set updated_at itself
-- =============================================================================

CREATE TRIGGER tr_reminder_settings_updated_at AFTER UPDATE ON reminder_settings
    WHEN NEW.updated_at = OLD.updated_at
BEGIN
    UPDATE reminder_settings SET updated_at = datetime('now', 'localtime') WHERE id = NEW.id;
END;

CREATE TRIGGER tr_resources_updated_at AFTER UPDATE ON resources
    WHEN NEW.updated_at = OLD.updated_at
BEGIN
    UPDATE resources SET updated_at = datetime('now', 'localtime') WHERE id = NEW.id;
END;
