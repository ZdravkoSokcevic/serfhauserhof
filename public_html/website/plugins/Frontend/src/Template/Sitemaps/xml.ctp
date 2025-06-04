<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
         xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?= $this->Url->build('/', true); ?></loc>
        <lastmod><?= date('c',time()); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>
<?php foreach($sitemap as $locale => $links){ ?>
    <?php if(count($links) > 0){ ?>
    <url>
        <loc><?= $this->Url->build('/' . $locale, true); ?></loc>
        <lastmod><?= date('c',time()); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>1</priority>
    </url>
    <?php foreach($links as $link){ ?>
    <url>
        <loc><?= $this->Url->build(['node' => 'node:' . $link['id'], 'language' => $locale], true); ?></loc>
        <lastmod><?= date('c',time()); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <?php } ?>
    <?php } ?>
<?php } ?>
</urlset>