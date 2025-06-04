<?php use Cake\Core\Configure; ?>

<?php 
$current_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>
<?php $current_url = urlencode($current_url); ?>

<div class="share-btns-wrap hidden-print">
	<div class="share-btns-darken">
		<a class="share-btns-close fa fa-times" href="javascript:void(0);"></a>
	</div>
	<div class="share-btns inner">
		
		<!-- Facebook -->
		<a class="share-btn share-btn-facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?= $current_url ?>" target="_blank">
			<span class="fa fa-facebook"></span>
			<span class="share-btn-text"><?= __d('fe','Share on Facebook') ?></span>
		</a>
		
		<!-- Google+ -->
		<a class="share-btn share-btn-google-plus share-btn-" href="https://plus.google.com/share?url=<?= $current_url ?>" target="_blank">
			<span class="fa fa-google-plus"></span>
			<span class="share-btn-text"><?= __d('fe','Share on Google+') ?></span>
		</a>
		
		<!-- Twitter -->
		<a class="share-btn share-btn-twitter" href="https://twitter.com/intent/tweet?url=<?= $current_url ?>" target="_blank">
			<span class="fa fa-twitter"></span>
			<span class="share-btn-text"><?= __d('fe','Share on Twitter') ?></span>
		</a>
		
		<!-- WhatsApp -->
		<a class="share-btn share-btn-whatsapp" href="whatsapp://send?text=<?= $current_url ?>">
			<span class="fa fa-whatsapp"></span>
			<span class="share-btn-text"><?= __d('fe','Share via Whatsapp') ?></span>
		</a>
		
		<!-- LinkedIn -->
		<a class="share-btn share-btn-linkedin" href="http://www.linkedin.com/shareArticle?mini=true&url=<?= $current_url ?>" target="_blank">
			<span class="fa fa-linkedin"></span>
			<span class="share-btn-text"><?= __d('fe','Share via LinkedIn') ?></span>
		</a>
		
		<!-- Pinterest -->
		<a class="share-btn share-btn-pinterest" href="http://pinterest.com/pin/create/bookmarklet/?is_video=false&url=<?= $current_url ?>" target="_blank">
			<span class="fa fa-pinterest"></span>
			<span class="share-btn-text"><?= __d('fe','Share via Pinterest') ?></span>
		</a>
		
		<!-- Tumblr -->
		<a class="share-btn share-btn-tumblr" href="http://www.tumblr.com/share/link?url=<?= $current_url ?>" target="_blank">
			<span class="fa fa-tumblr"></span>
			<span class="share-btn-text"><?= __d('fe','Share via Tumblr') ?></span>
		</a>
		
		<!-- Digg -->
		<a class="share-btn share-btn-digg" href="http://www.digg.com/submit?url=<?= $current_url ?>" target="_blank">
			<span class="fa fa-digg"></span>
			<span class="share-btn-text"><?= __d('fe','Share via digg') ?></span>
		</a>
		
		<!-- Reddit -->
		<a class="share-btn share-btn-reddit" href="http://reddit.com/submit?url=<?= $current_url ?>" target="_blank">
			<span class="fa fa-reddit"></span>
			<span class="share-btn-text"><?= __d('fe','Share via Reddit') ?></span>
		</a>
		
		<!-- Stumbleupon -->
		<a class="share-btn share-btn-stumbleupon" href="http://www.stumbleupon.com/submit?url=<?= $current_url ?>" target="_blank">
			<span class="fa fa-stumbleupon"></span>
			<span class="share-btn-text"><?= __d('fe','Share via Stumbleupon') ?></span>
		</a>
		
		<!-- Xing -->
		<a class="share-btn share-btn-xing" href="https://www.xing.com/spi/shares/new?url=<?= $current_url ?>" target="_blank">
			<span class="fa fa-xing"></span>
			<span class="share-btn-text"><?= __d('fe','Share via Xing') ?></span>
		</a>
		
		<!-- Vkontakte -->
		<a class="share-btn share-btn-vk" href="http://vkontakte.ru/share.php?url=<?= $current_url ?>" target="_blank">
			<span class="fa fa-vk"></span>
			<span class="share-btn-text"><?= __d('fe','Share via Vkontakte') ?></span>
		</a>
		
		<!-- E-Mail -->
		<a class="share-btn share-btn-mail" href="mailto:?body=<?= $current_url ?>">
			<span class="fa fa-envelope-o"></span>
			<span class="share-btn-text"><?= __d('fe','Share via Mail') ?></span>
		</a>
		
		<!-- Link kopieren -->
		<a class="share-btn share-btn-clipboard" href="#">
			<span class="fa fa-clipboard"></span>
			<span class="share-btn-text"><?= __d('fe','Copy url to clipboard') ?></span>
		</a>
		
		<!-- Drucken -->
		<a class="share-btn share-btn-print" href="#" onclick="window.print()">
			<span class="fa fa-print"></span>
			<span class="share-btn-text"><?= __d('fe','Print') ?></span>
		</a>
		
	</div>
</div>