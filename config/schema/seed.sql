-- =============================================================================
-- RespiraCare Online — Seed Data
-- =============================================================================
-- Project:     RespiraCare Online
-- Description: Development and testing sample data
--
-- Usage:
--   mysql -u username -p database_name < config/schema/seed.sql
--
-- Notes:
--   - FOR DEVELOPMENT/TESTING ONLY - DO NOT USE IN PRODUCTION
--   - Contains sample users, test data, and demonstration content
--   - All user passwords are: "password" (hashed with bcrypt)
--   - Must be run AFTER schema.sql
--   - Run this to reset test data during development
--
-- Test Accounts:
--   Username: admin1      | Password: password | Type: Admin
--   Username: clinician1  | Password: password | Type: Clinician
--   Username: patient1    | Password: password | Type: Patient
-- =============================================================================

-- =============================================================================
-- USER SEED DATA
-- =============================================================================
-- Default password for all accounts: "password"
-- Hash: $2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu


INSERT INTO users
(username, password, first_name, last_name, email, phone, address, date_of_birth, type)
VALUES
    -- ADMINS
    (
        'admin1',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'System',
        'Admin',
        'admin@respiracare.com',
        '0400000000',
        '1 Admin Street, Melbourne VIC 3000',
        '1990-01-01',
        'admin'
    ),
    (
        'admin2',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'Sarah',
        'Wilson',
        'sarah.wilson@respiracare.com',
        '0400000001',
        '2 Admin Avenue, Sydney NSW 2000',
        '1988-03-15',
        'admin'
    ),

    -- CLINICIANS
    (
        'clinician1',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'John',
        'Doctor',
        'john.doctor@respiracare.com',
        '0411111111',
        '2 Medical Ave, Melbourne VIC 3000',
        '1985-05-10',
        'clinician'
    ),
    (
        'clinician2',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'Emily',
        'Chen',
        'emily.chen@respiracare.com',
        '0411111112',
        '15 Hospital Road, Brisbane QLD 4000',
        '1982-11-22',
        'clinician'
    ),
    (
        'clinician3',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'Michael',
        'Thompson',
        'michael.thompson@respiracare.com',
        '0411111113',
        '8 Clinical Court, Perth WA 6000',
        '1979-07-30',
        'clinician'
    ),

    -- PATIENTS
    (
        'patient1',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'Jane',
        'Doe',
        'jane.doe@example.com',
        '0422222222',
        '3 Patient Road, Melbourne VIC 3001',
        '2000-09-15',
        'patient'
    ),
    (
        'patient2',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'Robert',
        'Smith',
        'robert.smith@example.com',
        '0422222223',
        '45 Suburban Drive, Sydney NSW 2010',
        '1975-12-05',
        'patient'
    ),
    (
        'patient3',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'Maria',
        'Garcia',
        'maria.garcia@example.com',
        '0422222224',
        '12 Garden Street, Adelaide SA 5000',
        '1992-04-18',
        'patient'
    ),
    (
        'patient4',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'David',
        'Johnson',
        'david.johnson@example.com',
        '0422222225',
        '88 Oak Avenue, Hobart TAS 7000',
        '1968-08-22',
        'patient'
    ),
    (
        'patient5',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'Linda',
        'Brown',
        'linda.brown@example.com',
        '0422222226',
        '156 Beach Road, Gold Coast QLD 4217',
        '1985-02-14',
        'patient'
    ),
    (
        'patient6',
        '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
        'William',
        'Davis',
        'william.davis@example.com',
        '0422222227',
        '22 Hill Street, Canberra ACT 2600',
        '1995-06-30',
        'patient'
    );

-- Note: admins, clinicians, and patients tables are auto-populated by triggers

-- =============================================================================
-- CLINICIAN-PATIENT RELATIONSHIPS
-- =============================================================================

