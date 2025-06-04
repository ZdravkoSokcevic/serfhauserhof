<?php if(isset($breadcrumbs) && is_array($breadcrumbs) && count($breadcrumbs) > 0){ ?>
<?php $pos = 1; ?>
<section class="breadcrumbs hidden-print">
    <ol itemscope itemtype="http://schema.org/BreadcrumbList">
        <?php foreach($breadcrumbs as $breadcrumb){ ?>
            <?php if($pos > 1){ ?>
            <i>|</i>
            <?php } ?>
            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <?php if($breadcrumb['linkable']){ ?>
                <a itemscope itemtype="http://schema.org/Thing" itemprop="item" href="<?= $this->Url->build(['node' => 'node:' . $breadcrumb['id'], 'language' => $this->request->params['language']], true); ?>">
                    <span itemprop="name"><?= $breadcrumb['content']; ?></span>
                </a>
                <meta itemprop="position" content="<?= $pos; ?>" />
                <?php }else{ ?>
                <span class="fake"><?= $breadcrumb['content']; ?></span>
                <?php } ?>
            </li>
            <?php $pos++; ?>
        <?php } ?>
    </ol>
</section>
<?php } ?>
<a class="anchor" name="breadcrumbs" id="before-content"></a>