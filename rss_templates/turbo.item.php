<?php
/** @var array $data */
$page = $data['page'];
$config = $data['config'];
$breadcrumblist = $config['breadcrumblist'];
// формируем header
preg_match_all($re = '/<img.+?>/m', $page->text, $matches, PREG_SET_ORDER, 0);
$img = '';
if (!empty($matches[0][0]) && mb_strlen($matches[0][0]) > 10) {
    $img = $matches[0][0];
}
$breadcrumbHeader = '';
foreach ($breadcrumblist as $breadcrumb) {
    $breadcrumbHeader .= " <a href=\"{$config['link']}/{$breadcrumb['url']}\">{$breadcrumb['text']}</a>";
}
$header = "<header>
                <h1>{$page->title}</h1>
                <figure>
                    {$img}
                </figure>
               <div data-block=\"breadcrumblist\">
                   {$breadcrumbHeader}
                </div>
            </header>";

?>
<item turbo="true">
    <!-- Информация о странице -->
    <link><?= htmlspecialchars($page->url); ?></link>
    <turbo:source><?= htmlspecialchars($page->url); ?></turbo:source>
    <turbo:topic><?= $page->title; ?></turbo:topic>
    <pubDate><?
        $date = new DateTime($page->updateAt);
        echo $date->format(DATE_RFC822);
        ?></pubDate>
    <author><?= $page->user; ?></author>
    <metrics>
        <yandex schema_identifier="<?= $page->id; ?>">
            <breadcrumblist>
                <?
                foreach ($breadcrumblist as $breadcrumb) {
                    echo "<breadcrumb url=\"{$config['link']}/{$breadcrumb['url']}\" text=\"{$breadcrumb['text']}\" /></breadcrumb>";
                }
                ?>
            </breadcrumblist>
        </yandex>
    </metrics>
    <yandex:related></yandex:related>
    <turbo:content>
        <![CDATA[
        <?= $header; ?>
        <?= $page->text; ?>
        ]]>
    </turbo:content>
</item>