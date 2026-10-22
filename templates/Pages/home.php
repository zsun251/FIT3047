<?php
/**
 * @var \App\View\AppView $this
 */

$this->assign('title', 'Home');
$this->Html->css('home', ['block' => true]);
?>

<main class="home-page">

    <!-- Intro Section -->
    <section class="home-intro" style="background-image: url('<?= h($this->Url->image($this->ContentBlock->imagePath('home-intro-image'))) ?>')">
        <div class="intro-overlay">
            <div class="container">
                <div class="intro-copy">
                    <h1 class="intro-title">
                        <?= $this->ContentBlock->text('home-intro-title') ?>
                    </h1>

                    <p class="intro-description">
                        <?= $this->ContentBlock->text('home-intro-description') ?>
                    </p>

                    <?= $this->Html->link(
                        '<i class="bi bi-person-plus"></i> ' . $this->ContentBlock->text('home-intro-button-text'),
                        ['controller' => 'Users', 'action' => 'register'],
                        [
                            'class' => 'intro-btn',
                            'escape' => false,
                            'role' => 'button',
                            'aria-label' => $this->ContentBlock->text('home-intro-button-aria-label')
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
                <span class="section-label"><?= $this->ContentBlock->text('home-benefits-label') ?></span>
                <h2><?= $this->ContentBlock->text('home-benefits-heading') ?></h2>
                <div class="section-line"></div>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi <?= h($this->ContentBlock->text('home-benefit-one-icon')) ?>"></i>
                    </div>

                    <div class="benefit-content">
                        <h3><?= $this->ContentBlock->text('home-benefit-one-title') ?></h3>
                        <p>
                            <?= $this->ContentBlock->text('home-benefit-one-text') ?>
                        </p>
                    </div>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi <?= h($this->ContentBlock->text('home-benefit-two-icon')) ?>"></i>
                    </div>

                    <div class="benefit-content">
                        <h3><?= $this->ContentBlock->text('home-benefit-two-title') ?></h3>
                        <p>
                            <?= $this->ContentBlock->text('home-benefit-two-text') ?>
                        </p>
                    </div>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <i class="bi <?= h($this->ContentBlock->text('home-benefit-three-icon')) ?>"></i>
                    </div>

                    <div class="benefit-content">
                        <h3><?= $this->ContentBlock->text('home-benefit-three-title') ?></h3>
                        <p>
                            <?= $this->ContentBlock->text('home-benefit-three-text') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Motivational Banner Section -->
    <section class="team-intro-section" style="background-image: url('<?= h($this->Url->image($this->ContentBlock->imagePath('home-motivational-banner-image'))) ?>')">
        <div class="team-intro-content">
            <h2><?= $this->ContentBlock->text('home-motivational-banner-heading') ?></h2>
            <p>
                <?= $this->ContentBlock->text('home-motivational-banner-text') ?>
            </p>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-section">
        <div class="container text-center">
            <h2 class="how-title"><?= $this->ContentBlock->text('home-how-heading') ?></h2>
            <div class="section-line"></div>

            <div class="how-grid">
                <div class="how-step">
                    <div class="how-icon">
                        <i class="bi <?= h($this->ContentBlock->text('home-how-step-one-icon')) ?>"></i>
                    </div>

                    <div class="how-connector"></div>

                    <h3>
                        <span>1</span>
                        <?= $this->ContentBlock->text('home-how-step-one-title') ?>
                    </h3>

                    <p>
                        <?= $this->ContentBlock->text('home-how-step-one-text') ?>
                    </p>
                </div>

                <div class="how-step">
                    <div class="how-icon">
                        <i class="bi <?= h($this->ContentBlock->text('home-how-step-two-icon')) ?>"></i>
                    </div>

                    <div class="how-connector"></div>

                    <h3>
                        <span>2</span>
                        <?= $this->ContentBlock->text('home-how-step-two-title') ?>
                    </h3>

                    <p>
                        <?= $this->ContentBlock->text('home-how-step-two-text') ?>
                    </p>
                </div>

                <div class="how-step">
                    <div class="how-icon">
                        <i class="bi <?= h($this->ContentBlock->text('home-how-step-three-icon')) ?>"></i>
                    </div>

                    <h3>
                        <span>3</span>
                        <?= $this->ContentBlock->text('home-how-step-three-title') ?>
                    </h3>

                    <p>
                        <?= $this->ContentBlock->text('home-how-step-three-text') ?>
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
                    <span class="section-label"><?= $this->ContentBlock->text('home-getting-started-label') ?></span>

                    <h2><?= $this->ContentBlock->text('home-getting-started-heading') ?></h2>

                    <p class="getting-started-text">
                        <?= $this->ContentBlock->text('home-getting-started-text') ?>
                    </p>

                    <?= $this->Html->link(
                        '<i class="bi bi-person-plus"></i> ' . $this->ContentBlock->text('home-getting-started-button-text'),
                        ['controller' => 'Users', 'action' => 'register'],
                        [
                            'class' => 'getting-started-btn',
                            'escape' => false,
                            'role' => 'button',
                            'aria-label' => $this->ContentBlock->text('home-intro-button-aria-label')
                        ]
                    ) ?>
                </div>

                <div class="getting-right">
                    <div class="timeline-line"></div>

                    <div class="timeline-item">
                        <div class="timeline-number">1</div>

                        <div class="timeline-content large-card">
                            <h3><?= $this->ContentBlock->text('home-getting-started-step-one-title') ?></h3>

                            <div class="feature-pill">
                                <i class="bi bi-check-circle"></i>
                                <?= $this->ContentBlock->text('home-getting-started-step-one-item-one') ?>
                            </div>

                            <div class="feature-pill">
                                <i class="bi bi-check-circle"></i>
                                <?= $this->ContentBlock->text('home-getting-started-step-one-item-two') ?>
                            </div>

                            <div class="feature-pill">
                                <i class="bi bi-check-circle"></i>
                                <?= $this->ContentBlock->text('home-getting-started-step-one-item-three') ?>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-number">2</div>

                        <div class="timeline-content">
                            <i class="bi bi-shield-check"></i>
                            <h3><?= $this->ContentBlock->text('home-getting-started-step-two-title') ?></h3>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-number">3</div>

                        <div class="timeline-content">
                            <i class="bi bi-graph-up-arrow"></i>
                            <h3><?= $this->ContentBlock->text('home-getting-started-step-three-title') ?></h3>
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

                <h2><?= $this->ContentBlock->text('home-faq-heading') ?></h2>

                <p>
                    <?= $this->ContentBlock->text('home-faq-intro-text') ?>
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
                                <span class="faq-question"><?= $this->ContentBlock->text('home-faq-one-question') ?></span>
                            </button>
                        </h2>

                        <div
                            id="faqOne"
                            class="accordion-collapse collapse show"
                            aria-labelledby="faqOneHeading"
                            data-bs-parent="#faqAccordion"
                        >
                            <div class="accordion-body">
                                <?= $this->ContentBlock->text('home-faq-one-answer') ?>
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
                                <span class="faq-question"><?= $this->ContentBlock->text('home-faq-two-question') ?></span>
                            </button>
                        </h2>

                        <div
                            id="faqTwo"
                            class="accordion-collapse collapse"
                            aria-labelledby="faqTwoHeading"
                            data-bs-parent="#faqAccordion"
                        >
                            <div class="accordion-body">
                                <?= $this->ContentBlock->text('home-faq-two-answer') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>
