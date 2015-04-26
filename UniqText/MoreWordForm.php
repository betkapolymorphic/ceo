<?php

// Подключите файл common.php. phpmorphy-0.3.2 - для версии 0.3.2,
// если используется иная версия исправьте код.
require_once( './phpmorphy-0.3.7/src/common.php"');


function transform_word(phpMorphy $morphy, $original, $replacement, $strict = false) {
    $result_forms = array();

    // ищем слово в словаре, получаем коллекцию парадигм, в которые входит слово
    if(false === ($collection = $morphy->findWord($original))) {
        throw new Exception("Can`t find $original word");
    }

    // ищем в словаре слово на которве бедкм менять
    if(false === ($collection_repl = $morphy->findWord($replacement))) {
        throw new Exception("Can`t find $original word");
    }


    // омонимия на уровне разных частей речи - ключ, лук, душа, стали и т.п.
    foreach($collection as $descriptor) {
        // омонимия внутри форм одного слова
        // стол имеет одинаковые формы для именительного и винительного падежей
        foreach($descriptor->getFoundWordForm() as $form) {
            // запоминаем граммемы и часть речи для $original
            $grammems = $form->getGrammems();
            $part_of_speech = $form->getPartOfSpeech();

            // ищем форму в $collection_repl c этими характеристиками

            // Ищем в коллекции по части речи
            foreach($collection_repl->getByPartOfSpeech($part_of_speech) as $descriptor_repl) {
                foreach($descriptor_repl->getWordFormsByGrammems($grammems) as $form_repl) {
                    // ВСЕ граммемы для каждой найденной формы должны совпадать с $grammems (если $strict === true)
                    if(!$strict || $form_repl->getGrammems() == $grammems) {
                        // ОК, нашли форму
                        $result_forms[$form_repl->getWord()] = 1;
                    }
                }
            }
        }
    }

    return array_keys($result_forms);
}
function getMorphyObject()
{
    try {

// Укажите путь к каталогу со словарями
        $dir = './bin';

// Укажите, для какого языка будем использовать словарь.
// Язык указывается как ISO3166 код страны и ISO639 код языка,
// разделенные символом подчеркивания (ru_RU, uk_UA, en_EN, de_DE и т.п.)

        $lang = 'ru_RU';

// Укажите опции
// Список поддерживаемых опций см. ниже
        $opts = array(
            'storage' => PHPMORPHY_STORAGE_FILE,
        );
        return new phpMorphy($dir, $lang, $opts);
    } catch (phpMorphy_Exception $e) {
        die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
    }
}
function getMorph($word)
{
    $word = mb_strtoupper($word,"UTF-8");
// создаем экземпляр класса phpMorphy
// обратите внимание: все функции phpMorphy являются throwable т.е.
// могут возбуждать исключения типа phpMorphy_Exception (конструктор тоже)
    try {

// Укажите путь к каталогу со словарями
        $dir = './bin';

// Укажите, для какого языка будем использовать словарь.
// Язык указывается как ISO3166 код страны и ISO639 код языка,
// разделенные символом подчеркивания (ru_RU, uk_UA, en_EN, de_DE и т.п.)

        $lang = 'ru_RU';

// Укажите опции
// Список поддерживаемых опций см. ниже
        $opts = array(
            'storage' => PHPMORPHY_STORAGE_FILE,
        );
        $morphy = new phpMorphy($dir, $lang, $opts);
        $array = array();
        if (false === ($paradigms = $morphy->findWord($word))) {
           // die('Can`t find word');
            array_push($array,$word);
            return $array;
        }

// получить только существительные можно при помощи


// обрабатываем омонимы

        foreach ($paradigms as $paradigm) {

            foreach ($paradigm->getAllForms() as $form) {
                if (!in_array(strtolower(mb_strtolower($form, "UTF-8")), $array)) {
                    array_push($array, strtolower(mb_strtolower($form, "UTF-8")));
                }
            }

        }

    } catch (phpMorphy_Exception $e) {
        die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
    }
    return $array;
}


?>