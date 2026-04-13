<!-- templates/Pages/home.php -->
<main class="flex-shrink-0">

    <!-- Hero Section -->
    <header class="py-5 bg-light text-center">
        <div class="container">
            <!-- Logo -->
            <img src="img/logo.png" alt="RespiraCare Logo" class="mb-4" style="max-height: 150px; width: auto;">

            <!-- Headline -->
            <h1 class="fw-bold mb-3 small-hero-text">
                Take Control of Your Health with RespiraCare Online
            </h1>

            <!-- Description -->
            <p class="lead mb-4">
                Easily monitor your lung health, oxygen levels, and symptoms from home. Stay in touch with your care team and get helpful reminders — no extra clinic visits required.
            </p>

            <!-- Call to Action -->
            <div>
                <?= $this->Html->link(
                    'Contact Us',
                    ['controller' => 'ContactMessages', 'action' => 'add'],
                    ['class' => 'btn btn-primary btn-lg px-5 py-3 fw-bold', 'role' => 'button', 'aria-label' => 'Contact Us']
                ) ?>
            </div>
        </div>
    </header>

    <!-- Key Benefits Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <i class="bi bi-heart-pulse display-4 text-primary mb-3" aria-hidden="true"></i>
                    <h3>Simple Remote Monitoring</h3>
                    <p>Track oxygen levels, lung function, and symptoms quickly from the comfort of your home.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-bell display-4 text-primary mb-3" aria-hidden="true"></i>
                    <h3>Helpful Reminders</h3>
                    <p>Never miss a check-in — get helpful reminders to log your symptoms vitals.</p>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-person-lines-fill display-4 text-primary mb-3" aria-hidden="true"></i>
                    <h3>Trusted Clinician Support</h3>
                    <p>Your healthcare team can review your data and provide guidance without in-person visits.</p>
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
                    <h5 class="fw-bold">1. Enter Your Data</h5>
                    <p>Input your vital signs and symptoms through our easy online interface.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold">2. Receive Guidance</h5>
                    <p>Your clinicians review your data and can provide advice or adjustments remotely.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold">3. Stay on Track</h5>
                    <p>Get reminders, alerts, and tips to help you manage COPD safely at home.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Contact Section -->
    <section class="py-5 text-center">
        <div class="container">
            <h2 class="fw-bold mb-3">Getting Started with RespiraCare</h2>
            <p class="mb-4">
                To get started with RespiraCare, please contact our team to arrange your access and onboarding.
            </p>            <?= $this->Html->link(
                'Contact Us',
                ['controller' => 'ContactMessages', 'action' => 'add'],
                ['class' => 'btn btn-primary btn-lg px-5 py-3 fw-bold', 'role' => 'button', 'aria-label' => 'Contact Us']
            ) ?>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Frequently Asked Questions</h2>
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
                            How often should I input my vitals?
                        </button>
                    </h2>
                    <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="faq2">
                        <div class="accordion-body">
                            Ideally, daily or as recommended by your clinician to help them monitor your condition effectively.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


</main>
