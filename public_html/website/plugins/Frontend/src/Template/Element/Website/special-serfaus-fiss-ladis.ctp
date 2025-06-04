<?php 
    $lang = 'de';
    if(array_key_exists('language', $this->request->params))
        $lang = $this->request->params['language'];

    $url = '';
    if($lang == 'de')
        $url = 'https://www.serfaus-fiss-ladis.at/de/Unterkuenfte/Hotel-Serfauserhof-Hotel-Serfaus_affad-28558';
    else $url = 'https://www.serfaus-fiss-ladis.at/en/Accommodation-list/Hotel-Serfauserhof-hotel-Serfaus_affad-28558';
?>
<section class="section-iframe">
    <iframe scrolling="auto" allowtransparency="true" src="<?= $url ?>" style="padding:1px;" frameborder="0" width="100%" height="800"></iframe>
</section>