<?php

declare(strict_types=1);

namespace Page\GoogleChromePage;

use Page\GoogleChromePage\External\WikipediaPage;

class SearchResultPage
{
    /** @var string xpath вкладки "Картинки" */
    public const PICTURES_TAB = '//*[text()="Картинки"]';

    /** @var string xpath ссылки на ресурсы в поисковой выдаче */
    public const RESOURCES_LINK = '//div[@class="r"]//cite';

    /** @var string xpath котнейнер со строкой рейтинга */
    public const RATING_CONTAINER = '//div[@class="dhIWPd f"]';

    /** @var string xpath сссылки на следующую страницу */
    public const NEXT_PAGE_LINK = '//a[@class="G0iuSb"]//span[text()="Следующая"]';

    /** @var array Массив с рейтингамии со страниц поиска */
    public $searchPageRating;

    /** @var array Массив с рейтингами со стрницы play-market */
    public $marketPageRating;

    /** @var array Массив со ссылками на офф. сайт ivi */
    public $linksFromWikipedia;

    /**
     * SearchResultPage constructor.
     * @param \AcceptanceTester $tester
     */
    public function __construct(\AcceptanceTester $tester)
    {
        $this->tester = $tester;
        $this->playMarketPage = new PlayMarketPage();
        $this->wikipediaPage = new WikipediaPage();
    }

    /** @var \AcceptanceTester */
    protected $tester;

    /** @var PlayMarketPage */
    protected $playMarketPage;

    protected $wikipediaPage;

    /**
     * @return PicturesPage
     * @throws \Exception
     * Кликает на вкладку "Картинки"
     */
    public function clickOnPicturesTab(): PicturesPage
    {
        $this->tester->waitForElementVisible(self::PICTURES_TAB);
        $this->tester->click(self::PICTURES_TAB);
        return new PicturesPage($this->tester);
    }

    /**
     * @throws \Exception
     * Проходит по ссылкам на странице, ищет ссылку на play.google, берет ее рейтинг
     * переходит на play.google и берет рейтинг там. Сохраняет рейтинги в два разных массива.
     * ToDo: вынести действия над страницей с гуглом в отдельный метод
     */
    public function getRatingsFromSearchPageAndMarketPage(): void
    {
        $links = $this->getResourcesLinksOnPage();
        $linkPattern = "/\bplay.google.com\b/";
        $ratingPattern = "((?:\d+,)\d+(?:\.\d+)?)";
        foreach ($links as $link) {
            if (preg_match($linkPattern, $link)) {
                $stringWithRatingOnSearchPage = $this->tester->grabTextFrom(self::RATING_CONTAINER);
                $this->tester->click($link);
                $this->tester->waitForElementVisible($this->playMarketPage::RATING_CONTAINER);
                $stringWithRatingOnMarketPage = $this->tester->grabAttributeFrom($this->playMarketPage::RATING_CONTAINER, 'aria-label');
                $this->tester->moveBack();
                $this->tester->wait(1);
                //Из строк с рейтингами получаем непосредственно сам рейтинг(число)
                preg_match($ratingPattern, $stringWithRatingOnSearchPage, $searchPageRating);
                preg_match($ratingPattern, $stringWithRatingOnMarketPage, $marketPageRating);
                $this->searchPageRating[] = $searchPageRating;
                $this->marketPageRating[] = $marketPageRating;
            }
        }
    }

    /**
     * @param int $numberOfPages
     * @return SearchResultPage
     * @throws \Exception
     * Ходит по заданному количеству страниц и собирает рейтинг с этих страниц
     */
    public function checkPagesForRating(int $numberOfPages): SearchResultPage
    {
        for ($pageNumber = 1; $pageNumber < $numberOfPages; $pageNumber++) {
            $this->getRatingsFromSearchPageAndMarketPage();
            $this->clickOnNextPage();
        }
        return $this;
    }

    /**
     * @return SearchResultPage
     * Проходит по ссылкам на странице, ищет ссылку на страницу википедии, если она найдена переходит на страницу и ищет
     * ссылки на офф. сайт ivi, если ссылки найдены сохраняет их в массив
     * ToDO: вынести действия над википедией в отдельный метод (первый if)
     */
    private function findLinksToWikipediaAndSaveIt(): SearchResultPage
    {
        $links = $this->getResourcesLinksOnPage();
        $linkPattern = '/\bwikipedia.org\b/';
        $officialIviSitePattern = "/^http[s]?:\/\/(.*)(www.ivi.ru)/";
        //Цикл для всех ссылок на странице поисковой выдачи
        foreach ($links as $link) {
            //Если есть ссыслка на википедию, переходим туда
            if (preg_match($linkPattern, $link)) {
                $this->tester->click($link);
                $articleLinks = $this->tester->grabMultiple($this->wikipediaPage::LINKS_IN_ARTICLE, 'href');
                //Проходит по всем ссылка в статье и ищет совпадения по регулярному выражению
                foreach ($articleLinks as $articleLink) {
                    if (preg_match($officialIviSitePattern, $articleLink, $matches)) {
                        $this->linksFromWikipedia[] = $matches;
                    }
                }
                $this->tester->moveBack();
            }
        }
        return $this;
    }

    /**
     * @param int $numberOfPages
     * @return SearchResultPage
     * Ходит по заданному количеству страниц и ищет ссылки на википедию
     */
    public function checkPagesForLinksToWikipedia(int $numberOfPages): SearchResultPage
    {
        for ($pageNumber = 1; $pageNumber < $numberOfPages; $pageNumber++) {
            $this->findLinksToWikipediaAndSaveIt();
            $this->clickOnNextPage();
        }
        return $this;
    }

    /**
     * @return SearchResultPage
     * Кликает на следующую страницу поисковой выдачи
     */
    private function clickOnNextPage(): SearchResultPage
    {
        $this->tester->wait(1);
        $this->tester->scrollTo(self::NEXT_PAGE_LINK);
        $this->tester->click(self::NEXT_PAGE_LINK);
        return $this;
    }

    /**
     * @return array
     * Забирает ссылки на ресурсы со страницы поиска
     */
    private function getResourcesLinksOnPage(): array
    {
        return $this->tester->grabMultiple(self::RESOURCES_LINK);
    }
}