INSERT INTO clinicians_patients
(clinician_id, patient_id, assigned_at, unassigned_at, status)
VALUES
    -- Clinician 1 (John Doctor) has 3 active patients
    (1, 1, '2024-01-15', NULL, 'active'),
    (1, 2, '2024-02-01', NULL, 'active'),
    (1, 3, '2024-02-20', NULL, 'active'),

    -- Clinician 2 (Emily Chen) has 2 active patients and 1 inactive
    (2, 4, '2024-01-10', NULL, 'active'),
    (2, 5, '2024-03-01', NULL, 'active'),
    (2, 6, '2024-01-05', '2024-03-15', 'inactive'),

    -- Clinician 3 (Michael Thompson) has 1 active patient
    (3, 6, '2024-03-20', NULL, 'active');

-- =============================================================================
-- APPOINTMENTS
-- =============================================================================

INSERT INTO appointments
(patient_id, clinician_id, scheduled_at, duration, status)
VALUES
    -- Upcoming appointments
    (1, 1, DATE_ADD(NOW(), INTERVAL 2 DAY), 30, 'upcoming'),
    (2, 1, DATE_ADD(NOW(), INTERVAL 5 DAY), 45, 'upcoming'),
    (3, 1, DATE_ADD(NOW(), INTERVAL 7 DAY), 30, 'upcoming'),
    (4, 2, DATE_ADD(NOW(), INTERVAL 3 DAY), 60, 'upcoming'),
    (5, 2, DATE_ADD(NOW(), INTERVAL 10 DAY), 30, 'upcoming'),
    (6, 3, DATE_ADD(NOW(), INTERVAL 4 DAY), 45, 'upcoming'),

    -- Completed appointments (past)
    (1, 1, DATE_SUB(NOW(), INTERVAL 7 DAY), 30, 'completed'),
    (1, 1, DATE_SUB(NOW(), INTERVAL 14 DAY), 30, 'completed'),
    (2, 1, DATE_SUB(NOW(), INTERVAL 10 DAY), 45, 'completed'),
    (3, 1, DATE_SUB(NOW(), INTERVAL 5 DAY), 30, 'completed'),
    (4, 2, DATE_SUB(NOW(), INTERVAL 12 DAY), 60, 'completed'),
    (5, 2, DATE_SUB(NOW(), INTERVAL 8 DAY), 30, 'completed'),

    -- Cancelled appointments
    (6, 3, DATE_SUB(NOW(), INTERVAL 2 DAY), 45, 'cancelled'),
    (2, 1, DATE_ADD(NOW(), INTERVAL 15 DAY), 30, 'cancelled');


-- =============================================================================
-- SYMPTOMS
-- =============================================================================

INSERT INTO symptoms (symptom_name, description) VALUES
    ('Shortness of breath', 'Difficulty breathing or feeling breathless'),
    ('Cough', 'Persistent cough (may be worse at certain times)'),
    ('Mucus/phlegm production', 'Producing sputum (note color: clear, yellow, green)'),
    ('Wheezing', 'Whistling or squeaky sound when breathing'),
    ('Chest tightness', 'Feeling of pressure or tightness in chest'),

    ('Increased fatigue', 'More tired than usual or lacking energy'),
    ('Difficulty with daily activities', 'Unable to do usual tasks or exercise'),
    ('Difficulty sleeping', 'Trouble sleeping due to breathing problems'),

    ('Blue lips or fingernails', 'Bluish color on lips, fingernails, or skin'),
    ('Confusion or drowsiness', 'Feeling confused, disoriented, or unusually sleepy'),

    ('Swollen ankles or feet', 'Swelling in lower legs, ankles, or feet'),
    ('Morning headaches', 'Headaches when waking up (may indicate low oxygen overnight)');

-- =============================================================================
-- VITAL DATA
-- =============================================================================

