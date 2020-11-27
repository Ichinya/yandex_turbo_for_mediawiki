<?php


class cRSS
{
    var $items;
    var $header;
    var $footer;

    public function __construct()
    {
    }

    /*
      <rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/" xmlns:turbo="http://turbo.yandex.ru" version="2.0">
        <channel>
            <title>Название RSS-канала</title>
            <link>http://www.example.com/</link>
            <description>Краткое описание RSS-канала</description>
            <turbo:analytics type="Yandex" id="123456"></turbo:analytics>
            <turbo:adNetwork type="Yandex" id="идентификатор блока" turbo-ad-id="first_ad_place"></turbo:adNetwork>
            <turbo:adNetwork type="AdFox" turbo-ad-id="second_ad_place">
                <![CDATA[
                    <div id="идентификатор контейнера"></div>
                    <script>
                        window.Ya.adfoxCode.create({
                            ownerId: 123456,
                            containerId: 'идентификатор контейнера',
                            params: {
                                pp: 'g',
                                ps: 'cmic',
                                p2: 'fqem'
                            }
                        });
                    </script>
                ]]>
            </turbo:adNetwork>
            <yandex:related type="infinity">
                <link url="http://www.example.com/other-page1.html">Текст ссылки</link>
                <link url="http://www.example.com/other-page2.html">Текст ссылки</link>
            </yandex:related>
            <item turbo="true">
                <link>http://www.example.com/page1.html</link>
                <author>Иван Иванов</author>
                <category>Технологии</category>
                <pubDate>Sun, 29 Sep 2002 19:59:01 +0300</pubDate>
                <turbo:content>
                    <![CDATA[
                        <header>
                            <h1>Ресторан «Полезный завтрак»</h1>
                            <h2>Вкусно и полезно</h2>
                            <figure>
                                <img src="https://avatars.mds.yandex.net/get-sbs-sd/403988/e6f459c3-8ada-44bf-a6c9-dbceb60f3757/orig">
                            </figure>
                            <menu>
                                <a href="http://example.com/page1.html">Пункт меню 1</a>
                                <a href="http://example.com/page2.html">Пункт меню 2</a>
                            </menu>
                        </header>
                        <p>Как хорошо начать день? Вкусно и полезно позавтракать!</p>
                        <p>Приходите к нам на завтрак. Фотографии наших блюд ищите <a href="#">на нашем сайте</a>.</p>
                        <h2>Меню</h2>
                        <figure>
                            <img src="https://avatars.mds.yandex.net/get-sbs-sd/369181/49e3683c-ef58-4067-91f9-786222aa0e65/orig">
                            <figcaption>Омлет с травами</figcaption>
                        </figure>
                        <p>В нашем меню всегда есть свежие, вкусные и полезные блюда.</p>
                        <p>Убедитесь в этом сами.</p>
                        <button formaction="tel:+7(123)456-78-90" data-background-color="#5B97B0" data-color="white" data-primary="true">Заказать столик</button>
                        <div data-block="widget-feedback" data-stick="false">
                            <div data-block="chat" data-type="whatsapp" data-url="https://whatsapp.com"></div>
                            <div data-block="chat" data-type="telegram" data-url="http://telegram.com/"></div>
                            <div data-block="chat" data-type="vkontakte" data-url="https://vk.com/"></div>
                            <div data-block="chat" data-type="facebook" data-url="https://facebook.com"></div>
                            <div data-block="chat" data-type="viber" data-url="https://viber.com"></div>
                        </div>
                        <p>Наш адрес: <a href="#">Nullam dolor massa, porta a nulla in, ultricies vehicula arcu.</a></p>
                        <p>Фотографии — http://unsplash.com</p>
                    ]]>
                </turbo:content>
            </item>
        </channel>
    </rss>

    */
}