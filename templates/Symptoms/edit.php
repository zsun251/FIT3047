<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Symptom $symptom
 * @var string[]|\Cake\Collection\CollectionInterface $vitalData
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $symptom->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $symptom->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Symptoms'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="symptoms form content">
            <?= $this->Form->create($symptom) ?>
            <fieldset>
                <legend><?= __('Edit Symptom') ?></legend>
                <?php
                    echo $this->Form->control('symptom_name');
                    echo $this->Form->control('description');
                    echo $this->Form->control('vital_data._ids', ['options' => $vitalData]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
