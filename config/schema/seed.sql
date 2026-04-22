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


-- =============================================================================
-- SYMPTOM INSERTS
-- =============================================================================
INSERT INTO symptoms (id, symptom_name, description) VALUES
                                                         (1, 'Cough', NULL),
                                                         (22, 'Wheezing', NULL),
                                                         (23, 'Shortness of breath', NULL),
                                                         (24, 'Chest tightness', NULL),
                                                         (25, 'Fatigue', NULL),
                                                         (26, 'Chronic cough', NULL),
                                                         (27, 'Mucus production', NULL),
                                                         (28, 'Difficulty breathing', NULL),
                                                         (29, 'Rapid breathing', NULL),
                                                         (30, 'Blue lips or fingernails', NULL),
                                                         (31, 'Frequent respiratory infections', NULL),
                                                         (32, 'Reduced exercise tolerance', NULL),
                                                         (33, 'Night-time coughing', NULL),
                                                         (34, 'Morning headaches', NULL),
                                                         (35, 'Dizziness', NULL),
                                                         (36, 'Loss of appetite', NULL),
                                                         (37, 'Unintentional weight loss', NULL),
                                                         (38, 'Swelling in ankles', NULL),
                                                         (39, 'Confusion', NULL),
                                                         (40, 'Sleep disturbances', NULL);

