<?php

declare(strict_types=1);

namespace Page\GoogleChromePage;

use Facebook\WebDriver\WebDriverKeys;

class GoogleChromeMainPage
{
    /** @var string
     * URL страницы, т. к. основной url проекта задается в конфиге acceptance.suite.yml,
     * то на главной странице обращается к корню
     */
    public const URL = '/';

    /** @var string Локатор поисковой строки */
    public const SEARCH_STRING_INPUT = '//div[@class="RNNXgb"]//input';

    /**
     * GoogleChromeMainPage constructor.
     * @param \AcceptanceTester $tester
     */
    public function __construct(\AcceptanceTester $tester)
    {
        $this->tester = $tester;
    }

    /** @var \AcceptanceTester */
    protected $tester;

    /**
     * @param string $searchString
     * @return SearchResultPage
     * Выполняет поиск по заданной строке, искомая строка задается непосредственно из теста
     */
    public function search(string $searchString): SearchResultPage
    {
        $this->tester->clearField(self::SEARCH_STRING_INPUT);
        $this->tester->fillField(self::SEARCH_STRING_INPUT, $searchString);
        $this->tester->pressKey(self::SEARCH_STRING_INPUT, WebDriverKeys::ENTER);
        return new SearchResultPage($this->tester);
    }
}