INSERT INTO vital_data
(patient_id, oxygen_saturation, respiratory_rate, blood_pressure, pulse_oximetry, weight, recorded_at)
VALUES
    -- Patient 1 - Recent readings showing good control
    (1, 96.5, 16,  '120/80', 96.5, 70.5, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (1, 97.0, 15,  '118/78', 97.0, 70.3, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (1, 95.8, 17,  '122/82', 95.8, 70.6, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (1, 96.2, 16,  '120/80', 96.2, 70.4, DATE_SUB(NOW(), INTERVAL 7 DAY)),

    -- Patient 2 - Showing some fluctuation
    (2, 94.5, 18,  '135/85', 94.5, 82.0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (2, 93.8, 19,  '138/88', 93.8, 82.2, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (2, 95.0, 17,  '132/84', 95.0, 81.8, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (2, 94.2, 18,  '136/86', 94.2, 82.1, DATE_SUB(NOW(), INTERVAL 5 DAY)),

    -- Patient 3 - Good readings
    (3, 97.5, 14,  '115/75', 97.5, 65.0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (3, 97.8, 14,  '118/76', 97.8, 64.8, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (3, 98.0, 13,  '116/74', 98.0, 65.2, DATE_SUB(NOW(), INTERVAL 7 DAY)),

    -- Patient 4 - Concerning readings
    (4, 91.5, 22,  '145/95', 91.5, 95.5, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4, 90.8, 23,  '148/96', 90.8, 95.8, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (4, 92.0, 21,  '142/92', 92.0, 95.2, DATE_SUB(NOW(), INTERVAL 4 DAY)),

    -- Patient 5 - Stable readings
    (5, 96.0, 16,  '125/80', 96.0, 75.0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (5, 95.5, 17,  '128/82', 95.5, 75.2, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (5, 96.2, 16,  '124/78', 96.2, 74.8, DATE_SUB(NOW(), INTERVAL 6 DAY)),

    -- Patient 6 - Recent improvement
    (6, 95.5, 17,  '130/85', 95.5, 88.0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (6, 94.8, 18,  '132/86', 94.8, 88.3, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    (6, 95.0, 18,  '131/85', 95.0, 88.1, DATE_SUB(NOW(), INTERVAL 7 DAY));

-- =============================================================================
-- VITAL DATA SYMPTOMS (JUNCTION TABLE)
-- =============================================================================

INSERT INTO vital_data_symptoms
(vital_data_id, symptom_id, severity, notes)
VALUES
    -- Patient 1 - Mild symptoms
    (1, 1, 'mild', 'Slight breathlessness after climbing stairs'),
    (2, 6, 'mild', 'Feeling a bit tired today'),

    -- Patient 2 - Moderate symptoms
    (5, 4, 'moderate', 'Wheezing noticed during exercise'),
    (5, 1, 'moderate', 'Shortness of breath during physical activity'),
    (6, 5, 'moderate', 'Chest tightness in the morning'),
    (7, 1, 'mild', 'Breathing slightly faster than normal'),

    -- Patient 4 - Severe symptoms (concerning)
    (12, 1, 'severe', 'Significant difficulty breathing'),
    (12, 4, 'severe', 'Persistent wheezing throughout day'),
    (12, 5, 'severe', 'Severe chest tightness'),
    (13, 1, 'severe', 'Very difficult to breathe'),
    (13, 6, 'severe', 'Extreme fatigue, unable to perform daily tasks'),

    -- Patient 5 - Mild symptoms
    (15, 2, 'mild', 'Occasional dry cough'),
    (16, 2, 'mild', 'Coughing at night occasionally'),

    -- Patient 6 - Moderate improving
    (18, 1, 'moderate', 'Breathlessness improving'),
    (19, 4, 'moderate', 'Wheezing less frequent'),
    (20, 6, 'moderate', 'Energy levels improving');

-- =============================================================================
-- REMINDERS
-- =============================================================================

INSERT INTO reminders
(message, scheduled_at, sent_at, status, delivery_method, interacted_at, interaction_type, vital_data_id, created_at)
VALUES
    -- Pending appointment reminders
    ('Reminder: You have an appointment tomorrow at 10:00 AM with Dr. John Doctor', DATE_ADD(NOW(), INTERVAL 1 DAY), NULL, 'pending', 'email', NULL, NULL, NULL, NOW()),
    ('Reminder: You have an appointment in 2 days at 2:00 PM with Dr. John Doctor', DATE_ADD(NOW(), INTERVAL 2 DAY), NULL, 'pending', 'sms', NULL, NULL, NULL, NOW()),
    ('Reminder: You have an appointment in 3 days at 11:00 AM with Dr. Emily Chen', DATE_ADD(NOW(), INTERVAL 3 DAY), NULL, 'pending', 'email', NULL, NULL, NULL, NOW()),

    -- Sent appointment reminders
    ('Reminder: You had an appointment today at 9:00 AM', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY), 'sent', 'sms', NULL, NULL, NULL, DATE_SUB(NOW(), INTERVAL 8 DAY)),
    ('Reminder: You had an appointment yesterday', DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), 'sent', 'email', NULL, NULL, NULL, DATE_SUB(NOW(), INTERVAL 9 DAY)),

    -- Active vitals reminders (pending, not interacted with)
    ('Please record your vitals for today', DATE_ADD(NOW(), INTERVAL 1 HOUR), NULL, 'pending', 'system', NULL, NULL, NULL, NOW()),
    ('Daily vitals reminder: Please update your oxygen saturation', NOW(), NULL, 'pending', 'email', NULL, NULL, NULL, NOW()),
    ('Weekly check-in: Record your respiratory rate', DATE_ADD(NOW(), INTERVAL 3 DAY), NULL, 'pending', 'sms', NULL, NULL, NULL, NOW()),

    -- Completed vitals reminders (patient submitted vitals)
    ('Daily vitals reminder', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 'sent', 'system', DATE_SUB(NOW(), INTERVAL 1 DAY), 'completed', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    ('Please record your vitals', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 'sent', 'email', DATE_SUB(NOW(), INTERVAL 3 DAY), 'completed', 2, DATE_SUB(NOW(), INTERVAL 4 DAY)),

    -- Dismissed vitals reminders (patient dismissed without submitting)
    ('Morning vitals check', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 'sent', 'system', DATE_SUB(NOW(), INTERVAL 2 DAY), 'dismissed', NULL, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    ('Evening vitals reminder', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 'sent', 'email', DATE_SUB(NOW(), INTERVAL 5 DAY), 'dismissed', NULL, DATE_SUB(NOW(), INTERVAL 6 DAY));

-- =============================================================================
-- APPOINTMENT REMINDERS (JUNCTION TABLE)
-- =============================================================================

INSERT INTO appointments_reminders
(reminder_id, appointment_id)
VALUES
    (1, 1),  -- Appointment 1 has reminder 1
    (2, 2),  -- Appointment 2 has reminder 2
    (3, 4),  -- Appointment 4 has reminder 3
    (4, 7),  -- Completed appointment 7 had reminder 4
    (5, 8);  -- Completed appointment 8 had reminder 5

-- =============================================================================
-- VITALS REMINDERS (JUNCTION TABLE)
-- =============================================================================

INSERT INTO vitals_reminders
(reminder_id, patient_id, recurrence_pattern, time_slot_1, time_slot_2, time_slot_3, time_slot_4, is_active, email_enabled, created_at, updated_at)
VALUES
    -- Patient 1: 4 daily reminders, email enabled
    (6, 1, 'daily', '08:00:00', '12:00:00', '16:00:00', '20:00:00', TRUE, TRUE, NOW(), NOW()),
    (9, 1, 'daily', '08:00:00', '12:00:00', '16:00:00', '20:00:00', TRUE, TRUE, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (11, 1, 'daily', '08:00:00', '12:00:00', '16:00:00', '20:00:00', TRUE, TRUE, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),

    -- Patient 2: 3 daily reminders, email enabled
    (7, 2, 'daily', '09:00:00', '14:00:00', '19:00:00', NULL, TRUE, TRUE, NOW(), NOW()),

    -- Patient 3: Completed reminder
    (10, 3, 'daily', '08:00:00', '12:00:00', '16:00:00', '20:00:00', TRUE, FALSE, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY)),

    -- Patient 4: 2 daily reminders, SMS only (email disabled)
    (8, 4, 'daily', '10:00:00', '18:00:00', NULL, NULL, TRUE, FALSE, NOW(), NOW()),

    -- Patient 5: Dismissed reminder
    (12, 5, 'daily', '07:00:00', '13:00:00', '19:00:00', NULL, TRUE, TRUE, DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY));

-- =============================================================================
-- RESOURCES
-- =============================================================================

INSERT INTO resources
(clinician_id, title, type, body, url, thumbnail_url, status, published_at)
VALUES
    -- Published articles by Clinician 1
    (
        1,
        'Understanding COPD: A Patient Guide',
        'article',
        'Chronic Obstructive Pulmonary Disease (COPD) is a progressive lung disease that makes breathing difficult. This comprehensive guide covers symptoms, diagnosis, treatment options, and lifestyle modifications to help manage COPD effectively...',
        NULL,
        NULL,
        'published',
        DATE_SUB(NOW(), INTERVAL 30 DAY)
    ),
    (
        1,
        'Breathing Exercises for Better Lung Health',
        'article',
        'Regular breathing exercises can significantly improve lung capacity and overall respiratory health. This article demonstrates five essential breathing techniques including pursed-lip breathing, diaphragmatic breathing, and box breathing...',
        NULL,
        NULL,
        'published',
        DATE_SUB(NOW(), INTERVAL 15 DAY)
    ),

    -- Video resources
    (
        1,
        'How to Use Your Inhaler Correctly',
        'video',
        NULL,
        'https://www.youtube.com/watch?v=example1',
        'https://img.youtube.com/vi/example1/maxresdefault.jpg',
        'published',
        DATE_SUB(NOW(), INTERVAL 20 DAY)
    ),
    (
        2,
        'Managing Asthma Triggers in Daily Life',
        'video',
        NULL,
        'https://www.youtube.com/watch?v=example2',
        'https://img.youtube.com/vi/example2/maxresdefault.jpg',
        'published',
        DATE_SUB(NOW(), INTERVAL 10 DAY)
    ),

    -- Blog posts by Clinician 2
    (
        2,
        '5 Tips for Better Sleep with Respiratory Conditions',
        'blog',
        'Getting quality sleep can be challenging when dealing with respiratory issues. Here are five evidence-based tips to improve your sleep quality: 1) Elevate your head, 2) Use a humidifier, 3) Practice relaxation techniques...',
        NULL,
        NULL,
        'published',
        DATE_SUB(NOW(), INTERVAL 5 DAY)
    ),
    (
        2,
        'The Importance of Regular Vital Monitoring',
        'blog',
        'Monitoring your vital signs regularly is crucial for managing chronic respiratory conditions. This post explains what vitals to track, how often to measure them, and when to seek medical attention...',
        NULL,
        NULL,
        'published',
        DATE_SUB(NOW(), INTERVAL 12 DAY)
    ),

    -- External links
    (
        3,
        'Australian Lung Foundation Resources',
        'link',
        NULL,
        'https://lungfoundation.com.au',
        NULL,
        'published',
        DATE_SUB(NOW(), INTERVAL 25 DAY)
    ),
    (
        1,
        'WHO Guidelines on Air Quality',
        'link',
        NULL,
        'https://www.who.int/air-pollution/guidelines',
        NULL,
        'published',
        DATE_SUB(NOW(), INTERVAL 40 DAY)
    ),

    -- News articles
    (
        2,
        'New Study Shows Benefits of Early COPD Detection',
        'news',
        'Recent research published in the Respiratory Medicine Journal demonstrates that early detection of COPD can significantly improve patient outcomes. The study followed 1,000 patients over five years...',
        'https://example.com/copd-study-2024',
        NULL,
        'published',
        DATE_SUB(NOW(), INTERVAL 3 DAY)
    ),
    (
        3,
        'Advances in Asthma Treatment: 2024 Update',
        'news',
        'The latest developments in asthma management include new biological therapies and improved inhaler technologies. This article reviews recent FDA approvals and clinical trials...',
        'https://example.com/asthma-advances-2024',
        NULL,
        'published',
        DATE_SUB(NOW(), INTERVAL 7 DAY)
    ),

    -- Draft resources
    (
        1,
        'Nutrition Tips for Lung Health',
        'article',
        'Draft content about anti-inflammatory foods, vitamins for respiratory health...',
        NULL,
        NULL,
        'draft',
        NULL
    ),
    (
        2,
        'Exercise Guidelines for COPD Patients',
        'blog',
        'Draft: Recommendations for safe exercise routines...',
        NULL,
        NULL,
        'draft',
        NULL
    ),
    (
        3,
        'Understanding Your Lung Function Test Results',
        'article',
        'Draft: Explaining spirometry, peak flow, and other tests...',
        NULL,
        NULL,
        'draft',
        NULL
    );

-- =============================================================================
-- CONTACT MESSAGES
-- =============================================================================

INSERT INTO contact_messages
(first_name, last_name, contact_number, email, message, status, submitted_at, reviewed_by, reviewed_at)
VALUES
    -- Unread messages
    ('Michael', 'Green', '0411222333', 'michael.green@example.com', 'I need help with my account login. I forgot my password.', 'unread', DATE_SUB(NOW(), INTERVAL 2 HOUR), NULL, NULL),
    ('Samantha', 'White', '0422333444', 'samantha.white@example.com', 'Can you provide details about your pricing plans?', 'unread', DATE_SUB(NOW(), INTERVAL 5 HOUR), NULL, NULL),
    ('David', 'Taylor', '0433444555', 'david.taylor@example.com', 'I am interested in your premium package. How do I upgrade?', 'unread', DATE_SUB(NOW(), INTERVAL 1 DAY), NULL, NULL),
    ('Emma', 'Harris', '0444555666', 'emma.harris@example.com', 'How do I reset my password? The reset email is not arriving.', 'unread', DATE_SUB(NOW(), INTERVAL 3 HOUR), NULL, NULL),
    ('Oliver', 'Martin', '0455666777', 'oliver.martin@example.com', 'I have a billing question about my last invoice.', 'unread', DATE_SUB(NOW(), INTERVAL 8 HOUR), NULL, NULL),

    -- Read but not archived
    ('Sophia', 'Lee', '0466777888', 'sophia.lee@example.com', 'Can I get a demo of your software?', 'read', DATE_SUB(NOW(), INTERVAL 2 DAY), 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    ('Liam', 'Walker', '0477888999', 'liam.walker@example.com', 'Do you offer discounts for healthcare workers?', 'read', DATE_SUB(NOW(), INTERVAL 3 DAY), 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    ('Isabella', 'King', '0488999000', 'isabella.king@example.com', 'I would like to know your office hours.', 'read', DATE_SUB(NOW(), INTERVAL 4 DAY), 2, DATE_SUB(NOW(), INTERVAL 3 DAY)),

    -- Archived messages
    ('Noah', 'Wright', '0412341122', 'noah.wright@example.com', 'Can you help me with an error I received when logging in?', 'archived', DATE_SUB(NOW(), INTERVAL 7 DAY), 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
    ('Mia', 'Scott', '0423452233', 'mia.scott@example.com', 'I want to change my subscription plan to the basic tier.', 'archived', DATE_SUB(NOW(), INTERVAL 10 DAY), 2, DATE_SUB(NOW(), INTERVAL 9 DAY)),
    ('Ethan', 'Adams', '0434563344', 'ethan.adams@example.com', 'How long does it take to set up a new account?', 'archived', DATE_SUB(NOW(), INTERVAL 14 DAY), 1, DATE_SUB(NOW(), INTERVAL 13 DAY)),

    -- More unread messages
    ('Ava', 'Baker', '0445674455', 'ava.baker@example.com', 'Can you provide a quote for enterprise services?', 'unread', DATE_SUB(NOW(), INTERVAL 6 HOUR), NULL, NULL),
    ('James', 'Carter', '0456785566', 'james.carter@example.com', 'I need technical support with the mobile app.', 'unread', DATE_SUB(NOW(), INTERVAL 12 HOUR), NULL, NULL),
    ('Charlotte', 'Mitchell', '0467896677', 'charlotte.mitchell@example.com', 'Do you offer corporate plans for healthcare facilities?', 'unread', DATE_SUB(NOW(), INTERVAL 4 HOUR), NULL, NULL),
    ('Benjamin', 'Perez', '0478907788', 'benjamin.perez@example.com', 'I have feedback on your website navigation.', 'unread', DATE_SUB(NOW(), INTERVAL 1 HOUR), NULL, NULL),

    -- Additional messages
    ('Daniel', 'Evans', '0411223344', 'daniel.evans@example.com', 'I would like information about telehealth consultations.', 'unread', DATE_SUB(NOW(), INTERVAL 10 HOUR), NULL, NULL),
    ('Grace', 'Collins', '0422334455', 'grace.collins@example.com', 'Can you explain the data privacy policy?', 'read', DATE_SUB(NOW(), INTERVAL 5 DAY), 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
    ('Matthew', 'Turner', '0433445566', 'matthew.turner@example.com', 'I need help accessing my medical records.', 'unread', DATE_SUB(NOW(), INTERVAL 7 HOUR), NULL, NULL),
    ('Chloe', 'Parker', '0444556677', 'chloe.parker@example.com', 'How do I cancel my subscription?', 'read', DATE_SUB(NOW(), INTERVAL 6 DAY), 2, DATE_SUB(NOW(), INTERVAL 5 DAY)),
    ('Ryan', 'Murphy', '0455667788', 'ryan.murphy@example.com', 'I received an invoice, can you clarify the charges?', 'archived', DATE_SUB(NOW(), INTERVAL 15 DAY), 1, DATE_SUB(NOW(), INTERVAL 14 DAY)),
    ('Ella', 'Bell', '0466778899', 'ella.bell@example.com', 'Do you have an API for integration?', 'unread', DATE_SUB(NOW(), INTERVAL 9 HOUR), NULL, NULL),
    ('Joshua', 'Cooper', '0477889900', 'joshua.cooper@example.com', 'I need to update my payment details.', 'unread', DATE_SUB(NOW(), INTERVAL 2 HOUR), NULL, NULL),
    ('Lily', 'Reed', '0488990011', 'lily.reed@example.com', 'How can I export my health data?', 'read', DATE_SUB(NOW(), INTERVAL 8 DAY), 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
    ('Andrew', 'Howard', '0412334455', 'andrew.howard@example.com', 'Can you help me reset my two-factor authentication?', 'unread', DATE_SUB(NOW(), INTERVAL 3 HOUR), NULL, NULL),
    ('Zoe', 'Ward', '0423445566', 'zoe.ward@example.com', 'I would like to request a product demonstration.', 'unread', DATE_SUB(NOW(), INTERVAL 11 HOUR), NULL, NULL),
    ('Nathan', 'Cox', '0434556677', 'nathan.cox@example.com', 'Is there a discount for annual subscriptions?', 'read', DATE_SUB(NOW(), INTERVAL 12 DAY), 2, DATE_SUB(NOW(), INTERVAL 11 DAY)),
    ('Hannah', 'Gray', '0445667788', 'hannah.gray@example.com', 'I am having issues with the appointment booking system.', 'unread', DATE_SUB(NOW(), INTERVAL 5 HOUR), NULL, NULL),
    ('Ryan', 'Price', '0456778899', 'ryan.price@example.com', 'Can you provide a breakdown of features in each tier?', 'archived', DATE_SUB(NOW(), INTERVAL 20 DAY), 1, DATE_SUB(NOW(), INTERVAL 19 DAY)),
    ('Madison', 'Ross', '0467889900', 'madison.ross@example.com', 'I want to upgrade my subscription to include more users.', 'unread', DATE_SUB(NOW(), INTERVAL 6 HOUR), NULL, NULL),
    ('Kevin', 'Cook', '0478990011', 'kevin.cook@example.com', 'I would like to schedule a consultation with a clinician.', 'unread', DATE_SUB(NOW(), INTERVAL 4 HOUR), NULL, NULL);


