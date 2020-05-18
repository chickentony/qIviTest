# qIviTest

### Запуск проекта

Для работы с проектом необходимо:

- скопировать (или склонировать) проект на свою машину
- установить на машину где будет запускаться проект php интерпретатор (для *nix систем лучше сделать через пакетный менеджер,
 для винды - скачать архив https://windows.php.net/download#php-7.4, распокавать архив в новую директорию на диске C, добавить путь до интерпретатора в переменную среды (https://www.youtube.com/watch?v=GWwhLfTRAV8)) 
- установить на машину где будет запускаться проект java для запуска selenium
- скачать chromedriver последней версии для вашей системы и положить в корень проекта
- запустить selenium командой ```java -jar selenium-server-standalone-3.141.59.jar```, selenium уже есть в проекте, можно выполнить команду в коре проекта (для корректной работы лучше так и сделать)

### Запуск тестов

В терминале, в корне проекта выполнить команду ```php codecept.phar run tests/acceptance/Ivi/FindIviCest.php```

Запуск одного теста ```php codecept.phar run tests/acceptance/Ivi/FindIviCest.php:compareRatingOnSearchPageAndGooglePlayPage```

Запуск одного теста в режиме дебага ```php codecept.phar run tests/acceptance/Ivi/FindIviCest.php:compareRatingOnSearchPageAndGooglePlayPage -vvv```

### Структура проекта (для лучшего понимания)

Все ui тесты находятся в папке tests/acceptance все PageObjects в папке tests/_support/Page.
Объект $I - это WebDriver, его инициализация происходит под капотом, при запуске тестов. Конфигурация драйвера происходит в файле - acceptance.suite.yml
