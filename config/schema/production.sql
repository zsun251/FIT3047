-- =============================================================================
-- RespiraCare Online — Production Initial Data
-- =============================================================================
-- Project:     RespiraCare Online
-- Description: Essential production data for initial deployment
--
-- Usage:
--   mysql -u username -p database_name < config/schema/production.sql
--
-- Notes:
--   - FOR PRODUCTION USE ONLY
--   - Contains only essential data required for application startup
--   - Must be run AFTER schema.sql
--   -   SECURITY: Change default admin password immediately after setup!
--   - Review all data before deploying to production
--
--   IMPORTANT SECURITY NOTICE:
--   The default admin password MUST be changed after first login.
--   Default credentials are a security risk and should never remain active.
-- =============================================================================

-- =============================================================================
-- ESSENTIAL ADMIN ACCOUNT
-- =============================================================================
--   CHANGE THIS PASSWORD IMMEDIATELY AFTER SETUP!
-- Default credentials:
--   Username: admin
--   Password: password (TEMPORARY - MUST BE CHANGED)

INSERT INTO users (username, password, first_name, last_name, email, type, created_at)
VALUES (
           'admin',
           '$2y$10$9ANh3J64bHsi2UoHLmVyK.h42Z369uLJQuL13pQ3jyMCfXoKaEmlu',
           'System',
           'Administrator',
           'admin@respiracare.com',
           'admin',
           NOW()
       );

-- =============================================================================
-- ESSENTIAL CONFIGURATION DATA
-- =============================================================================
