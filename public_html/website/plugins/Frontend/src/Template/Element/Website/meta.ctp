<!-- favicon //-->
<link rel="apple-touch-icon" sizes="180x180" href="/frontend/img/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/frontend/img/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/frontend/img/favicon-16x16.png">
<link rel="manifest" href="/frontend/img/manifest.json">
<link rel="mask-icon" href="/frontend/img/safari-pinned-tab.svg" color="#b19756">
<meta name="apple-mobile-web-app-title" content="Hotel Serfauser Hof ****">
<meta name="application-name" content="Hotel Serfauser Hof ****">
<meta name="theme-color" content="#ffffff">

<?php if(isset($content) && array_key_exists('meta', $content)){ ?>
<!-- description //-->
<meta name="description" content="<?= $content['meta']; ?>" />
<?php } ?>

<?php if(isset($seo) && array_key_exists('robots', $seo)){ ?>
<!-- robots //-->
<meta name="robots" content="<?= $seo['robots']; ?>" />
<?php } ?>

<?php if(isset($seo) && array_key_exists('canonical', $seo)){ ?>
<!-- canonical //-->
<link rel="canonical" href="<?= $seo['canonical']; ?>" />
<?php } ?>

<?= $this->element('Frontend.Website/geotagging', ['seo' => $seo, 'content' => $content]) ?>