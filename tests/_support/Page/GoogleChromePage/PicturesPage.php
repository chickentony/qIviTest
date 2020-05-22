<?php

declare(strict_types=1);

namespace Page\GoogleChromePage;

class PicturesPage
{
    /** @var string Ссылка у картинки */
    public const IMAGE_LINK_CONTAINER = '(//div[@class="v4dQwb"]//a)[1]';

    /** @var string Превью картинки при клике на картинку в выдаче */
    public const IMAGE_PREVIEW = '//div[@id="islsp"]';

    /** @var string Кнопка "Инструменты" */
    public const TOOLS_BTN = '//div[@class="PNyWAd ZXJQ7c"]';

    /** @var string Выпадающий спсок со значениями размеров картинок */
    public const IMAGE_SIZE_DROPDOWN = '//div[@class="gLW9ub"]';

    /** @var string Значение из выпадающего списка */
    public const BIG_IMAGE_SIZE_DROPDOWN_VALUE = '(//div[@class="Hm7Qac "]//span[@class="igM9Le"])[1]';

    /**
     * PicturesPage constructor.
     * @param \AcceptanceTester $tester
     */
    public function __construct(\AcceptanceTester $tester)
    {
        $this->tester = $tester;
    }

    /** @var \AcceptanceTester */
    protected $tester;

    /**
     * @param int $numberOfViewedPictures
     * @param int $expectedNumberOfLinks
     * @return int
     * Кликает на заданное число картинок в поисковой выдаче и проверяет на какой сайт ведет ссылка на картинке
     * @throws \Exception
     */
    public function clickOnImageInListAndCheckImageHref(
        int $numberOfViewedPictures,
        int $expectedNumberOfLinks
    ): int
    {
        $linksCount = 0;
        $pattern = "/^http[s]?:\/\/(.*)(www.ivi.ru)/";
        for ($i = 0; $i < $numberOfViewedPictures; $i++) {
            //xpath картинки, номер картинки меняется каждую итерацию
            $imageXPath = $this->generateXpathForImage($i);
            if ($this->openImagePreviewAndCheckLink($imageXPath, $pattern)) {
                $linksCount++;
            }
            if ($linksCount === $expectedNumberOfLinks) {
                break;
            }
        }

        return $linksCount;
    }

    /**
     * @param string $image
     * @param string $linkPattern
     * @return bool
     * @throws \Exception
     * Открывает превью картинки и возвращает true если ссылка с превью ведет на оффициальный сайт
     */
    private function openImagePreviewAndCheckLink(string $image, string $linkPattern): bool
    {
        $this->tester->click($image);
        $this->tester->waitForElementVisible(self::IMAGE_PREVIEW);
        $link = $this->tester->grabAttributeFrom(self::IMAGE_LINK_CONTAINER, 'href');
        preg_match($linkPattern, $link, $matches);

        return !empty($matches) && $matches[0] === 'https://www.ivi.ru';
    }

    /**
     * @return PicturesPage
     * @throws \Exception
     * Кликает на кнопку "Инструменты"
     */
    public function clickOnToolsButton(): PicturesPage
    {
        $this->tester->waitForElementVisible(self::TOOLS_BTN);
        $this->tester->click(self::TOOLS_BTN);
        return $this;
    }

    /**
     * @param string $imageSize
     * @return PicturesPage
     * @throws \Exception Выбирает размер картинок в выдаче
     */
    public function selectImageSize(string $imageSize): PicturesPage
    {
        $this->tester->wait(1);
        $this->tester->click(self::IMAGE_SIZE_DROPDOWN);
        $this->tester->waitForElementVisible($imageSize);
        $this->tester->click(self::BIG_IMAGE_SIZE_DROPDOWN_VALUE);
        return $this;
    }

    /**
     * @param int $pictureNumber
     * @return string
     * Генерирует xpath для картинки в поисковой выдаче, нумерация картинок начинается с нулевого элемента
     */
    private function generateXpathForImage(int $pictureNumber): string
    {
        return "//div[@id='islrg']//div[@data-ri='{$pictureNumber}']";
    }
}
