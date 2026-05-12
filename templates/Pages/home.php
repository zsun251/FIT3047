<?php
/**
 * @var \App\View\AppView $this
 */

$this->assign('title', 'Home');
$this->Html->css('home', ['block' => true]);
?>

<main class="home-page">

    <!-- Intro Section -->
    <section class="home-intro">
        <div class="intro-overlay">
            <div class="container">
                <div class="intro-copy">
                    <h1 class="intro-title">
                        Take Control of Your Health With RespiraCare Online
                    </h1>

                    <p class="intro-description">
                        Easily monitor your lung health, oxygen levels, and symptoms from home.
                        Stay in touch with your care team and get helpful reminders, no extra clinic visits required.
                    </p>

                    <?= $this->Html->link(
                        '<i class="bi bi-person-plus"></i> Get Started',
                        ['controller' => 'Users', 'action' => 'register'],
                        [
                            'class' => 'intro-btn',
                            'escape' => false,
                            'role' => 'button',
                            'aria-label' => 'Register for RespiraCare'
                        ]
                    ) ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="container">
            <div class="benefits-header">
                <span class="section-label">Why RespiraCare?</span>
                <h2>Simple Support for Everyday Respiratory Care</h2>
                <div class="section-line"></div>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>

                    <div class="benefit-content">
                        <h3>Simple Remote Monitoring</h3>
                        <p>
                            Track oxygen levels, lung function, and symptoms quickly from the comfort of your home.
                        </p>
                    </div>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi bi-bell"></i>
                    </div>

                    <div class="benefit-content">
                        <h3>Helpful Reminders</h3>
                        <p>
                            Never miss a check-in, get helpful reminders to log your symptoms and vitals.
                        </p>
                    </div>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>

                    <div class="benefit-content">
                        <h3>Trusted Clinician Support</h3>
                        <p>
                            Your healthcare team can review your data and provide guidance without in-person visits.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Image Section -->
    <section class="team-intro-section">
        <div class="team-intro-content">
            <h2>Better Care,<br>Together.</h2>
            <p>
                RespiraCare connects you with your care team for smarter, easier respiratory health management.
            </p>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-section">
        <div class="container text-center">
            <h2 class="how-title">How RespiraCare Works</h2>
            <div class="section-line"></div>

            <div class="how-grid">
                <div class="how-step">
                    <div class="how-icon">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>

                    <div class="how-connector"></div>

                    <h3>
                        <span>1</span>
                        Enter Your Data
                    </h3>

                    <p>
                        Input your vital signs and symptoms through our easy online interface.
                    </p>
                </div>

                <div class="how-step">
                    <div class="how-icon">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>

                    <div class="how-connector"></div>

                    <h3>
                        <span>2</span>
                        Receive Guidance
                    </h3>

                    <p>
                        Your clinicians review your data and can provide advice or adjustments remotely.
                    </p>
                </div>

                <div class="how-step">
                    <div class="how-icon">
                        <i class="bi bi-bell"></i>
                    </div>

                    <h3>
                        <span>3</span>
                        Stay On Track
                    </h3>

                    <p>
                        Get reminders, alerts, and tips to help you manage COPD safely at home.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Getting Started Section -->
    <section class="getting-started-section">
        <div class="container">
            <div class="getting-started-card">
                <div class="getting-left">
                    <span class="section-label">Getting Started</span>

                    <h2>Start Your RespiraCare Journey In Just A Few Simple Steps</h2>

                    <p class="getting-started-text">
                        Register your account and, once approved, use RespiraCare to track your respiratory health from home and stay connected with your clinicians for monitoring support.                    </p>

                    <?= $this->Html->link(
                        '<i class="bi bi-person-plus"></i> Get Started',
                        ['controller' => 'Users', 'action' => 'register'],
                        [
                            'class' => 'getting-started-btn',
                            'escape' => false,
                            'role' => 'button',
                            'aria-label' => 'Register for RespiraCare'
                        ]
                    ) ?>
                </div>

                <div class="getting-right">
                    <div class="timeline-line"></div>

                    <div class="timeline-item">
                        <div class="timeline-number">1</div>

                        <div class="timeline-content large-card">
                            <h3>Register Your Account</h3>

                            <div class="feature-pill">
                                <i class="bi bi-check-circle"></i>
                                Enter Your Details
                            </div>

                            <div class="feature-pill">
                                <i class="bi bi-check-circle"></i>
                                Submit Your Registration
                            </div>

                            <div class="feature-pill">
                                <i class="bi bi-check-circle"></i>
                                Secure Patient Access
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-number">2</div>

                        <div class="timeline-content">
                            <i class="bi bi-shield-check"></i>
                            <h3>Wait For Approval</h3>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-number">3</div>

                        <div class="timeline-content">
                            <i class="bi bi-graph-up-arrow"></i>
                            <h3>Start Tracking Your Health</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="faq-header">
                <div class="faq-icon">
                    <i class="bi bi-question-circle"></i>
                </div>

                <h2>Frequently Asked Questions</h2>

                <p>
                    Find quick answers to common questions about RespiraCare.
                </p>

                <div class="section-line"></div>
            </div>

            <div class="faq-wrapper">
                <div class="accordion custom-faq" id="faqAccordion">
                    <div class="accordion-item faq-card">
                        <h2 class="accordion-header" id="faqOneHeading">
                            <button
                                class="accordion-button"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faqOne"
                                aria-expanded="true"
                                aria-controls="faqOne"
                            >
                                <span class="faq-toggle" aria-hidden="true"></span>
                                <span class="faq-question">Is My Health Information Safe?</span>
                            </button>
                        </h2>

                        <div
                            id="faqOne"
                            class="accordion-collapse collapse show"
                            aria-labelledby="faqOneHeading"
                            data-bs-parent="#faqAccordion"
                        >
                            <div class="accordion-body">
                                Yes. RespiraCare keeps your information secure and only available to authorised users.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item faq-card">
                        <h2 class="accordion-header" id="faqTwoHeading">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faqTwo"
                                aria-expanded="false"
                                aria-controls="faqTwo"
                            >
                                <span class="faq-toggle" aria-hidden="true"></span>
                                <span class="faq-question">How Often Should I Input My Vitals?</span>
                            </button>
                        </h2>

                        <div
                            id="faqTwo"
                            class="accordion-collapse collapse"
                            aria-labelledby="faqTwoHeading"
                            data-bs-parent="#faqAccordion"
                        >
                            <div class="accordion-body">
                                Many patients are asked to check in up to four times a day, but you should always follow the schedule recommended by your clinician or healthcare provider.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
