<section class="newsletter hidden-print">
    <h3><?= __d('fe', 'Newsletter'); ?></h3>
    <?= $this->CustomForm->input('newsletter', ['type' => 'checkbox', 'label' => __d('fe', 'Yes, I want to receive a newsletter.'), 'id' => 'newsletter']); ?>
</section>