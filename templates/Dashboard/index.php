<?php
$this->assign('title','Dashboard');

// Load CSS assets and inject into layout CSS block
$this->start('datatables_css');

echo $this->Html->css([
    'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css',
    'datatable',
    'dashboard'
]);

$this->end();

// Load JS assets and inject into layout script block
$this->start('datatables_js');
echo $this->Html->script([
    'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
    'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js',
    'datatable-init'
]);
$this->end();

// Desktop Table Header builder
// Output buffering to pass into reusable table element
ob_start();
?>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Message</th>
        <th>Status</th>
        <th>Submitted</th>
        <th>Actions</th>
    </tr>
<?php
$thead = ob_get_clean();

ob_start();
?>

<?= $this->Html->link(
    'All',
    ['?' => ['status'=>'all']],
    [
        'class' =>
            'filter-btn ' .
            ($status==='all' ? 'active' : '')
    ]
) ?>
<?= $this->Html->link(
    'Unread',
    ['?' => ['status'=>'unread']],
    [
        'class' =>
            'filter-btn unread ' .
            ($status==='unread' ? 'active' : '')
    ]
) ?>

<?= $this->Html->link(
    'Read',
    ['?' => ['status'=>'read']],
    [
        'class' =>
            'filter-btn read ' .
            ($status==='read' ? 'active' : '')
    ]
) ?>

<?= $this->Html->link(
    'Archived',
    ['?' => ['status'=>'archived']],
    [
        'class' =>
            'filter-btn archived ' .
            ($status==='archived' ? 'active' : '')
    ]
) ?>

<?php
$filters = ob_get_clean();

// Desktop Table Body builder
// Creates rows for each submission
ob_start();
foreach($recent as $msg):
    ?>
    <tr>

        <!-- Name + phone -->
        <td class="nowrap name-cell">
            <?= h($msg->full_name) ?>

            <?php if($msg->has_phone): ?>
                <div class="phone-text">
                    <?= h($msg->contact_number) ?>
                </div>
            <?php endif; ?>

        </td>

        <!-- Email -->
        <td class="nowrap">
            <a href="mailto:<?= h($msg->email) ?>" class="email-link">
                <?= h($msg->email) ?>
            </a>
        </td>

        <!-- Message -->
        <td>
            <div class="msg-text">
                <?= h($msg->message) ?>
            </div>
        </td>

        <!-- Status -->
        <td>
        <span class="badge-status <?= $msg->status_badge_class ?>">
            <?= h($msg->status) ?>
        </span>
        </td>

        <!-- Submission datetime-->
        <td class="nowrap">
            <?= h($msg->formatted_date) ?>

            <div class="submitted-time">
                <?= h($msg->formatted_time) ?>
            </div>
        </td>

        <!-- Dashboard actions -->
        <td class="nowrap">

            <?= $this->Html->link(
                'View',
                ['controller'=>'ContactMessages','action'=>'view',$msg->id],
                ['class'=>'btn-action me-1']
            ) ?>

            <?php if($msg->status==='unread'): ?>

                <?= $this->Form->postLink(
                    'Mark Read',
                    ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id, 'read'],
                    ['class'=>'btn-action me-1']
                ) ?>

            <?php endif; ?>

            <?php if($msg->status==='read'): ?>

                <?= $this->Form->postLink(
                    'Archive',
                    ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id, 'archived'],
                    ['class'=>'btn-action']
                ) ?>

            <?php endif; ?>

            <?php if($msg->status==='read'): ?>

                <?= $this->Form->postLink(
                    'Undo Read',
                    ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id, 'unread'],
                    ['class'=>'btn-action']
                ) ?>

            <?php endif; ?>
            <?php if($msg->status==='archived'): ?>

                <?= $this->Form->postLink(
                    'Undo Archived',
                    ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id, 'read'],
                    ['class'=>'btn-action']
                ) ?>

            <?php endif; ?>

            <?= $this->Form->postLink(
                'Delete',
                ['controller'=>'ContactMessages','action'=>'delete',$msg->id],
                [
                    'class'=>'btn-action danger',
                    'confirm'=>'Delete this submission?',
                    'data' => ['redirect' => $this->Url->build(['controller' => 'Dashboard', 'action' => 'index'])]
                ]
            ) ?>

        </td>

    </tr>
<?php
endforeach;
$tbody = ob_get_clean();

