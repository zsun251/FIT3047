<?php
$this->assign('title','Dashboard');

// CSS
$this->start('datatables_css');
echo $this->Html->css([
    'https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css',
    'datatable',
    'dashboard'
]);
$this->end();

// JS
$this->start('datatables_js');
echo $this->Html->script([
    'https://cdn.datatables.net/2.1.8/js/dataTables.min.js',
    'https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js',
]);
$this->end();

$user = $this->Identity->get();
?>

    <div class="dash-header">
        <h1 class="dash-title">
            Welcome, <em><?= h($user->first_name . ' ' . $user->last_name) ?></em>
        </h1>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Total Submissions</div>
                <div class="stat-value"><?= h($stats['total']) ?></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Unread</div>
                <div class="stat-value stat-unread"><?= h($stats['unread']) ?></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Read</div>
                <div class="stat-value stat-read"><?= h($stats['read']) ?></div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-label">Archived</div>
                <div class="stat-value stat-arch"><?= h($stats['archived']) ?></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-3">
        <?= $this->Html->link('All', ['?' => ['status'=>'all']], [
            'class' => 'filter-btn ' . ($status==='all' ? 'active' : '')
        ]) ?>

        <?= $this->Html->link('Unread', ['?' => ['status'=>'unread']], [
            'class' => 'filter-btn unread ' . ($status==='unread' ? 'active' : '')
        ]) ?>

        <?= $this->Html->link('Read', ['?' => ['status'=>'read']], [
            'class' => 'filter-btn read ' . ($status==='read' ? 'active' : '')
        ]) ?>

        <?= $this->Html->link('Archived', ['?' => ['status'=>'archived']], [
            'class' => 'filter-btn archived ' . ($status==='archived' ? 'active' : '')
        ]) ?>
    </div>

    <!-- Table -->
    <div class="table-responsive d-none d-md-block">
        <table id="recentTable" class="table table-striped dt-hidden">
            <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($recent as $msg): ?>
                <tr>

                    <td class="nowrap name-cell">
                        <?= h($msg->full_name) ?>
                        <?php if ($msg->has_phone): ?>
                            <div class="phone-text"><?= h($msg->contact_number) ?></div>
                        <?php endif; ?>
                    </td>

                    <td class="nowrap">
                        <a href="mailto:<?= h($msg->email) ?>" class="email-link">
                            <?= h($msg->email) ?>
                        </a>
                    </td>

                    <td>
                        <div class="msg-text"><?= h($msg->message) ?></div>
                    </td>

                    <td>
            <span class="badge-status <?= $msg->status_badge_class ?>">
                <?= h($msg->status) ?>
            </span>
                    </td>

                    <td class="nowrap">
                        <?= h($msg->formatted_date) ?>
                        <div class="submitted-time"><?= h($msg->formatted_time) ?></div>
                    </td>

                    <td class="nowrap">

                        <?= $this->Html->link(
                            'View',
                            ['controller'=>'ContactMessages','action'=>'view',$msg->id],
                            ['class'=>'btn-action me-1']
                        ) ?>

                        <?php if ($msg->status === 'unread'): ?>
                            <?= $this->Form->postLink('Mark Read',
                                ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'read'],
                                ['class'=>'btn-action me-1']
                            ) ?>
                        <?php endif; ?>

                        <?php if ($msg->status === 'read'): ?>
                            <?= $this->Form->postLink('Archive',
                                ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'archived'],
                                ['class'=>'btn-action me-1']
                            ) ?>

                            <?= $this->Form->postLink('Undo Read',
                                ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'unread'],
                                ['class'=>'btn-action']
                            ) ?>
                        <?php endif; ?>

                        <?php if ($msg->status === 'archived'): ?>
                            <?= $this->Form->postLink('Undo Archived',
                                ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id,'read'],
                                ['class'=>'btn-action']
                            ) ?>
                        <?php endif; ?>

                        <?= $this->Form->postLink('Delete',
                            ['controller'=>'ContactMessages','action'=>'delete',$msg->id],
                            [
                                'class'=>'btn-action danger',
                                'confirm'=>'Delete this submission?',
                                'data' => [
                                    'redirect' => $this->Url->build([
                                        'controller'=>'Dashboard',
                                        'action'=>'index'
                                    ])
                                ]
                            ]
                        ) ?>

                    </td>

                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    <div class="d-md-none">
        <?php foreach ($recent as $msg): ?>
            <div class="sub-card">

                <div class="card-top">
                    <div>
                        <div class="card-name"><?= h($msg->full_name) ?></div>
                        <?php if ($msg->has_phone): ?>
                            <div class="card-phone"><?= h($msg->contact_number) ?></div>
                        <?php endif; ?>
                    </div>

                    <span class="badge-status <?= $msg->status_badge_class ?>">
            <?= h($msg->status) ?>
        </span>
                </div>

                <div class="card-field">
                    <strong>Email:</strong>
                    <a href="mailto:<?= h($msg->email) ?>"><?= h($msg->email) ?></a>
                </div>

                <div class="card-field">
                    <strong>Message:</strong>
                    <div><?= h($msg->message) ?></div>
                </div>

                <div class="card-date">
                    <?= h($msg->formatted_date) ?> at <?= h($msg->formatted_time) ?>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

<?php $this->start('script'); ?>
    <script>
        $(function(){
            $('#recentTable').DataTable({
                order: [[4,'desc']],
                pageLength: 10,
                columnDefs: [{ orderable: false, targets: [2,5] }],
                layout: {
                    topStart: 'pageLength',
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                }
            });
            $('#recentTable').removeClass('dt-hidden');
        });
    </script>
<?php $this->end(); ?>
