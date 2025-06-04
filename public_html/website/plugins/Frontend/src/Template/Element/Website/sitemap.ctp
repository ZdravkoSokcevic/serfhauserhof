<ul>
<?php
    $level = isset($lvl) ? (int) $lvl : 1;
?>
<?php foreach($nodes as $pos => $node){ ?>
    <?php if(in_array($node['route'], ['hmo', 'iee'])) continue; ?>
    <?php if($level == 1 && $pos == 4){ ?>
    </ul><ul>
    <?php } ?>
    <li class="pos-<?= $pos; ?>">
    	<?php $link = $node['type']=='link' && isset($node['details']['link']) ? $node['details']['link'] : $this->Url->build(['node' => 'node:' . $node['id'], 'language' => $this->request->params['language']]); ?>
        <a href="<?= $link; ?>" target="<?php echo isset($node['details']['target']) ? $node['details']['target'] : '_parent'; ?>"><i class="fa fa-angle-double-right" aria-hidden="true"></i> <?= $node['content']; ?></a>
        <?php if(is_array($node['children']) && count($node['children']) > 0){ ?>
            <?php echo $this->element('Frontend.Website/sitemap', ['nodes' => $node['children'], 'lvl' => $level + 1]); ?>
        <?php } ?>
    </li>
<?php } ?>
</ul>
