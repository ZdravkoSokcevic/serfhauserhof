<section class="main search">
	<div class="inner">
	    <form action="" method="GET">
	        <input type="text" name="s" value="<?= array_key_exists('s', $_GET) ? $_GET['s'] : '' ?>" placeholder="" />
	        <button class="button"><i class="fa fa-search" aria-hidden="true"></i></button>
	    </form>
		<?php if(array_key_exists('details', $special_element_content) && is_array($special_element_content['details']) && array_key_exists('_details', $special_element_content['details']) && is_array($special_element_content['details']['_details']) && array_key_exists('matches', $special_element_content['details']['_details']) && count($special_element_content['details']['_details']['matches']) > 0){ ?>
		    <div class="articles" data-article-count="<?= count($special_element_content['details']['_details']['matches']) ?>">
			    <?php foreach($special_element_content['details']['_details']['matches'] as $match){ ?>
			        <article>
			        	<div class="search-text">
				            <h2 class="h1-like"><?= $match['details']['headline']; ?></h2>
				            <div class="short"><?= $this->Text->truncate($match['details']['content'], 250, ['html' => true]); ?></div>
			            </div>
			            <a href="<?= $this->Url->build(['node' => 'node:' . $match['node'], 'language' => $this->request->params['language']]); ?>" class="button"><?= __d('fe','more'); ?></a>
			        </article>
			    <?php } ?>
			</div>
		<?php } else if(array_key_exists('details', $special_element_content) && is_array($special_element_content['details']) && array_key_exists('_details', $special_element_content['details']) && is_array($special_element_content['details']['_details']) && array_key_exists('term', $special_element_content['details']['_details'])){ ?>
		    <div class="message"><?= __d('fe', 'No pages with the term "%s" found!', $special_element_content['details']['_details']['term']); ?></div>
		<?php } ?>
	</div>
</section>