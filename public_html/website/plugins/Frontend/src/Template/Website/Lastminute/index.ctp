<main class="page">
    <a class="anchor" name="content"></a>
    <h1><?= $content['headline']; ?></h1>
    <div class="content">
        <?= $content['content']; ?>
        <div class="clear"></div>
    </div>
</main>
<div class="form-wrapper">
	<div class="inner">
        <?php if($messages['show']){ ?>
            <?= $this->element('Frontend.Website/form-message', ['messages' => $messages]) ?>
        <?php }else{ ?>
            <?php $nr = 0; ?>
            <?php if(count($last_minute_offers) > 0){ ?>

            <!--// Form Start //-->
            <?= $this->CustomForm->create($form); ?>

            <?php
                $open = array_key_exists('id', $this->request->data) && strlen($this->request->data['id']) == 36 ? $this->request->data['id'] : false;
            ?>

            <?php $_form = false; $periods = []; ?>
            <?php foreach($last_minute_offers as $k => $last_minute_offer){ ?>

                <?php if(array_key_exists($last_minute_offer['room'], $room_details) && array_key_exists($last_minute_offer['room'], $room_details[$last_minute_offer['room']]['_details']['nodes'])){ ?>
                <section class="last-minute" data-rel="<?= $last_minute_offer['id']; ?>" data-offer="<?= $room_details[$last_minute_offer['room']]['_details']['infos'][$last_minute_offer['room']]['title']; ?>" data-room="<?= $last_minute_offer['room']; ?>">
                    <div class="preview">
                        <div class="img" style="background-position: <?= $room_details[$last_minute_offer['room']]['_details']['infos'][$last_minute_offer['room']]['images'][0]['details']['focus'][4]['css']; ?>; background-image: url('<?= $room_details[$last_minute_offer['room']]['_details']['infos'][$last_minute_offer['room']]['images'][0]['details']['seo'][4]; ?>');"></div>
                        <div class="txt">
                            <h2><?= $room_details[$last_minute_offer['room']]['_details']['infos'][$last_minute_offer['room']]['title']; ?></h2>
                            <?php if(array_key_exists('ranges', $last_minute_offer) && is_array($last_minute_offer['ranges']) && count($last_minute_offer['ranges']) > 0){ ?>
                            <div class="ranges">
                                <?php foreach($last_minute_offer['ranges'] as $range){ ?>
                                    <?php $txt = date("d.m.Y", $range['from']) . " - " . date("d.m.Y", $range['to']); ?>
                                    <?php $periods[$txt] = $txt; ?>
                                    <div class="range"><?= $txt; ?></div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            <div class="desc"><?= $last_minute_offer['price_desc']; ?></div>
                            <div class="value">
                                <?= __d('fe', 'now') . '&nbsp;&euro;&nbsp;' . number_format($last_minute_offer['price_value'],2,",","."); ?>
                            </div>
                            <div class="buttons">
                                <a class="button s2u room" href="<?= $this->Url->build(['node' => 'node:' . $room_details[$last_minute_offer['room']]['_details']['nodes'][$last_minute_offer['room']], 'language' => $this->request->params['language']]) ?>"><?= __d('fe', 'Room'); ?></a>
                                <a class="button s2u dark book" href="#"><?= __d('fe', 'Book'); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="more<?php echo $open == $last_minute_offer['id'] ? '' : ' hidden'; ?>">
                        <?= $last_minute_offer['content']; ?>
                        <div class="form">
                            <?php if(($open === false || $open == $last_minute_offer['id']) && $_form === false){ ?>
                            <div id="lmf" class="form-wrapper">
                                <input id="lmo" type="hidden" name="id" value="<?= $last_minute_offer['id']; ?>" />
                                <input id="lmor" type="hidden" name="room" value="<?= $last_minute_offer['room']; ?>" />
                                <?= $this->CustomForm->input('period', ['label' => __d('fe', 'Period'), 'options' => $periods, 'empty' => __d('fe', '-- Please select --')]); ?>
                                <div class="form-cols">
                                    <div class="col left">
                                        <?= $this->CustomForm->input('salutation', ['label' => __d('fe', 'Salutation'), 'empty' => __d('fe', '-- Please select --')]); ?>
                                        <?= $this->CustomForm->input('title', ['label' => __d('fe', 'Title')]); ?>
                                        <?= $this->CustomForm->input('firstname', ['label' => __d('fe', 'Firstname')]); ?>
                                        <?= $this->CustomForm->input('lastname', ['label' => __d('fe', 'Lastname')]); ?>
                                        <?= $this->CustomForm->input('email', ['label' => __d('fe', 'E-Mail')]); ?>    
                                    </div>
                                    <div class="col right">
                                        <?= $this->CustomForm->input('address', ['label' => __d('fe', 'Address')]); ?>
                                        <?= $this->CustomForm->input('zip', ['label' => __d('fe', 'ZIP')]); ?>
                                        <?= $this->CustomForm->input('city', ['label' => __d('fe', 'City')]); ?>
                                        <?= $this->CustomForm->input('country', ['label' => __d('fe', 'Country'), 'empty' => __d('fe', '-- Please select --')]); ?>
                                        <?= $this->CustomForm->input('phone', ['label' => __d('fe', 'Phone')]); ?>    
                                    </div>
                                </div>
                                <?= $this->CustomForm->input('message', ['label' => __d('fe', 'Message')]); ?>
                                <?= $this->element('Frontend.Website/newsletter') ?>
                                <?= $this->element('Frontend.Website/privacy') ?>
                                <?= $this->element('Frontend.Website/captcha') ?>
                                <a href="#" class="button s2u close"><?= __d('fe', 'Cancel'); ?></a>
                                <div class="clear"></div>
                            </div>
                            <?php $_form = true; ?>
                            <?php } ?>
                        </div>
                    </div>
                </section>
                <?php $nr++; ?>
                <?php } ?>
            <?php } ?>
            <?= $this->CustomForm->end(); ?>
            <!--// Form End //-->

            <?php } ?>
            <?php if($nr < 1){ ?>
                <div class="message space-top"><?= __d('fe', 'No offers available'); ?></div>
            <?php } ?>
        <?php } ?>
    </div>
</div>
<?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'media', 'wrapper' => 'media']) ?>