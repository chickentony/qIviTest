<?php

declare(strict_types=1);

namespace Page\GoogleChromePage;

class PicturesPage
{
    /** @var string Ссылка у картинки */
    public const IMAGE_LINK_CONTAINER = '(//div[@class="v4dQwb"]//a)[1]';

    /** @var string Кнопка "Инструменты" */
    public const TOOLS_BTN = '//div[@class="PNyWAd ZXJQ7c"]';

    /** @var string Выпадающий спсок со значениями размеров картинок */
    public const IMAGE_SIZE_DROPDOWN = '//div[@class="gLW9ub"]';

    /** @var string Значение из выпадающего списка */
    public const BIG_IMAGE_SIZE_DROPDOWN_VALUE = '//div[@class="Hm7Qac "]//span[contains(text(), \'Большой\')]';

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
     * @param int $numberOfPicturesLeadingToTheSite
     * @return PicturesPage
     *
     */
    public function clickOnImageInListAndCheckImageHref(
        int $numberOfViewedPictures,
        int $numberOfPicturesLeadingToTheSite
    ): PicturesPage
    {
        $currentPicture = 0;
        for ($i = 0; $i < $numberOfViewedPictures; $i++) {
            $image = "//div[@id='islrg']//div[@data-ri='{$i}']";
            $this->tester->click($image);
            $this->tester->wait(2);
            $link = $this->tester->grabAttributeFrom(self::IMAGE_LINK_CONTAINER, 'href');
            $pattern = "/^http[s]?:\/\/(.*)(www.ivi.ru)/";
            preg_match($pattern, $link, $matches);
            if (!empty($matches) && $matches[0] === 'https://www.ivi.ru') {
                $currentPicture++;
            }
            if ($currentPicture === $numberOfPicturesLeadingToTheSite) {
                break;
            }
        }

        return $this;
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
}
