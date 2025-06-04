<div class="<?= strtolower($this->name); ?> list">
    <?php foreach($shortcuts as $shortcut){ ?>
        <?php if($shortcut['display']){ ?>
        <a href="<?= $this->Url->build($shortcut['url']); ?>" class="shortcut"><i class="fa fa-<?= $shortcut['icon']; ?>"></i><span><?= $shortcut['title']; ?></span></a>
        <?php } ?>
    <?php } ?>
    <div class="clear"></div>
</div>