// Mobile Card Layout Builder
ob_start();
foreach($recent as $msg):
    ?>

    <div class="sub-card" data-search="<?= h($msg->search_text) ?>">

        <!-- Card Header -->
        <div class="card-top">

            <div>
                <div class="card-name">
                    <?= h($msg->full_name) ?>
                </div>

                <?php if($msg->has_phone): ?>
                    <div class="card-phone">
                        <?= h($msg->contact_number) ?>
                    </div>
                <?php endif; ?>

            </div>

            <span class="badge-status <?= $msg->status_badge_class ?>">
            <?= h($msg->status) ?>
        </span>

        </div>

        <!-- Email -->
        <div class="card-field">
            <span class="card-field-label">Email</span>

            <span class="card-field-value">
            <a href="mailto:<?= h($msg->email) ?>">
                <?= h($msg->email) ?>
            </a>
        </span>
        </div>

        <!-- Message -->
        <div class="card-field">

            <span class="card-field-label">Message</span>

            <div class="card-message">
                <?= h($msg->message) ?>
            </div>

        </div>

        <!-- Date -->
        <div class="card-date">
            Submitted <?= h($msg->formatted_date) ?>
            at <?= h($msg->formatted_time) ?>
        </div>

        <!-- Actions -->
        <div class="card-actions">

            <?= $this->Html->link(
                'View',
                ['controller'=>'ContactMessages','action'=>'view',$msg->id,],
                ['class'=>'btn-action']
            ) ?>

            <?php if($msg->status==='unread'): ?>

                <?= $this->Form->postLink(
                    'Mark Read',
                    ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id, 'read'],
                    ['class'=>'btn-action me-1']
                ) ?>

            <?php endif; ?>

            <?php if($msg->status==='read'): ?>

                <?= $this->Form->postLink(
                    'Archive',
                    ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id, 'archived'],
                    ['class'=>'btn-action']
                ) ?>

            <?php endif; ?>

            <?php if($msg->status==='read'): ?>

                <?= $this->Form->postLink(
                    'Undo Read',
                    ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id, 'unread'],
                    ['class'=>'btn-action']
                ) ?>

            <?php endif; ?>
            <?php if($msg->status==='archived'): ?>

                <?= $this->Form->postLink(
                    'Undo Archived',
                    ['controller'=>'ContactMessages','action'=>'updateStatus',$msg->id, 'read'],
                    ['class'=>'btn-action']
                ) ?>

            <?php endif; ?>

            <?= $this->Form->postLink(
                'Delete',
                ['controller'=>'ContactMessages','action'=>'delete',$msg->id],
                [
                    'class'=>'btn-action danger',
                    'confirm'=>'Delete this submission?',
                    'data' => ['redirect' => $this->Url->build(['controller' => 'Dashboard', 'action' => 'index'])]]
            ) ?>

        </div>

    </div>

<?php
endforeach;
$mobileCards = ob_get_clean();
?>

<?php
$user = $this->Identity->get(); // fetch the logged-in user
$firstName = h($user->first_name);
$lastName  = h($user->last_name);
?>

<div class="dash-header">
    <h1 class="dash-title">
        Welcome, <em><?= $firstName . ' ' . $lastName ?></em>
    </h1>
</div>

    <!-- Stat Cards -->
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

<!-- Datatable Element-->
<?= $this->element('data_table',[
    'tableId'=>'recentTable',
    'tableTitle'=>'Recent Submissions',
//    'viewAllUrl'=>[
//        'controller'=>'ContactMessages',
//        'action'=>'index',
//    ],
    'searchPlaceholder'=>'Search submissions…',
    'thead'=>$thead,
    'tbody'=>$tbody,
    'mobileCards'=>$mobileCards,
    'filters'=>$filters,
    'emptyMessage'=>'No contact submissions found.'
]) ?>

<!-- Initialisation and configuration of DataTables and mobile cards-->

<?php $this->start('script'); ?>

    <script>

        $(function(){
            initDataTable('#recentTable', {
                order: [[4,'desc']],
                pageLength: 30,
                columnDefs: [{ orderable: false, targets: [2,5] }],
                language: { searchPlaceholder: "Search submissions..." }
            });

            initMobileCards({
                container:'#recentTableCards',
                search:'#recentTableSearch',
                info:'#recentTableInfo',
                pagination:'#recentTablePagination',
                empty:'#recentTableEmpty',
                perPage:8
            });

        });

    </script>

<?php $this->end(); ?>
