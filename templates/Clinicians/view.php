<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Clinician $clinician
 */

$this->assign('title', __('Clinician Details'));
echo $this->Html->css('view-shared', ['block' => true]);
?>
<?= $this->element('page_header', ['backUrl' => ['action' => 'index']]) ?>

<div class="view-container">
    <div class="view-header">
        <h1><?= __('Clinician') ?> #<?= h($clinician->id) ?></h1>
        <div class="view-actions">
            <?= $this->Html->link(
                '<i class="bi bi-pencil"></i> ' . __('Edit'),
                ['action' => 'edit', $clinician->id],
                ['class' => 'btn btn-primary', 'escape' => false]
            ) ?>
            <?= $this->Form->postLink(
                '<i class="bi bi-trash"></i> ' . __('Delete'),
                ['action' => 'delete', $clinician->id],
                [
                    'confirm' => __('Are you sure you want to delete # {0}?', $clinician->id),
                    'class' => 'btn btn-outline-danger',
                    'escape' => false,
                ]
            ) ?>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><?= __('User') ?></div>
            <div class="detail-value"><?= $clinician->hasValue('user') ? $this->Html->link($clinician->user->username, ['controller' => 'Users', 'action' => 'view', $clinician->user->id]) : '' ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><?= __('Id') ?></div>
            <div class="detail-value"><?= $this->Number->format($clinician->id) ?></div>
        </div>
    </div>

    <div class="related-section">
        <h2><?= __('Related Patients') ?></h2>
        <?php if (!empty($clinician->patients)) : ?>
        <div class="table-responsive">
            <table class="rc-table">
                <thead>
                    <tr>
                        <th><?= __('Id') ?></th>
                        <th><?= __('User Id') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clinician->patients as $patient) : ?>
                    <tr>
                        <td><?= h($patient->id) ?></td>
                        <td><?= h($patient->user_id) ?></td>
                        <td class="actions"><?= $this->element('row_actions', ['controller' => 'Patients', 'id' => $patient->id]) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <p class="related-empty"><?= __('Nothing to show yet.') ?></p>
        <?php endif; ?>
    </div>

    <div class="related-section">
        <h2><?= __('Related Appointments') ?></h2>
        <?php if (!empty($clinician->appointments)) : ?>
        <div class="table-responsive">
            <table class="rc-table">
                <thead>
                    <tr>
                        <th><?= __('Id') ?></th>
                        <th><?= __('Patient Id') ?></th>
                        <th><?= __('Scheduled At') ?></th>
                        <th><?= __('Duration') ?></th>
                        <th><?= __('Status') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clinician->appointments as $appointment) : ?>
                    <tr>
                        <td><?= h($appointment->id) ?></td>
                        <td><?= h($appointment->patient_id) ?></td>
                        <td><?= h($appointment->scheduled_at) ?></td>
                        <td><?= h($appointment->duration) ?></td>
                        <td><?= h($appointment->status) ?></td>
                        <td class="actions"><?= $this->element('row_actions', ['controller' => 'Appointments', 'id' => $appointment->id]) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <p class="related-empty"><?= __('Nothing to show yet.') ?></p>
        <?php endif; ?>
    </div>

    <div class="related-section">
        <h2><?= __('Related Resources') ?></h2>
        <?php if (!empty($clinician->resources)) : ?>
        <div class="table-responsive">
            <table class="rc-table">
                <thead>
                    <tr>
                        <th><?= __('Id') ?></th>
                        <th><?= __('Title') ?></th>
                        <th><?= __('Type') ?></th>
                        <th><?= __('Body') ?></th>
                        <th><?= __('Url') ?></th>
                        <th><?= __('Thumbnail Url') ?></th>
                        <th><?= __('Status') ?></th>
                        <th><?= __('Published At') ?></th>
                        <th><?= __('Created At') ?></th>
                        <th><?= __('Updated At') ?></th>
                        <th class="actions"><?= __('Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clinician->resources as $resource) : ?>
                    <tr>
                        <td><?= h($resource->id) ?></td>
                        <td><?= h($resource->title) ?></td>
                        <td><?= h($resource->type) ?></td>
                        <td><?= h($resource->body) ?></td>
                        <td><?= h($resource->url) ?></td>
                        <td><?= h($resource->thumbnail_url) ?></td>
                        <td><?= h($resource->status) ?></td>
                        <td><?= h($resource->published_at) ?></td>
                        <td><?= h($resource->created_at) ?></td>
                        <td><?= h($resource->updated_at) ?></td>
                        <td class="actions"><?= $this->element('row_actions', ['controller' => 'Resources', 'id' => $resource->id]) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else : ?>
        <p class="related-empty"><?= __('Nothing to show yet.') ?></p>
        <?php endif; ?>
    </div>
</div>
