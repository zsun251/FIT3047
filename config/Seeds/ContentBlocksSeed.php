<?php

declare(strict_types=1);

use Migrations\BaseSeed;

class ContentBlocksSeed extends BaseSeed
{
    public function run(): void
    {
        $data = [
            // -----------------------------------------------------------------
            // Global branding
            // -----------------------------------------------------------------
            [
                'parent' => 'global',
                'label' => 'Site Name',
                'description' => 'Shared site name used in branding and page titles.',
                'slug' => 'global-site-name',
                'type' => 'text',
                'value' => 'RespiraCare',
            ],
            [
                'parent' => 'global',
                'label' => 'Logo',
                'description' => 'Main logo used in the topbar and authentication pages.',
                'slug' => 'global-logo',
                'type' => 'image',
                'value' => 'logo.png',
            ],
            [
                'parent' => 'global',
                'label' => 'Logo Alt Text',
                'description' => 'Accessible text for the RespiraCare logo image.',
                'slug' => 'global-logo-alt',
                'type' => 'text',
                'value' => 'RespiraCare logo',
            ],
            [
                'parent' => 'global',
                'label' => 'Footer Copyright',
                'description' => 'Copyright text displayed in the site footer.',
                'slug' => 'global-footer-copyright',
                'type' => 'text',
                'value' => '© RespiraCare 2026',
            ],

            // -----------------------------------------------------------------
            // Homepage: intro
            // -----------------------------------------------------------------
            [
                'parent' => 'home_intro',
                'label' => 'Intro Title',
                'description' => 'Large headline shown at the top of the homepage.',
                'slug' => 'home-intro-title',
                'type' => 'text',
                'value' => 'Take Control of Your Health With RespiraCare Online',
            ],
            [
                'parent' => 'home_intro',
                'label' => 'Intro Description',
                'description' => 'Short paragraph shown under the homepage intro title.',
                'slug' => 'home-intro-description',
                'type' => 'text',
                'value' => 'Easily monitor your lung health, oxygen levels, and symptoms from home. Stay in touch with your care team and get helpful reminders, no extra clinic visits required.',
            ],
            [
                'parent' => 'home_intro',
                'label' => 'Intro Button Text',
                'description' => 'Text shown on the main homepage call-to-action button.',
                'slug' => 'home-intro-button-text',
                'type' => 'text',
                'value' => 'Get Started',
            ],
            [
                'parent' => 'home_intro',
                'label' => 'Intro Button Aria Label',
                'description' => 'Accessible label for the homepage call-to-action button.',
                'slug' => 'home-intro-button-aria-label',
                'type' => 'text',
                'value' => 'Register for RespiraCare',
            ],
            [
                'parent' => 'home_intro',
                'label' => 'Intro Background Image',
                'description' => 'Large background image used in the homepage intro section.',
                'slug' => 'home-intro-image',
                'type' => 'image',
                'value' => 'home.png',
            ],

            // -----------------------------------------------------------------
            // Homepage: benefits
            // -----------------------------------------------------------------
            [
                'parent' => 'home_benefits',
                'label' => 'Benefits Section Label',
                'description' => 'Small label shown above the benefits heading.',
                'slug' => 'home-benefits-label',
                'type' => 'text',
                'value' => 'Why RespiraCare?',
            ],
            [
                'parent' => 'home_benefits',
                'label' => 'Benefits Heading',
                'description' => 'Main heading for the homepage benefits section.',
                'slug' => 'home-benefits-heading',
                'type' => 'text',
                'value' => 'Simple Support for Everyday Respiratory Care',
            ],
            [
                'parent' => 'home_benefits',
                'label' => 'Benefit One Icon',
                'description' => 'Bootstrap Icons class used for the first benefit card.',
                'slug' => 'home-benefit-one-icon',
                'type' => 'text',
                'value' => 'bi-heart-pulse',
            ],
            [
                'parent' => 'home_benefits',
                'label' => 'Benefit One Title',
                'description' => 'Title for the first homepage benefit card.',
                'slug' => 'home-benefit-one-title',
                'type' => 'text',
                'value' => 'Simple Remote Monitoring',
            ],
            [
                'parent' => 'home_benefits',
                'label' => 'Benefit One Text',
                'description' => 'Description for the first homepage benefit card.',
                'slug' => 'home-benefit-one-text',
                'type' => 'text',
                'value' => 'Track oxygen levels, lung function, and symptoms quickly from the comfort of your home.',
            ],
            [
                'parent' => 'home_benefits',
                'label' => 'Benefit Two Icon',
                'description' => 'Bootstrap Icons class used for the second benefit card.',
                'slug' => 'home-benefit-two-icon',
                'type' => 'text',
                'value' => 'bi-bell',
            ],
            [
                'parent' => 'home_benefits',
                'label' => 'Benefit Two Title',
                'description' => 'Title for the second homepage benefit card.',
                'slug' => 'home-benefit-two-title',
                'type' => 'text',
                'value' => 'Helpful Reminders',
            ],
            [
                'parent' => 'home_benefits',
                'label' => 'Benefit Two Text',
                'description' => 'Description for the second homepage benefit card.',
                'slug' => 'home-benefit-two-text',
                'type' => 'text',
                'value' => 'Never miss a check-in — get helpful reminders to log your symptoms and vitals.',
            ],
            [
                'parent' => 'home_benefits',
                'label' => 'Benefit Three Icon',
                'description' => 'Bootstrap Icons class used for the third benefit card.',
                'slug' => 'home-benefit-three-icon',
                'type' => 'text',
                'value' => 'bi-person-lines-fill',
            ],

            [
                'parent' => 'home_benefits',
                'label' => 'Benefit Three Title',
                'description' => 'Title for the third homepage benefit card.',
                'slug' => 'home-benefit-three-title',
                'type' => 'text',
                'value' => 'Trusted Clinician Support',
            ],
            [
                'parent' => 'home_benefits',
                'label' => 'Benefit Three Text',
                'description' => 'Description for the third homepage benefit card.',
                'slug' => 'home-benefit-three-text',
                'type' => 'text',
                'value' => 'Your healthcare team can review your data and provide guidance without in-person visits.',
            ],

            // -----------------------------------------------------------------
            // Homepage: team image section
            // -----------------------------------------------------------------
            [
                'parent' => 'home_team',
                'label' => 'Team Section Heading',
                'description' => 'Heading shown over the team image section.',
                'slug' => 'home-team-heading',
                'type' => 'text',
                'value' => 'Better Care, Together.',
            ],
            [
                'parent' => 'home_team',
                'label' => 'Team Section Text',
                'description' => 'Short text shown over the team image section.',
                'slug' => 'home-team-text',
                'type' => 'text',
                'value' => 'RespiraCare connects you with your care team for smarter, easier respiratory health management.',
            ],
            [
                'parent' => 'home_team',
                'label' => 'Team Section Image',
                'description' => 'Background image used in the Better Care, Together section.',
                'slug' => 'home-team-image',
                'type' => 'image',
                'value' => 'homepage.png',
            ],

            // -----------------------------------------------------------------
            // Homepage: how it works
            // -----------------------------------------------------------------
            [
                'parent' => 'home_how_it_works',
                'label' => 'How It Works Heading',
                'description' => 'Main heading for the homepage process section.',
                'slug' => 'home-how-heading',
                'type' => 'text',
                'value' => 'How RespiraCare Works',
            ],
            [
                'parent' => 'home_how_it_works',
                'label' => 'How It Works Step One Icon',
                'description' => 'Bootstrap Icons class used for the first how-it-works step.',
                'slug' => 'home-how-step-one-icon',
                'type' => 'text',
                'value' => 'bi-clipboard2-pulse',
            ],
            [
                'parent' => 'home_how_it_works',
                'label' => 'How It Works Step One Title',
                'description' => 'Title for the first process step.',
                'slug' => 'home-how-step-one-title',
                'type' => 'text',
                'value' => 'Enter Your Data',
            ],
            [
                'parent' => 'home_how_it_works',
                'label' => 'How It Works Step One Text',
                'description' => 'Description for the first process step.',
                'slug' => 'home-how-step-one-text',
                'type' => 'text',
                'value' => 'Input your vital signs and symptoms through our easy online interface.',
            ],
            [
                'parent' => 'home_how_it_works',
                'label' => 'How It Works Step Two Icon',
                'description' => 'Bootstrap Icons class used for the second how-it-works step.',
                'slug' => 'home-how-step-two-icon',
                'type' => 'text',
                'value' => 'bi-clipboard2-pulse',
            ],
            [
                'parent' => 'home_how_it_works',
                'label' => 'How It Works Step Two Title',
                'description' => 'Title for the second process step.',
                'slug' => 'home-how-step-two-title',
                'type' => 'text',
                'value' => 'Receive Guidance',
            ],
            [
                'parent' => 'home_how_it_works',
                'label' => 'How It Works Step Two Text',
                'description' => 'Description for the second process step.',
                'slug' => 'home-how-step-two-text',
                'type' => 'text',
                'value' => 'Your clinicians review your data and can provide advice or adjustments remotely.',
            ],
            [
                'parent' => 'home_how_it_works',
                'label' => 'How It Works Step Three Icon',
                'description' => 'Bootstrap Icons class used for the third how-it-works step.',
                'slug' => 'home-how-step-three-icon',
                'type' => 'text',
                'value' => 'bi-bell',
            ],
            [
                'parent' => 'home_how_it_works',
                'label' => 'How It Works Step Three Title',
                'description' => 'Title for the third process step.',
                'slug' => 'home-how-step-three-title',
                'type' => 'text',
                'value' => 'Stay On Track',
            ],
            [
                'parent' => 'home_how_it_works',
                'label' => 'How It Works Step Three Text',
                'description' => 'Description for the third process step.',
                'slug' => 'home-how-step-three-text',
                'type' => 'text',
                'value' => 'Get reminders, alerts, and tips to help you manage COPD safely at home.',
            ],

            // -----------------------------------------------------------------
            // Homepage: getting started
            // -----------------------------------------------------------------
            [
                'parent' => 'home_getting_started',
                'label' => 'Getting Started Label',
                'description' => 'Small label shown above the getting started heading.',
                'slug' => 'home-getting-started-label',
                'type' => 'text',
                'value' => 'Getting Started',
            ],
            [
                'parent' => 'home_getting_started',
                'label' => 'Getting Started Heading',
                'description' => 'Main heading for the getting started homepage section.',
                'slug' => 'home-getting-started-heading',
                'type' => 'text',
                'value' => 'Start Your RespiraCare Journey In Just A Few Simple Steps',
            ],
            [
                'parent' => 'home_getting_started',
                'label' => 'Getting Started Text',
                'description' => 'Introductory text for the getting started homepage section.',
                'slug' => 'home-getting-started-text',
                'type' => 'text',
                'value' => 'Register your account and, once approved, use RespiraCare to track your respiratory health from home and stay connected with your clinicians for monitoring support.',
            ],
            [
                'parent' => 'home_getting_started',
                'label' => 'Getting Started Button Text',
                'description' => 'Text shown on the getting started call-to-action button.',
                'slug' => 'home-getting-started-button-text',
                'type' => 'text',
                'value' => 'Contact Us',
            ],
            [
                'parent' => 'home_getting_started',
                'label' => 'Getting Started Step One Title',
                'description' => 'Title for the first getting started timeline item.',
                'slug' => 'home-getting-started-step-one-title',
                'type' => 'text',
                'value' => 'Register Your Account',
            ],
            [
                'parent' => 'home_getting_started',
                'label' => 'Getting Started Step One Item One',
                'description' => 'First checklist item in the getting started section.',
                'slug' => 'home-getting-started-step-one-item-one',
                'type' => 'text',
                'value' => 'Easy onboarding',
            ],
            [
                'parent' => 'home_getting_started',
                'label' => 'Getting Started Step One Item Two',
                'description' => 'Second checklist item in the getting started section.',
                'slug' => 'home-getting-started-step-one-item-two',
                'type' => 'text',
                'value' => 'Secure patient access',
            ],
            [
                'parent' => 'home_getting_started',
                'label' => 'Getting Started Step One Item Three',
                'description' => 'Third checklist item in the getting started section.',
                'slug' => 'home-getting-started-step-one-item-three',
                'type' => 'text',
                'value' => 'Remote monitoring support',
            ],
            [
                'parent' => 'home_getting_started',
                'label' => 'Getting Started Step Two Title',
                'description' => 'Title for the second getting started timeline item.',
                'slug' => 'home-getting-started-step-two-title',
                'type' => 'text',
                'value' => 'Wait For Approval',
            ],
            [
                'parent' => 'home_getting_started',
                'label' => 'Getting Started Step Three Title',
                'description' => 'Title for the third getting started timeline item.',
                'slug' => 'home-getting-started-step-three-title',
                'type' => 'text',
                'value' => 'Start Tracking Your Health',
            ],

            // -----------------------------------------------------------------
            // Homepage: FAQ
            // -----------------------------------------------------------------
            [
                'parent' => 'home_faq',
                'label' => 'FAQ Heading',
                'description' => 'Main heading for the homepage FAQ section.',
                'slug' => 'home-faq-heading',
                'type' => 'text',
                'value' => 'Frequently Asked Questions',
            ],
            [
                'parent' => 'home_faq',
                'label' => 'FAQ Intro Text',
                'description' => 'Short text shown under the FAQ heading.',
                'slug' => 'home-faq-intro-text',
                'type' => 'text',
                'value' => 'Find quick answers to common questions about RespiraCare.',
            ],
            [
                'parent' => 'home_faq',
                'label' => 'FAQ Question One',
                'description' => 'First FAQ question.',
                'slug' => 'home-faq-one-question',
                'type' => 'text',
                'value' => 'Is My Health Information Safe?',
            ],
            [
                'parent' => 'home_faq',
                'label' => 'FAQ Answer One',
                'description' => 'First FAQ answer.',
                'slug' => 'home-faq-one-answer',
                'type' => 'text',
                'value' => 'Yes. RespiraCare uses secure encryption and only authorised clinicians can access your data.',
            ],
            [
                'parent' => 'home_faq',
                'label' => 'FAQ Question Two',
                'description' => 'Second FAQ question.',
                'slug' => 'home-faq-two-question',
                'type' => 'text',
                'value' => 'How Often Should I Input My Vitals?',
            ],
            [
                'parent' => 'home_faq',
                'label' => 'FAQ Answer Two',
                'description' => 'Second FAQ answer.',
                'slug' => 'home-faq-two-answer',
                'type' => 'text',
                'value' => 'Many patients are asked to check in up to four times a day, but you should always follow the schedule recommended by your clinician or healthcare provider.',
            ],

            // -----------------------------------------------------------------
            // Contact page
            // -----------------------------------------------------------------
            [
                'parent' => 'contact',
                'label' => 'Page Title',
                'description' => 'Main heading on the public contact page.',
                'slug' => 'contact-page-title',
                'type' => 'text',
                'value' => 'Contact Us',
            ],
            [
                'parent' => 'contact',
                'label' => 'Intro Text',
                'description' => 'Short introduction shown above the contact form.',
                'slug' => 'contact-intro-text',
                'type' => 'text',
                'value' => "Have a question? Send us a message and we'll get back to you.",
            ],
            [
                'parent' => 'contact',
                'label' => 'Required Fields Text',
                'description' => 'Small helper text shown above the contact form fields.',
                'slug' => 'contact-required-fields-text',
                'type' => 'text',
                'value' => '* Required fields',
            ],
            [
                'parent' => 'contact',
                'label' => 'Message Placeholder',
                'description' => 'Placeholder text shown inside the contact message field.',
                'slug' => 'contact-message-placeholder',
                'type' => 'text',
                'value' => 'How can we help you?',
            ],
            [
                'parent' => 'contact',
                'label' => 'Submit Button Text',
                'description' => 'Text shown on the public contact form submit button.',
                'slug' => 'contact-submit-button-text',
                'type' => 'text',
                'value' => 'Send Message',
            ],

            // -----------------------------------------------------------------
            // Resource library
            // -----------------------------------------------------------------
            [
                'parent' => 'resources',
                'label' => 'Library Page Title',
                'description' => 'Main heading on the public resource library page.',
                'slug' => 'resources-library-title',
                'type' => 'text',
                'value' => 'Resource Library',
            ],
            [
                'parent' => 'resources',
                'label' => 'Library Intro Text',
                'description' => 'Introductory text shown below the resource library heading.',
                'slug' => 'resources-library-intro-text',
                'type' => 'text',
                'value' => 'Educational materials and insights about respiratory health',
            ],
            [
                'parent' => 'resources',
                'label' => 'Empty State Text',
                'description' => 'Text shown when no resources are available.',
                'slug' => 'resources-empty-text',
                'type' => 'text',
                'value' => 'No resources found. Check back soon for new content.',
            ],
            [
                'parent' => 'resources',
                'label' => 'External Resource Heading',
                'description' => 'Heading shown on external resource pages.',
                'slug' => 'resources-external-heading',
                'type' => 'text',
                'value' => 'Visit External Resource',
            ],
            [
                'parent' => 'resources',
                'label' => 'External Resource Text',
                'description' => 'Helper text shown on external resource pages.',
                'slug' => 'resources-external-text',
                'type' => 'text',
                'value' => 'This resource is hosted on an external website.',
            ],

            // -----------------------------------------------------------------
            // Authentication pages
            // -----------------------------------------------------------------
            [
                'parent' => 'auth',
                'label' => 'Login Heading',
                'description' => 'Main heading on the login page.',
                'slug' => 'auth-login-heading',
                'type' => 'text',
                'value' => 'Welcome Back',
            ],
            [
                'parent' => 'auth',
                'label' => 'Login Intro Text',
                'description' => 'Short text shown under the login heading.',
                'slug' => 'auth-login-intro-text',
                'type' => 'text',
                'value' => 'Sign in to access your account',
            ],
            [
                'parent' => 'auth',
                'label' => 'Register Heading',
                'description' => 'Main heading on the patient registration page.',
                'slug' => 'auth-register-heading',
                'type' => 'text',
                'value' => 'Create Patient Account',
            ],
            [
                'parent' => 'auth',
                'label' => 'Register Intro Text',
                'description' => 'Short text shown under the registration heading.',
                'slug' => 'auth-register-intro-text',
                'type' => 'text',
                'value' => 'Set up your account to start using RespiraCare',
            ],
            [
                'parent' => 'auth',
                'label' => 'Forgot Password Heading',
                'description' => 'Main heading on the forgot password page.',
                'slug' => 'auth-forgot-password-heading',
                'type' => 'text',
                'value' => 'Forgot Password?',
            ],
            [
                'parent' => 'auth',
                'label' => 'Forgot Password Intro Text',
                'description' => 'Short instruction text on the forgot password page.',
                'slug' => 'auth-forgot-password-intro-text',
                'type' => 'text',
                'value' => "Enter your email address and we'll send you instructions to reset your password.",
            ],
            [
                'parent' => 'auth',
                'label' => 'Reset Password Heading',
                'description' => 'Main heading on the reset password page.',
                'slug' => 'auth-reset-password-heading',
                'type' => 'text',
                'value' => 'Reset Password',
            ],
            [
                'parent' => 'auth',
                'label' => 'Reset Password Intro Text',
                'description' => 'Short instruction text on the reset password page.',
                'slug' => 'auth-reset-password-intro-text',
                'type' => 'text',
                'value' => 'Enter your new password below.',
            ],

            // -----------------------------------------------------------------
            // Patient dashboard and patient-facing help text
            // -----------------------------------------------------------------
            [
                'parent' => 'dashboard',
                'label' => 'Patient Dashboard Help Text',
                'description' => 'Information message shown near the top of the patient dashboard.',
                'slug' => 'dashboard-patient-help-text',
                'type' => 'text',
                'value' => 'Use your dashboard to quickly review your latest health information, check upcoming appointments, and access helpful resources.',
            ],
            [
                'parent' => 'dashboard',
                'label' => 'Staff Dashboard Intro Text',
                'description' => 'Short description shown on the staff dashboard.',
                'slug' => 'dashboard-staff-intro-text',
                'type' => 'text',
                'value' => 'Manage contact messages submissions and inquiries',
            ],

            // -----------------------------------------------------------------
            // Patient health records
            // -----------------------------------------------------------------
            [
                'parent' => 'health_records',
                'label' => 'Patient Health Records Heading',
                'description' => 'Main heading on the patient health records page.',
                'slug' => 'health-records-patient-heading',
                'type' => 'text',
                'value' => 'My Health Records',
            ],
            [
                'parent' => 'health_records',
                'label' => 'Patient Health Records Intro Text',
                'description' => 'Short description on the patient health records page.',
                'slug' => 'health-records-patient-intro-text',
                'type' => 'text',
                'value' => 'Track your vital signs and health trends over time',
            ],
            [
                'parent' => 'health_records',
                'label' => 'No Health Records Heading',
                'description' => 'Heading shown when the patient has no health records.',
                'slug' => 'health-records-empty-heading',
                'type' => 'text',
                'value' => 'No Health Records Yet',
            ],
            [
                'parent' => 'health_records',
                'label' => 'No Health Records Text',
                'description' => 'Text shown when the patient has no health records.',
                'slug' => 'health-records-empty-text',
                'type' => 'text',
                'value' => 'Start tracking your vital signs by adding your first health data entry.',
            ],
            [
                'parent' => 'health_records',
                'label' => 'Insufficient Chart Data Text',
                'description' => 'Text shown when there is not enough data to show health charts.',
                'slug' => 'health-records-insufficient-chart-data-text',
                'type' => 'text',
                'value' => 'Add at least 2 health records to see trend charts.',
            ],

            // -----------------------------------------------------------------
            // Appointments
            // -----------------------------------------------------------------
            [
                'parent' => 'appointments',
                'label' => 'Patient Appointments Heading',
                'description' => 'Main heading on the patient appointments page.',
                'slug' => 'appointments-patient-heading',
                'type' => 'text',
                'value' => 'My Appointments',
            ],
            [
                'parent' => 'appointments',
                'label' => 'Patient Appointments Help Text',
                'description' => 'Helper text shown on the patient appointments page.',
                'slug' => 'appointments-patient-help-text',
                'type' => 'text',
                'value' => 'If you need help with an appointment, use the contact button below to send a message to the clinic.',
            ],
            [
                'parent' => 'appointments',
                'label' => 'No Upcoming Appointments Heading',
                'description' => 'Heading shown when the patient has no upcoming appointments.',
                'slug' => 'appointments-empty-upcoming-heading',
                'type' => 'text',
                'value' => 'No upcoming appointments',
            ],
            [
                'parent' => 'appointments',
                'label' => 'No Upcoming Appointments Text',
                'description' => 'Text shown when the patient has no upcoming appointments.',
                'slug' => 'appointments-empty-upcoming-text',
                'type' => 'text',
                'value' => 'You do not currently have any upcoming appointments scheduled.',
            ],

            // -----------------------------------------------------------------
            // Reminders
            // -----------------------------------------------------------------
            [
                'parent' => 'reminders',
                'label' => 'Reminders Heading',
                'description' => 'Main heading on the patient reminders page.',
                'slug' => 'reminders-heading',
                'type' => 'text',
                'value' => 'My Reminders',
            ],
            [
                'parent' => 'reminders',
                'label' => 'Reminders Intro Text',
                'description' => 'Short description on the patient reminders page.',
                'slug' => 'reminders-intro-text',
                'type' => 'text',
                'value' => 'Manage your vital data entry reminders',
            ],
            [
                'parent' => 'reminders',
                'label' => 'No Active Reminders Text',
                'description' => 'Text shown when no reminders are active.',
                'slug' => 'reminders-no-active-text',
                'type' => 'text',
                'value' => 'No active reminders',
            ],
            [
                'parent' => 'reminders',
                'label' => 'No Completed Reminders Text',
                'description' => 'Text shown when no reminders have been completed today.',
                'slug' => 'reminders-no-completed-text',
                'type' => 'text',
                'value' => 'No completed reminders today',
            ],
            [
                'parent' => 'reminders',
                'label' => 'Schedule Intro Text',
                'description' => 'Text shown above the patient reminder schedule.',
                'slug' => 'reminders-schedule-intro-text',
                'type' => 'text',
                'value' => 'You will receive reminders at:',
            ],

        ];

        $this->execute('DELETE FROM content_blocks');
        $this->table('content_blocks')->insert($data)->saveData();
    }
}
