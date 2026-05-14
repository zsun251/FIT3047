<?php
/**
 * @var \App\View\AppView $this
 * @var array<string, array<\ContentBlocks\Model\Entity\ContentBlock>> $contentBlocksGrouped
 */

$this->assign('title', 'Content Blocks');

$this->Html->css('ContentBlocks.content-blocks', ['block' => true]);

$contentBlocksGrouped = $contentBlocksGrouped ?? [];

$slugify = function ($text) {
    return strtolower(trim((string)preg_replace('/[^A-Za-z0-9-]+/', '-', (string)$text), '-'));
};
?>

<div class="contentBlocks index content">
    <h3><?= __('Content Blocks') ?></h3>

    <?php if (!empty($contentBlocksGrouped)): ?>
        <div>
            Quick links
            <?php foreach (array_keys($contentBlocksGrouped) as $parent): ?>
                :: <a href="#<?= h($slugify($parent)) ?>"><?= h($parent) ?></a>
            <?php endforeach; ?>
        </div>

        <?php foreach ($contentBlocksGrouped as $parent => $contentBlocks): ?>
            <h4 class="content-blocks--list-subheading">
                <a href="#<?= h($slugify($parent)) ?>" id="<?= h($slugify($parent)) ?>">
                    <?= h($parent) ?>
                </a>
            </h4>

            <ul class="content-blocks--list-group">
                <?php foreach ($contentBlocks as $contentBlock): ?>
                    <li class="content-blocks--list-group-item">
                        <div class="content-blocks--text">
                            <div class="content-blocks--display-name">
                                <?= h($contentBlock->label) ?>
                            </div>

                            <div class="content-blocks--description">
                                <?= h($contentBlock->description) ?>
                            </div>
                        </div>

                        <div class="content-blocks--actions">
                            <?= $this->Html->link(
                                __('Edit'),
                                [
                                    'plugin' => false,
                                    'controller' => 'ContentBlocks',
                                    'action' => 'edit',
                                    $contentBlock->id
                                ]
                            ) ?>

                            <?php if (!empty($contentBlock->previous_value)): ?>
                                ::
                                <?= $this->Form->postLink(
                                    __('Restore'),
                                    [
                                        'plugin' => false,
                                        'controller' => 'ContentBlocks',
                                        'action' => 'restore',
                                        $contentBlock->id
                                    ],
                                    [
                                        'confirm' => __(
                                            "Are you sure you want to restore the previous version for this item?\n{0}/{1}\nNote: You cannot cancel this action!",
                                            $contentBlock->parent,
                                            $contentBlock->slug
                                        )
                                    ]
                                ) ?>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    <?php else: ?>
        <p><?= __('No content blocks are available. Run the content block seed to create editable content.') ?></p>
    <?php endif; ?>
</div>
