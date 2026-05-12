<?php
///**
// * @var \App\View\AppView $this
// * @var \App\Model\Entity\Resource $resource
// * @var \Cake\Collection\CollectionInterface|string[] $clinicians
// */
//?>
<!--<div class="row">-->
<!--    <aside class="column">-->
<!--        <div class="side-nav">-->
<!--            <h4 class="heading">--><?php //= __('Actions') ?><!--</h4>-->
<!--            --><?php //= $this->Html->link(__('List Resources'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
<!--        </div>-->
<!--    </aside>-->
<!--    <div class="column column-80">-->
<!--        <div class="resources form content">-->
<!--            --><?php //= $this->Form->create($resource) ?>
<!--            <fieldset>-->
<!--                <legend>--><?php //= __('Add Resource') ?><!--</legend>-->
<!--                --><?php
//                    echo $this->Form->control('clinician_id', ['options' => $clinicians]);
//                    echo $this->Form->control('title');
//                    echo $this->Form->control('type');
//                    echo $this->Form->control('body');
//                    echo $this->Form->control('url');
//                    echo $this->Form->control('thumbnail_url');
//                    echo $this->Form->control('status');
//                    echo $this->Form->control('published_at', ['empty' => true]);
//                    echo $this->Form->control('created_at');
//                    echo $this->Form->control('updated_at');
//                ?>
<!--            </fieldset>-->
<!--            --><?php //= $this->Form->button(__('Submit')) ?>
<!--            --><?php //= $this->Form->end() ?>
<!--        </div>-->
<!--    </div>-->
<!--</div>-->








<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Resource $resource
 */
?>

<div class="resources form content">

    <?= $this->Form->create($resource) ?>

    <fieldset>
        <legend>Add Resource</legend>

        <?= $this->Form->control('title') ?>

        <?= $this->Form->control('type', [
            'options' => [
                'article' => 'Article',
                'video' => 'Video',
                'blog' => 'Blog',
                'link' => 'Link',
                'news' => 'News'
            ]
        ]) ?>

        <?= $this->Form->control('category', [
            'options' => [
                'respiratory' => 'Respiratory',
                'asthma' => 'Asthma',
                'copd' => 'COPD',
                'general' => 'General',
                'education' => 'Education'
            ]
        ]) ?>

        <?= $this->Form->control('body', [
            'type' => 'textarea',
            'rows' => 8,
            'style' => 'width: 100%; max-width: 100%;'
        ]) ?>

        <?= $this->Form->control('url', [
            'style' => 'width: 100%; max-width: 100%;'
        ]) ?>

        <?= $this->Form->control('thumbnail_url', [
            'style' => 'width: 100%; max-width: 100%;'
        ]) ?>

    </fieldset>

    <?= $this->Form->button(__('Save Resource')) ?>

    <?= $this->Form->end() ?>

</div>
