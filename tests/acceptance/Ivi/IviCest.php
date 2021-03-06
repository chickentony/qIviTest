<?php

declare(strict_types=1);

namespace tests\acceptance\ivi;

use AcceptanceTester;
use Page\GoogleChromePage\MainPage;
use Page\GoogleChromePage\PicturesPage;
use Page\GoogleChromePage\SearchResultPage;

class IviCest
{
    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function findLinkToOfficialSiteFromPictures(AcceptanceTester $I): void
    {
        //arrange
        $googleChromeMainPage = new MainPage($I);
        $googleChromeSearchResultPage = new SearchResultPage($I);
        $googleChromePicturesPage = new PicturesPage($I);

        //act
        $I->amOnPage($googleChromeMainPage::URL);
        $googleChromeMainPage->search('ivi');
        $googleChromeSearchResultPage->clickOnPicturesTab();
        $googleChromePicturesPage->clickOnToolsButton();
        $googleChromePicturesPage->selectImageSize($googleChromePicturesPage::BIG_IMAGE_SIZE_DROPDOWN_VALUE);
        $linksCount = $googleChromePicturesPage->clickOnImageInListAndCheckImageHref(
            30, 3
        );

        //assert
        $I->assertEquals(3, $linksCount);
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function compareRatingOnSearchPageAndGooglePlayPage(AcceptanceTester $I): void
    {
        //arrange
        $googleChromeMainPage = new MainPage($I);
        $googleChromeSearchResultPage = new SearchResultPage($I);

        //act
        $I->amOnPage($googleChromeMainPage::URL);
        $googleChromeMainPage->search('ivi');
        $googleChromeSearchResultPage->checkPagesForRating(5);

        //assert
        $I->assertEquals(
            $googleChromeSearchResultPage->searchPageRating, $googleChromeSearchResultPage->marketPageRating
        );
    }

    /**
     * @param AcceptanceTester $I
     * @throws \Exception
     */
    public function findLinkToOfficialSiteInWikipediaArticle(AcceptanceTester $I): void
    {
        //arrange
        $googleChromeMainPage = new MainPage($I);
        $googleChromeSearchResultPage = new SearchResultPage($I);

        //act
        $I->amOnPage($googleChromeMainPage::URL);
        $googleChromeMainPage->search('ivi');
        $googleChromeSearchResultPage->checkPagesForLinksToWikipedia(5);

        //assert
        $I->assertNotEmpty($googleChromeSearchResultPage->linksFromWikipedia);
    }
}
