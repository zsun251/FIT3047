<!-- templates/Pages/home.php -->
<main class="flex-shrink-0">

    <!-- Hero Section -->
    <header class="py-5 bg-light text-center">
        <div class="container">
            <!-- Logo -->
            <img src="img/logo.png" alt="RespiraCare Logo" class="mb-4" style="max-height: 150px; width: auto;">

            <!-- Headline -->
            <h1 class="fw-bold mb-3 small-hero-text">
                Melbourne's Trusted Respiratory Health Monitoring
            </h1>

            <!-- Description -->
            <p class="lead mb-4">
                Supporting Victorians with chronic respiratory conditions through innovative home monitoring.
                Track your lung health, oxygen levels, and symptoms from home while staying connected to your local healthcare team.
            </p>

            <!-- Call to Action -->
            <div>
                <?= $this->Html->link(
                    'Get Started Today',
                    ['controller' => 'ContactMessages', 'action' => 'add'],
                    ['class' => 'btn btn-primary btn-lg px-5 py-3 fw-bold', 'role' => 'button', 'aria-label' => 'Get Started with RespiraCare']
                ) ?>
            </div>
        </div>
    </header>

    <!-- Key Benefits Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Why Victorian Patients Choose RespiraCare</h2>
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <i class="bi bi-heart-pulse display-4 text-primary mb-3" aria-hidden="true"></i>
                    <h3>Simple Remote Monitoring</h3>
                    <p>Track oxygen levels, lung function, and symptoms quickly from the comfort of your home.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-bell display-4 text-primary mb-3" aria-hidden="true"></i>
                    <h3>Helpful Reminders</h3>
                    <p>Stay on track with timely reminders to log your vitals and never miss an important health check.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-person-lines-fill display-4 text-primary mb-3" aria-hidden="true"></i>
                    <h3>Local Clinical Care</h3>
                    <p>Your Victorian healthcare team monitors your data and provides expert guidance remotely when you need it.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Image Hero Section -->
    <section class="py-5 text-center">
        <div class="container">
            <!--https://www.flickr.com/photos/68716695@N06/29609189022 -->
            <img src="img/home.png" alt="Happy and Healthy Patient with their Clinician" class="img-fluid rounded" style="max-height: 400px;">
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">How RespiraCare Works</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-3">
                        <div class="display-6 fw-bold text-primary mb-3">1</div>
                        <h5 class="fw-bold">Record Your Health Data</h5>
                        <p>Log your vital signs and symptoms through our simple, secure online platform.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <div class="display-6 fw-bold text-primary mb-3">2</div>
                        <h5 class="fw-bold">Your Team Reviews</h5>
                        <p>Victorian clinicians monitor your data and provide personalized care recommendations.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <div class="display-6 fw-bold text-primary mb-3">3</div>
                        <h5 class="fw-bold">Stay Healthy at Home</h5>
                        <p>Receive timely alerts, health tips, and support—reducing unnecessary clinic visits.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Local Trust Section -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="fw-bold mb-3">Supporting Victorian Communities</h2>
                    <p class="mb-3">
                        RespiraCare is proudly based in Victoria, serving patients across Melbourne and regional areas.
                        We understand the unique healthcare challenges facing Australians with chronic respiratory conditions.
                    </p>
                    <p class="mb-4">
                        Our platform connects you with local healthcare professionals who know and care about your wellbeing,
                        making quality respiratory care accessible from anywhere in Victoria.
                    </p>
                    <?= $this->Html->link(
                        'Start Your Journey',
                        ['controller' => 'ContactMessages', 'action' => 'add'],
                        ['class' => 'btn btn-primary btn-lg', 'role' => 'button']
                    ) ?>
                </div>
                <div class="col-md-6">
                    <div class="p-4 bg-light rounded">
                        <h4 class="fw-bold mb-3">Perfect For:</h4>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> COPD patients</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Post-hospitalization monitoring</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Reducing hospital readmissions</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Remote and regional patients</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-5 text-center bg-primary text-white">
        <div class="container">
            <h2 class="fw-bold mb-3">Ready to Take Control of Your Respiratory Health?</h2>
            <p class="lead mb-4">
                Join hundreds of Victorians already managing their respiratory health with confidence from home.
            </p>
            <?= $this->Html->link(
                'Schedule Your Onboarding',
                ['controller' => 'ContactMessages', 'action' => 'add'],
                ['class' => 'btn btn-light btn-lg px-5 py-3 fw-bold', 'role' => 'button', 'aria-label' => 'Schedule Onboarding']
            ) ?>
            <p class="mt-3 small">
                <i class="bi bi-telephone-fill me-2"></i>
                Our Melbourne-based team is here to help you get started
            </p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5 fw-bold">Frequently Asked Questions</h2>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq1">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                            Is my health information safe?
                        </button>
                    </h2>
                    <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes! RespiraCare uses secure encryption and only authorized clinicians can access your data.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="faq2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                            How often should I record my vitals?
                        </button>
                    </h2>
                    <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2">
                        <div class="accordion-body">
                            We recommend daily monitoring, or as advised by your healthcare provider.
                            Regular tracking helps your clinical team spot changes early and adjust your care plan when needed.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
