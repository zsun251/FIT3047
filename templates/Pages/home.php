<!-- templates/Pages/home.php -->
<main class="flex-shrink-0">

    <!-- Hero Section -->
    <!-- Hero Section -->
    <header class="hero-section">
        <div class="hero-overlay">

            <!-- Headline -->
            <h1 class="hero-title">
                Take Control of Your Health with RespiraCare Online
            </h1>

            <!-- Description -->
            <p class="hero-description">
                Easily monitor your lung health, oxygen levels, and symptoms
                from home. Stay in touch with your care team and get helpful
                reminders — no extra clinic visits required.
            </p>

            <!-- Call to Action -->
            <?= $this->Html->link(
                'Contact Us',
                ['controller' => 'ContactMessages', 'action' => 'add'],
                [
                    'class' => 'hero-btn',
                    'role' => 'button',
                    'aria-label' => 'Contact Us'
                ]
            ) ?>

        </div>
    </header>

    <!-- BENEFITS SECTION -->
    <section class="benefits-section">
        <div class="container">

            <div class="benefits-header">
                <span class="benefits-label">WHY RESPIRACARE?</span>

                <h2>
                    Simple support for everyday respiratory care
                </h2>

                <div class="benefits-line"></div>
            </div>

            <div class="benefits-grid">

                <!-- CARD 1 -->
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi bi-heart-pulse"></i>
                    </div>

                    <div class="benefit-content">
                        <h3>Simple Remote Monitoring</h3>

                        <p>
                            Track oxygen levels, lung function, and symptoms quickly
                            from the comfort of your home.
                        </p>
                    </div>
                </div>

                <!-- CARD 2 -->
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi bi-bell"></i>
                    </div>

                    <div class="benefit-content">
                        <h3>Helpful Reminders</h3>

                        <p>
                            Never miss a check-in — get helpful reminders to log
                            your symptoms and vitals.
                        </p>
                    </div>
                </div>

                <!-- CARD 3 -->
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>

                    <div class="benefit-content">
                        <h3>Trusted Clinician Support</h3>

                        <p>
                            Your healthcare team can review your data and provide
                            guidance without in-person visits.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Image Hero Section -->
    <section class="team-hero-section">
        <div class="team-hero-content">
            <h2>Better care,<br>together.</h2>
            <p>
                RespiraCare connects you with your care team for smarter,
                easier respiratory health management.
            </p>
        </div>
    </section>

    <section class="how-section">
        <div class="container text-center">

            <h2 class="how-title">How RespiraCare Works</h2>
            <div class="how-line"></div>

            <div class="how-grid">

                <div class="how-step">
                    <div class="how-icon">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>

                    <div class="how-connector"></div>
                    <h5><span>1</span> Enter Your Data</h5>
                    <p>Input your vital signs and symptoms through our easy online interface.</p>
                </div>

                <div class="how-step">
                    <div class="how-icon">
                        <i class="bi bi-clipboard2-pulse"></i>
                    </div>

                    <div class="how-connector"></div>
                    <h5><span>2</span> Receive Guidance</h5>
                    <p>Your clinicians review your data and can provide advice or adjustments remotely.</p>
                </div>

                <div class="how-step">
                    <div class="how-icon">
                        <i class="bi bi-bell"></i>
                    </div>
                    <h5><span>3</span> Stay on Track</h5>
                    <p>Get reminders, alerts, and tips to help you manage COPD safely at home.</p>
                </div>

            </div>
        </div>
    </section>

    <section class="getting-started-section">
        <div class="container">
            <div class="getting-started-card">

                <div class="getting-left">
                    <p class="section-label">GETTING STARTED</p>

                    <h2>Start your RespiraCare journey in just a few simple steps</h2>

                    <p class="getting-started-text">
                        Contact our healthcare team to receive your account details,
                        personalised onboarding, and support setting up your remote
                        respiratory monitoring.
                    </p>

                    <a href="/contact" class="getting-started-btn">
                        <i class="bi bi-chat-left-text"></i>
                        Contact Us
                    </a>
                </div>

                <div class="getting-right">
                    <div class="timeline-line"></div>

                    <div class="timeline-item">
                        <div class="timeline-number">1</div>
                        <div class="timeline-content large-card">
                            <h3>Contact our team</h3>

                            <div class="feature-pill">
                                <i class="bi bi-check-circle"></i>
                                Easy onboarding
                            </div>

                            <div class="feature-pill">
                                <i class="bi bi-check-circle"></i>
                                Secure patient access
                            </div>

                            <div class="feature-pill">
                                <i class="bi bi-check-circle"></i>
                                Remote monitoring support
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-number">2</div>
                        <div class="timeline-content">
                            <i class="bi bi-envelope"></i>
                            <h3>Receive your login details</h3>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-number">3</div>
                        <div class="timeline-content">
                            <i class="bi bi-graph-up-arrow"></i>
                            <h3>Start tracking your health</h3>
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

                <div class="faq-line"></div>
            </div>

            <div class="faq-wrapper">

                <div class="accordion custom-faq" id="faqAccordion">

                    <div class="accordion-item faq-card">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faqOne">

                                <span class="faq-number">−</span>

                                <span class="faq-question">
                                Is my health information safe?
                            </span>
                            </button>
                        </h2>

                        <div id="faqOne"
                             class="accordion-collapse collapse show"
                             data-bs-parent="#faqAccordion">

                            <div class="accordion-body">
                                Yes! RespiraCare uses secure encryption and only
                                authorized clinicians can access your data.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item faq-card">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faqTwo">

                                <span class="faq-number plus">+</span>

                                <span class="faq-question">
                                How often should I input my vitals?
                            </span>
                            </button>
                        </h2>

                        <div id="faqTwo"
                             class="accordion-collapse collapse"
                             data-bs-parent="#faqAccordion">

                            <div class="accordion-body">
                                Patients are usually encouraged to enter vitals
                                daily or as recommended by their clinician.
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>


</main>
