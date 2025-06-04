<?php if(is_array($special_element_content) && array_key_exists('details', $special_element_content)){ ?>
<?php
    
    $lines = [];
    $cols = [];
    $headlines = [];
    
    for($l = 1; $l < 10; $l++){
        if(array_key_exists('line' . $l,  $special_element_content['details']) && !empty($special_element_content['details']['line' . $l])){
            $lines[$l] = $special_element_content['details']['line' . $l];
        }
        
        for($c = 1; $c < 10; $c++){
            if(array_key_exists('col' . $c,  $special_element_content['details']) && !empty($special_element_content['details']['col' . $c])){
                
                $headlines[$c] = $special_element_content['details']['col' . $c];
                
                if(!array_key_exists($c, $headlines)){
                    $cols[$c] = [];
                }
                
                if(array_key_exists('value-' . $l . '-' . $c,  $special_element_content['details']) && !empty($special_element_content['details']['value-' . $l . '-' . $c])){
                    $cols[$c][$l] = $special_element_content['details']['value-' . $l . '-' . $c];
                }
                
            }
        }
    }
    
    $cols = array_filter($cols);
    foreach($lines as $k1 => $v1){
        $keep = false;
        foreach($cols as $k2 => $v2){
            if(array_key_exists($k1, $v2)){
                $keep = true;
            }
        }
        if($keep === false){
            unset($lines[$k1]);
        }
    }
    
?>
<?php if(count($lines) > 0 && count($cols) > 0){ ?>
<section class="main children-prices cols-<?= count($cols); ?> lines-<?= count($lines); ?>">
    <div class="inner">
        <a name="<?= $special_element_content['details']['anchor']; ?>" class="anchor editor"></a>
        <h2><?= $special_element_content['details']['headline']; ?></h2>
        <?= $special_element_content['details']['textblock']; ?>
        <div class="headlines">
            <div>&nbsp;</div>
            <?php foreach($cols as $cidx => $values){ ?>
                <div class="title"><?= $headlines[$cidx]; ?></div>
            <?php } ?>
        </div>        
        <div class="prices">
            <?php foreach($cols as $cidx => $values){ ?>
            <div class="column col-<?= $cidx; ?>">
                <div class="line top">
                    <div class="title"><?= $headlines[$cidx]; ?></div>
                </div>
                <?php foreach($lines as $lidx => $llabel){ ?>
                <div class="line line-<?= $lidx; ?>">
                    <div class="desc"><?= $llabel; ?></div>
                    <div class="value"><?php echo array_key_exists($lidx, $values)? $values[$lidx] : '--'; ?></div>
                </div>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
<?php } ?>
<?php } ?>