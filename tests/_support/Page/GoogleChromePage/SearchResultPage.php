<?php

declare(strict_types=1);

namespace Page\GoogleChromePage;

class SearchResultPage
{
    public const PICTURES_TAB = '//*[text()="Картинки"]';

    public const RESOURCES_LINK = '//div[@class="r"]//cite';

    public const RATING_CONTAINER = '//div[@class="dhIWPd f"]';

    public const FIRST_SEARCH_PAGE_NEXT_PAGE_LINK = '//a[@class="G0iuSb"]';

    public const NEXT_PAGE_LINK = '(//a[@class="G0iuSb"])[2]';

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
        $pageNumber = 1;
        while ($pageNumber < $numberOfPages) {
            $this->getRatingsFromSearchPageAndMarketPage();
            if ($pageNumber === 1) {
                $this->clickOnNextPage(self::FIRST_SEARCH_PAGE_NEXT_PAGE_LINK);
            }
            $this->clickOnNextPage(self::NEXT_PAGE_LINK);
            if ($pageNumber === $numberOfPages) {
                break;
            }
            $pageNumber++;
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
                    preg_match($officialIviSitePattern, $articleLink, $matches);
                    $this->linksFromWikipedia[] = $matches;
                }
                $this->tester->moveBack();
            }
        }
    }

    public function checkPagesForLinksToWikipediaAndGetLinksToOfficialIviSiteFromArticles(int $numberOfPages)
    {
        $pageNumber = 1;
        while ($pageNumber < $numberOfPages) {
            $this->openWikipediaPages();
            if ($pageNumber === 1) {
                $this->clickOnNextPage(self::FIRST_SEARCH_PAGE_NEXT_PAGE_LINK);
            }
            $this->clickOnNextPage(self::NEXT_PAGE_LINK);
            if ($pageNumber === $numberOfPages) {
                break;
            }
            $pageNumber++;
        }
    }

    private function clickOnNextPage(string $nextPageLocator): SearchResultPage
    {
        $this->tester->scrollTo($nextPageLocator);
        $this->tester->click($nextPageLocator);
        return $this;
    }

    private function getResourcesLinksOnPage(): array
    {
        return $this->tester->grabMultiple(self::RESOURCES_LINK);
    }
}
