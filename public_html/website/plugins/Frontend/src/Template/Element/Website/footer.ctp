<?php
    use Cake\Core\Configure;
?>
<footer>
<?php if($this->request->params['pass'] && $this->request->params['pass'][0]=='7ed6ee86-ab74-4f62-ac7d-2700fa73dab1') echo ' <div style="margin: 80px;"> <script id="CookieDeclaration" src="https://consent.cookiebot.com/8205fc3d-fc02-4dea-9608-e73797f4c230/cd.js" type="text/javascript" async></script> </div>';?>
<!-- <script id="CookieDeclaration" src="https://consent.cookiebot.com/8205fc3d-fc02-4dea-9608-e73797f4c230/cd.js" type="text/javascript" async></script>-->
 <section class="top-links hidden">
        <a href="<?= $this->Url->build(['node' => Configure::read('config.default.offers.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Top offers'); ?><i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
        <a href="<?= $this->Url->build(['node' => Configure::read('config.default.newsletter.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Newsletter'); ?><i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
        <a href="<?= $this->Url->build(['node' => Configure::read('config.default.jobs.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Jobs'); ?><i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
        <a href="<?= $this->Url->build(['node' => Configure::read('config.default.brochures.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Brochures'); ?><i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
    </section>
    <section class="address-infos">
        <div class="opening-hours">
            <h3 class='mobile-only'><?= __d('fe', 'Opening hours'); ?></h3>
            <div>
                <h3><?= __d('fe', 'Opening hours'); ?></h3>
                <div>
                    <div class="winter">
                        <span class="headline"><?= Configure::read('config.opening-hours.hours-winter-headline-' . $this->request->params['language']); ?></span>
                        <span><?= Configure::read('config.opening-hours.hours-winter-' . $this->request->params['language']); ?></span>
                    </div>
                    <div class="restaurant">
                        <span class="headline"><?= Configure::read('config.opening-hours.hours-restaurant-headline-' . $this->request->params['language']); ?></span>
                        <span><?= Configure::read('config.opening-hours.hours-restaurant-' . $this->request->params['language']); ?></span>
                    </div>
                    <div class="summer">
                        <span class="headline"><?= Configure::read('config.opening-hours.hours-summer-headline-' . $this->request->params['language']); ?></span>
                        <span><?= Configure::read('config.opening-hours.hours-summer-' . $this->request->params['language']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="address">
            <img alt="<?= Configure::read('config.default.hotel') ?>" src="/frontend/img/logo.svg" />
            <span><?= Configure::read('config.default.hotel') ?></span>
            <div class="colums">
                <div class="left"><?= Configure::read('config.default.family-' . $this->request->params['language']) ?><br /><?= Configure::read('config.default.street-' . $this->request->params['language']) ?><br /><?= Configure::read('config.default.zip'); ?>&nbsp;<?= Configure::read('config.default.city-' . $this->request->params['language']) ?> <span class="state">&bull; <?= Configure::read('config.default.state-' . $this->request->params['language']) ?> </span>&bull; <?= Configure::read('config.default.country-' . $this->request->params['language']) ?></div>
                <div class="right"><a href="tel:<?= Configure::read('config.default.phone-plain') ?>">Tel. <?= Configure::read('config.default.phone') ?></a><br />Fax <?= Configure::read('config.default.fax') ?><br /><a href="mailto:<?= Configure::read('config.default.email') ?>"><?= Configure::read('config.default.email') ?></a></div>
            </div>
            <div class="links">
                <a href="https://www.facebook.com/hotelserfauserhof" alt="Serfauserhof facebook" target="_blank">
                    <img src="/frontend/img/facebook.png" class="footer-icons facebook">
                </a>
                <a href="https://www.instagram.com/hotel_serfauserhof/" target="_blank">
                    <img src="/frontend/img/instagram.png" alt="Serfauserhof instagram" class="footer-icons instagram">
                </a>
            </div>
        </div>
        <div class="map">
            <a href="<?= $this->Url->build(['node' => Configure::read('config.default.map.0.org'), 'language' => $this->request->params['language']]); ?>"><img class="<?= __d('fe', 'Map'); ?>" src="/frontend/img/footer-map.png" /></a>
        </div>
    </section>
    <section class="partner">
        <a target="_blank" class="tripadvisor" href="<?= Configure::read('config.links.tripadvisor-' . $this->request->params['language']); ?>" target="_blank"><img alt="Tripadvisor" src="/frontend/img/footer-tripadvisor.png" /><span><?= __d('fe', 'Show reviews'); ?><i class="fa fa-angle-right" aria-hidden="true"></i></span></a>
        <a target="_blank" class="holidaycheck" href="<?= Configure::read('config.links.holidaycheck-' . $this->request->params['language']); ?>" target="_blank"><img alt="HolidayCheck" src="/frontend/img/footer-holidaycheck.png" /><span><?= __d('fe', 'Show reviews'); ?><i class="fa fa-angle-right" aria-hidden="true"></i></span></a>
    </section>
    <section class="bottom-links">
        <a href="<?= $this->Url->build(['node' => Configure::read('config.default.privacy.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Privacy policy'); ?></a><span>|</span><a href="<?= $this->Url->build(['node' => Configure::read('config.default.terms.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Terms and conditions'); ?></a><span>|</span><a href="<?= $this->Url->build(['node' => Configure::read('config.default.imprint.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Imprint'); ?></a><span class="nl"><span>|</span><a href="<?= $this->Url->build(['node' => Configure::read('config.default.sitemap.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Sitemap'); ?></a><span>|</span><a href="<?= $this->Url->build(['node' => Configure::read('config.default.downloads.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Press'); ?></a><span>|</span><a href="<?= $this->Url->build(['node' => Configure::read('config.default.jobs.0.org'), 'language' => $this->request->params['language']]); ?>"><?= __d('fe', 'Jobs'); ?></a></span>
    </section>
</footer>
