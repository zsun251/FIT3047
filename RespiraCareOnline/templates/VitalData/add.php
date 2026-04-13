<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\VitalData $vitalDataEntity
 * @var \Cake\Collection\CollectionInterface|string[] $patients
 * @var \Cake\Collection\CollectionInterface|string[] $symptoms
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Vital Data'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="vitalData form content">
            <?= $this->Form->create($vitalDataEntity) ?>
            <fieldset>
                <legend><?= __('Add Vital Data') ?></legend>
                <?php
                    echo $this->Form->control('patient_id', ['options' => $patients]);
                    echo $this->Form->control('oxygen_saturation');
                    echo $this->Form->control('respiratory_rate');
                    echo $this->Form->control('lung_function');
                    echo $this->Form->control('blood_pressure');
                    echo $this->Form->control('pulse_oximetry');
                    echo $this->Form->control('weight');
                    echo $this->Form->control('recorded_at');
                    echo $this->Form->control('symptoms._ids', ['options' => $symptoms]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
