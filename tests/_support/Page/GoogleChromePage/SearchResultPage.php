<?php

declare(strict_types=1);

namespace Page\GoogleChromePage;

class SearchResultPage
{
    public const PICTURES_TAB = '//*[text()="Картинки"]';

    public const RESOURCES_LINK = '//div[@class="r"]//cite';

    public const RATING_CONTAINER = '//div[@class="dhIWPd f"]';

    public const NEXT_PAGE_LINK = '//a[@class="G0iuSb"]//span[text()="Следующая"]';

    public $searchPageRating;

    public $marketPageRating;

    public $linksFromWikipedia;

    public function __construct(\AcceptanceTester $tester)
    {
        $this->tester = $tester;
        $this->playMarketPage = new PlayMarketPage();
    }

    protected $tester;

    protected $playMarketPage;

    /**
     * @return PicturesPage
     * @throws \Exception
     */
    public function clickOnPicturesTab(): PicturesPage
    {
        $this->tester->waitForElementVisible(self::PICTURES_TAB);
        $this->tester->click(self::PICTURES_TAB);
        return new PicturesPage($this->tester);
    }

    public function getRatingsFromSearchPageAndMarketPage(): void
    {
        $links = $this->getResourcesLinksOnPage();
        $linkPattern = "/\bplay.google.com\b/";
        $ratingPattern = "((?:\d+,)\d+(?:\.\d+)?)";
        foreach ($links as $linkText) {
            if (preg_match($linkPattern, $linkText)) {
                $stringWithRatingOnSearchPage = $this->tester->grabTextFrom(self::RATING_CONTAINER);
                $this->tester->click($linkText);
                $this->tester->waitForElementVisible($this->playMarketPage::RATING_CONTAINER);
                $stringWithRatingOnMarketPage = $this->tester->grabAttributeFrom($this->playMarketPage::RATING_CONTAINER, 'aria-label');
                $this->tester->moveBack();
                $this->tester->wait(1);
                preg_match($ratingPattern, $stringWithRatingOnSearchPage, $searchPageRating);
                preg_match($ratingPattern, $stringWithRatingOnMarketPage, $marketPageRating);
                $this->searchPageRating[] = $searchPageRating[0];
                $this->marketPageRating[] = $marketPageRating[0];
            }
        }
    }

    public function checkPagesForRating(int $numberOfPages): void
    {
        for ($pageNumber = 1; $pageNumber < $numberOfPages; $pageNumber++) {
            $this->getRatingsFromSearchPageAndMarketPage();
            $this->clickOnNextPage();
        }
    }

    public function openWikipediaPages(): void
    {
        $links = $this->getResourcesLinksOnPage();
        $linkPattern = '/\bwikipedia.org\b/';
        $officialIviSitePattern = "/^http[s]?:\/\/(.*)(www.ivi.ru)/";
        foreach ($links as $link) {
            if (preg_match($linkPattern, $link)) {
                $this->tester->click($link);
                $articleLinks = $this->tester->grabMultiple('//div[@id="bodyContent"]//a', 'href');
                foreach ($articleLinks as $articleLink) {
                    if (preg_match($officialIviSitePattern, $articleLink, $matches)) {
                        $this->linksFromWikipedia[] = $matches;
                    }
                }
                $this->tester->moveBack();
            }
        }
    }

    public function checkPagesForLinksToWikipediaAndGetLinksToOfficialIviSiteFromArticles(int $numberOfPages)
    {
        for ($pageNumber = 1; $pageNumber < $numberOfPages; $pageNumber++) {
            $this->openWikipediaPages();
            $this->clickOnNextPage();
        }
    }

    private function clickOnNextPage(): SearchResultPage
    {
        $this->tester->scrollTo(self::NEXT_PAGE_LINK);
        $this->tester->click(self::NEXT_PAGE_LINK);
        return $this;
    }

    private function getResourcesLinksOnPage(): array
    {
        return $this->tester->grabMultiple(self::RESOURCES_LINK);
    }
}
