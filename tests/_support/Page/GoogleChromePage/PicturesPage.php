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

    /** @var int Количество ссылок на оффициальный сайт иви */
    public $countLinksToOfficialSite;

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
     * @return PicturesPage
     * Кликает на заданное число картинок в поисковой выдаче и проверяет на какой сайт ведет ссылка на картинке
     * Код написан не оптимально, явно есть места дя разделения и рефакторинга, но посредством стандартных методов
     * фреймворка решить задачу нельзя, а для написание хелперов нужно время.
     */
    public function clickOnImageInListAndCheckImageHref(
        int $numberOfViewedPictures,
        int $expectedNumberOfLinks
    ): PicturesPage
    {
        $linksCount = 0;
        //Цикл который просмотривает заданное кол-во картинок и ище в нем ссылку на офф. сайт ivi
        for ($i = 0; $i < $numberOfViewedPictures; $i++) {
            //xpath картинки, номер картинки меняется каждую итерацию
            $image = "//div[@id='islrg']//div[@data-ri='{$i}']";
            //Кликает на картинку что бы открыть превью
            $this->tester->click($image);
            $this->tester->wait(2);
            //Получает ссылку из превью картинки
            $link = $this->tester->grabAttributeFrom(self::IMAGE_LINK_CONTAINER, 'href');
            //Регулярка для поиска ссылки н офф. сайт
            $pattern = "/^http[s]?:\/\/(.*)(www.ivi.ru)/";
            //Проверка ссылки на соответсвие регулярке
            preg_match($pattern, $link, $matches);
            //Если массив с совпадениями не пустой и первый элемент массива ссылка на офф сайт, увеличивается счетчик ссылок
            if (!empty($matches) && $matches[0] === 'https://www.ivi.ru') {
                $linksCount++;
            }
            //Если счетчик ссылок равен заданному ожидаемому кол-ву ссылок прерываем цикл
            if ($linksCount === $expectedNumberOfLinks) {
                break;
            }
        }
        $this->countLinksToOfficialSite = $linksCount;

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
