<?php

declare(strict_types=1);

namespace tests\acceptance\ivi;

use AcceptanceTester;
use Page\GoogleChromePage\GoogleChromeMainPage;
use Page\GoogleChromePage\PicturesPage;
use Page\GoogleChromePage\SearchResultPage;

class FindIviCest
{
    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function findIviInPictures(AcceptanceTester $I): void
    {
        //arrange
        $googleChromeMainPage = new GoogleChromeMainPage($I);
        $googleChromeSearchResultPage = new SearchResultPage($I);
        $googleChromePicturesPage = new PicturesPage($I);

        //act
        $I->amOnPage($googleChromeMainPage::URL);
        $googleChromeMainPage->search('ivi');
        $googleChromeSearchResultPage->clickOnPicturesTab();
        $googleChromePicturesPage->clickOnToolsButton();
        $googleChromePicturesPage->selectImageSize();

        //assert
        $googleChromePicturesPage->clickOnImageInListAndCheckImageHref(30, 3);
    }

    public function compareRatingOnSearchPageAndGooglePlayPage(AcceptanceTester $I): void
    {
        //arrange
        $googleChromeMainPage = new GoogleChromeMainPage($I);
        $googleChromeSearchResultPage = new SearchResultPage($I);

        //act
        $I->amOnPage($googleChromeMainPage::URL);
        $googleChromeMainPage->search('ivi');
        $googleChromeSearchResultPage->checkPagesForRating(5);

        //assert
        $I->assertEquals($googleChromeSearchResultPage->searchPageRating, $googleChromeSearchResultPage->marketPageRating);
    }

    public function findLinkToOfficialSiteInWikipedia(AcceptanceTester $I): void
    {
        //arrange
        $googleChromeMainPage = new GoogleChromeMainPage($I);
        $googleChromeSearchResultPage = new SearchResultPage($I);

        //act
        $I->amOnPage($googleChromeMainPage::URL);
        $googleChromeMainPage->search('ivi');
        $googleChromeSearchResultPage->checkPagesForLinksToWikipediaAndGetLinksToOfficialIviSiteFromArticles(5);

        //assert?
        var_dump($googleChromeSearchResultPage->linksFromWikipedia);
    }
}
