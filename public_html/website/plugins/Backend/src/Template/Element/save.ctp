<div class="<?= isset($padding) && in_array($padding, ['top','bottom']) ? 'space-' . $padding : ''; ?>">
    <?php if(!isset($show) || $show == 'back'){ ?>
        <?= $this->Form->button(__d('be', 'Save an back'), ['name' => '_submit', 'value' => 'back', 'class' => 'submit multi']); ?>
    <?php } ?>
    <?php if(!isset($show) || $show == 'stay'){ ?>
        <?= $this->Form->button(__d('be', 'Save'), ['name' => '_submit', 'value' => 'stay', 'class' => 'submit']); ?>
    <?php } ?>
    <div class="clear"></div>
</div>