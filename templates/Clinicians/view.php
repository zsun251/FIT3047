<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Clinician $clinician
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Clinician'), ['action' => 'edit', $clinician->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Clinician'), ['action' => 'delete', $clinician->id], ['confirm' => __('Are you sure you want to delete # {0}?', $clinician->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Clinicians'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Clinician'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="clinicians view content">
            <h3><?= h($clinician->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $clinician->hasValue('user') ? $this->Html->link($clinician->user->username, ['controller' => 'Users', 'action' => 'view', $clinician->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($clinician->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Patients') ?></h4>
                <?php if (!empty($clinician->patients)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($clinician->patients as $patient) : ?>
                        <tr>
                            <td><?= h($patient->id) ?></td>
                            <td><?= h($patient->user_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Patients', 'action' => 'view', $patient->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Patients', 'action' => 'edit', $patient->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Patients', 'action' => 'delete', $patient->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $patient->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Appointments') ?></h4>
                <?php if (!empty($clinician->appointments)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Patient Id') ?></th>
                            <th><?= __('Scheduled At') ?></th>
                            <th><?= __('Duration') ?></th>
                            <th><?= __('Status') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($clinician->appointments as $appointment) : ?>
                        <tr>
                            <td><?= h($appointment->id) ?></td>
                            <td><?= h($appointment->patient_id) ?></td>
                            <td><?= h($appointment->scheduled_at) ?></td>
                            <td><?= h($appointment->duration) ?></td>
                            <td><?= h($appointment->status) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Appointments', 'action' => 'view', $appointment->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Appointments', 'action' => 'edit', $appointment->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Appointments', 'action' => 'delete', $appointment->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $appointment->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Resources') ?></h4>
                <?php if (!empty($clinician->resources)) : ?>
                <div class="table-responsive">
                    <table>
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
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Resources', 'action' => 'view', $resource->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Resources', 'action' => 'edit', $resource->id]) ?>
                                <?= $this->Form->postLink(
                                    __('Delete'),
                                    ['controller' => 'Resources', 'action' => 'delete', $resource->id],
                                    [
                                        'method' => 'delete',
                                        'confirm' => __('Are you sure you want to delete # {0}?', $resource->id),
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>