<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Basic Project Template</h1>
    <br>
</p>


averia electronics test
-----------------------

~~~
https://gist.github.com/AlexGx/70c76dc2748a1487221fe4e70b033c70
~~~

### Установка

~~~
git clone https://github.com/vko-dp/averia-electronics-test.git
~~~

~~~
php composer.phar install
~~~

### Описание

1. Сделан согласно постановке простой шаблонизатор - https://github.com/vko-dp/averia-electronics-test/blob/master/models/templateEngine/Dummy.php
2. Т.к. проект на основе Yii 2 сделан рендерер работающий с данным шаблонизатором - https://github.com/vko-dp/averia-electronics-test/blob/master/models/templateEngine/DummyViewRenderer.php
3. Рендерер добавлен в конфиг веб приложения - https://github.com/vko-dp/averia-electronics-test/blob/master/config/web.php#L57 и теперь все шаблоны с расширением tpl будут скомпилированы с применением Dummy
4. На главной https://github.com/vko-dp/averia-electronics-test/blob/master/controllers/SiteController.php#L17 используем шаблон https://github.com/vko-dp/averia-electronics-test/blob/master/views/site/index.tpl и передаем параметры
5. На главной демонстрация того что описано в задании в "Термины"
6. Сделан консольный контроллер https://github.com/vko-dp/averia-electronics-test/blob/master/commands/RevertTplController.php в котором все параметры собраны описанные в задании в "Задача"
7. php yii revert-tpl/index - запускаем контроллер этой командой и видим результат работы для каждой пары параметров (шаблон, результат) - массив параметров либо соответствующее исключение
