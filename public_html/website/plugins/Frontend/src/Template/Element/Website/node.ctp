<a class="node-element" href="<?= $this->Url->build(['node' => 'node:' . $element_content['details']['node']['id'], 'language' => $this->request->params['language']]) ?>" data-route="<?= $element_content['details']['node']['route'] ?>" data-lang="<?= $this->request->params['language'] ?>">
	<?= $element_content['details']['element']['title'] ?>
</a>