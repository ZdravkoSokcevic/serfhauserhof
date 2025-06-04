<?php if(isset($type) && in_array($type, ['button','div','span']) && isset($icon) && isset($text)){ ?>
    <<?= $type; ?> title="<?= $text; ?>" class="icon <?= isset($cls) ? $cls : ''; ?>"><i class="fa fa-<?= $icon; ?>"></i></<?= $type; ?>>
<?php }else if(isset($icon) && isset($text) && isset($url) && is_array($url)){ ?>
    <a href="<?= $this->Url->build($url); ?>"<?= isset($target) && !empty($target) ? ' target="' . $target . '"' : ''; ?><?= isset($confirm) && !empty($confirm) ? ' onclick="return confirm(\'' . $confirm . '\')"' : ''; ?> title="<?= $text; ?>" class="icon <?= isset($cls) ? $cls : ''; ?>"><i class="fa fa-<?= $icon; ?>"></i></a>
<?php } ?>