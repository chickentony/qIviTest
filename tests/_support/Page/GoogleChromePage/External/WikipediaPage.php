<?php

declare(strict_types=1);

namespace Page\GoogleChromePage\External;

class WikipediaPage
{
    /** @var string xpath ссылок в статье */
    public const LINKS_IN_ARTICLE = '//div[@id="bodyContent"]//a';
}
