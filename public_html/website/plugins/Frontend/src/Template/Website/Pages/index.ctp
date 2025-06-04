<main>
    <a class="anchor" name="content"></a>
    <h1><?= $content['headline']; ?></h1>
    <div class="content">
        <?= $content['content']; ?>
        <div class="clear"></div>
    </div>
</main>
<?= $this->element('Frontend.Website/media', ['media' => $content['media'], 'position' => 'media', 'wrapper' => 'media']) ?>