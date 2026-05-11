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
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="intro-copy">
                        <span class="intro-eyebrow">
                            <i class="bi bi-heart-pulse"></i>
                            Respiratory care made easier
                        </span>

                        <h1 class="intro-title">
                            Breathe easier with support that stays close.
                        </h1>

                        <p class="intro-lead">
                            RespiraCare helps you keep track of your respiratory health from home, with simple check-ins and support when it matters.
                        </p>

                        <div class="intro-actions">
                            <?= $this->Html->link(
                                '<i class="bi bi-person-plus"></i> Get Started',
                                ['controller' => 'Users', 'action' => 'register'],
                                [
                                    'class' => 'btn btn-primary btn-lg intro-primary-btn',
                                    'escape' => false,
                                    'role' => 'button',
                                    'aria-label' => 'Register for RespiraCare'
                                ]
                            ) ?>

                            <?= $this->Html->link(
                                '<i class="bi bi-book"></i> View Resources',
                                ['controller' => 'Resources', 'action' => 'browse'],
                                [
                                    'class' => 'btn btn-outline-primary btn-lg intro-secondary-btn',
                                    'escape' => false,
                                    'role' => 'button',
                                    'aria-label' => 'View respiratory health resources'
                                ]
                            ) ?>
                        </div>

                        <div class="intro-trust-row">
                            <div class="intro-trust-item">
                                <strong>Simple</strong>
                                <span>easy to use</span>
                            </div>
                            <div class="intro-trust-item">
                                <strong>Secure</strong>
                                <span>private records</span>
                            </div>
                            <div class="intro-trust-item">
                                <strong>Connected</strong>
                                <span>care team support</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="intro-visual-card">
                        <div class="intro-image-wrap">
                            <?= $this->Html->image(
                                'home.png',
                                [
                                    'alt' => 'Patient and clinician discussing respiratory health support',
                                    'class' => 'intro-image'
                                ]
                            ) ?>
                        </div>

                        <div class="intro-floating-card intro-floating-card-top">
                            <div class="floating-icon floating-icon-blue">
                                <i class="bi bi-droplet"></i>
                            </div>
                            <div>
                                <strong>Daily check-ins</strong>
                                <span>Keep your care on track</span>
                            </div>
                        </div>

                        <div class="intro-floating-card intro-floating-card-bottom">
                            <div class="floating-icon floating-icon-purple">
                                <i class="bi bi-heart-pulse"></i>
                            </div>
                            <div>
                                <strong>Helpful trends</strong>
                                <span>See changes over time</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="intro-soft-shape intro-soft-shape-one"></div>
        <div class="intro-soft-shape intro-soft-shape-two"></div>
    </section>

    <!-- Quick Pathways Section -->
    <section class="home-section home-pathways">
        <div class="container">
            <div class="section-heading text-center">
                <span class="section-kicker">What RespiraCare helps with</span>
                <h2>Small daily steps. Clearer care.</h2>
                <p>
                    Keep the important parts of respiratory care organised, so you and your care team can focus on what matters.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="pathway-card pathway-card-blue">
                        <div class="pathway-icon">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <h3>Track your health</h3>
                        <p>Record your check-ins in one simple place.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="pathway-card pathway-card-purple">
                        <div class="pathway-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h3>Stay on top of care</h3>
                        <p>Keep appointments, records, and updates easier to follow.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="pathway-card pathway-card-green">
                        <div class="pathway-icon">
                            <i class="bi bi-journal-medical"></i>
                        </div>
                        <h3>Learn at your pace</h3>
                        <p>Find helpful respiratory health information when you need it.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="home-section home-benefits">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5">
                    <span class="section-kicker">Why RespiraCare</span>
                    <h2 class="section-title">Support that feels simple, steady, and close to home.</h2>
                    <p class="section-text">
                        Managing a respiratory condition can feel overwhelming. RespiraCare makes daily monitoring calmer, clearer, and easier to share with the people supporting your care.
                    </p>

                    <?= $this->Html->link(
                        'Contact RespiraCare',
                        ['controller' => 'ContactMessages', 'action' => 'add'],
                        [
                            'class' => 'btn btn-primary btn-lg',
                            'role' => 'button',
                            'aria-label' => 'Contact RespiraCare'
                        ]
                    ) ?>
                </div>

                <div class="col-lg-7">
                    <div class="benefit-grid">
                        <div class="benefit-card">
                            <i class="bi bi-house-heart"></i>
                            <h3>Care from home</h3>
                            <p>Keep up with your health without adding more stress to your routine.</p>
                        </div>

                        <div class="benefit-card">
                            <i class="bi bi-shield-check"></i>
                            <h3>Private and secure</h3>
                            <p>Your health information stays protected and organised.</p>
                        </div>

                        <div class="benefit-card">
                            <i class="bi bi-person-video3"></i>
                            <h3>Connected support</h3>
                            <p>Your care team can stay informed when they need to.</p>
                        </div>

                        <div class="benefit-card">
                            <i class="bi bi-geo-alt"></i>
                            <h3>Made for Victoria</h3>
                            <p>Built with local patients, families, and care needs in mind.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="home-section home-how">
        <div class="container">
            <div class="section-heading text-center">
                <span class="section-kicker">How it works</span>
                <h2>Simple enough for everyday use</h2>
            </div>

            <div class="how-steps">
                <div class="how-step">
                    <div class="how-step-number">1</div>
                    <h3>Check in</h3>
                    <p>Add your health details when it suits your routine.</p>
                </div>

                <div class="how-step">
                    <div class="how-step-number">2</div>
                    <h3>Build a clearer picture</h3>
                    <p>Your records help show how things change over time.</p>
                </div>

                <div class="how-step">
                    <div class="how-step-number">3</div>
                    <h3>Stay supported</h3>
                    <p>Your care team has better information to guide ongoing care.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Local Trust Section -->
    <section class="home-section home-local">
        <div class="container">
            <div class="local-card">
                <div class="row align-items-center g-4">
                    <div class="col-lg-7">
                        <span class="section-kicker">Local respiratory support</span>
                        <h2>For people who want care to feel less complicated.</h2>
                        <p>
                            RespiraCare brings health records, resources, and care support together in a way that is easy to use and simple to understand.
                        </p>
                    </div>

                    <div class="col-lg-5">
                        <div class="local-checklist">
                            <h3>Helpful for people who need:</h3>
                            <ul>
                                <li><i class="bi bi-check-circle-fill"></i> Regular respiratory check-ins</li>
                                <li><i class="bi bi-check-circle-fill"></i> Support after hospital care</li>
                                <li><i class="bi bi-check-circle-fill"></i> A clearer symptom history</li>
                                <li><i class="bi bi-check-circle-fill"></i> Easier access from home</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="home-section home-faq">
        <div class="container">
            <div class="section-heading text-center">
                <span class="section-kicker">Questions</span>
                <h2>Common questions</h2>
            </div>

            <div class="faq-wrap">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                Is my health information safe?
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes. RespiraCare keeps your information secure and only available to authorised users.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                How often should I check in?
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Your care team can guide you. Many patients use RespiraCare as part of a simple daily or weekly routine.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                Does RespiraCare replace medical advice?
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                No. RespiraCare supports your care, but it does not replace medical advice or emergency care.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="home-final-cta">
        <div class="container">
            <div class="final-cta-card text-center">
                <span class="section-kicker">Start today</span>
                <h2>Ready to make respiratory care feel easier?</h2>
                <p>
                    Contact RespiraCare to ask a question, learn how onboarding works, or get help setting up.
                </p>

                <?= $this->Html->link(
                    '<i class="bi bi-send"></i> Schedule Your Onboarding',
                    ['controller' => 'ContactMessages', 'action' => 'add'],
                    [
                        'class' => 'btn btn-light btn-lg final-cta-btn',
                        'escape' => false,
                        'role' => 'button',
                        'aria-label' => 'Schedule RespiraCare onboarding'
                    ]
                ) ?>
            </div>
        </div>
    </section>

</main>
