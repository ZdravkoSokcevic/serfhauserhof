<?php if(is_array($special_element_content) && array_key_exists('details', $special_element_content)){ ?>
<section class="main wellness-map">
    <div class="inner">
        <div class="plan plan-<?= $special_element_content['details']['wellness']; ?>">
            <div></div>
        </div>
        <h2><?= $special_element_content['details']['headline']; ?></h2>
        <div class="legend enumeration">
            <?php foreach(array_filter(explode('<br />',$special_element_content['details']['textblock'])) as $li){ ?>
                <?php list($value, $text) = explode(":", $li, 2); ?>
                <ol><li value="<?= $value; ?>"><?= $text; ?></li></ol>
            <?php } ?>
        </div>
    </div>
</section>
<?php } ?>