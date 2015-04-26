<?php
set_time_limit (0);
ini_set('memory_limit', '2048M');
error_reporting(-1);
ini_set('display_errors', 'On');
include_once('Proxies.php');
include_once('simple_html_dom.php');
include_once('synonymaser.php');
include_once('WordSplitter.php');
include_once('StopWord.php');
include_once('MoreWordForm.php');
$GLOBALS['proxy'] = new Proxies("0Vuo4IzN7FnECznmQjcv");
function isValidRequest($response){
    return $response!="" && strpos($response,"ya_.json.c(25)({")!=-1;
}
function request($u)
{
    $ch = curl_init();
    while(true) {
        curl_setopt($ch, CURLOPT_PROXY, $GLOBALS['proxy']->get());
        curl_setopt($ch, CURLOPT_URL, $u);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/');
        curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");

        $result = curl_exec($ch);


        if(!($result === false) && isValidRequest($result)){
            return $result;
        }
    }
    return $result;
}
function translate($text,$from,$to){
    $text = urlencode($text);
    $target = "http://translate.yandex.net/api/v1/tr.json/translate?callback=ya_.json.c(25)&lang=$from-$to&text=$text&srv=tr-text&id=0de0f539-8-0&reason=paste&options=4";
    $response = request($target);
    //echo $response;
    preg_match('/\\"text\\".*?:\\[\\"(.*?)\\"\\]/',$response,$matches);

    return $matches[1];
}
function retranslate($text){
    $out = translate($text,"ru","en");

    $out = translate($out,"en","ru");
    return $out;
}

function getSklonenWords($words){
    $sklonen  = array();

    foreach($words as $word) {
        //echo $word."<br>";
        $word1 = strtolower($word);
        $flag = false;
        for ($sklonen_i = 0; $sklonen_i < count($sklonen); $sklonen_i++) {
            if (in_array($word1, $sklonen[$sklonen_i]['sklonen'])) {
                $sklonen[$sklonen_i]['count']++;
                $flag = true;
                break;
            }
        }
        if (!$flag) {

            $sklonen_obj['sklonen'] = getMorph($word1);
            $sklonen_obj['count'] = 1;
            array_push($sklonen,$sklonen_obj);
        }
    }
    return $sklonen;
}
function getFrequencyWords($words){
    $sklonen = getSklonenWords($words);
    $frequencyArray = array();
    foreach($words as $word){
        if(!isset($frequencyArray[$word])){
            $frequencyArray[$word]=1;
        }else{
            $frequencyArray[$word]++;
        }
    }

}

function correctText($text){
    //Get frequency word from text
    $words = splitWord($text);
    $sklonen = getFrequencyWords($words);

    //log
    //foreach($frequencyWords as $word=>$freaq){
    //    echo "$word => $freaq"."<br>";
    //}
    //Синонимы которые мы уже заменяли(что бы не сломать послежющию замену)
    $badWord = array();
    //Гурвица частота которая будет уменьшаться в 2 раза
    $max_freq = 999999;//MAX

    //По всем словам проход/
    foreach($words as $cur_word /*$word=>$freaq*/ ){
        //проверяем нормальное ли слово не (он она их и т.д)
        if(isStopWord($cur_word) ){
            continue;
        }
        //Хорошая частота
        $goodFreaq = $max_freq/2;
        //синнонимы нашего слова
        $synonyms = null;
        $synonyms_counter = 0;
        $cur_freaq = 0;

        $word1 = strtolower(mb_strtolower($cur_word, "UTF-8"));
        for ($sklonen_i = 0; $sklonen_i < count($sklonen); $sklonen_i++) {
            if (in_array($word1, $sklonen[$sklonen_i]['sklonen'])) {
                $cur_freaq = $sklonen[$sklonen_i]['count'];
                break;
            }
        }

        //заменяем плохие слова
        for($i = $cur_freaq;$i>=$goodFreaq;$i--){
            //загружаем синонимы
            if($synonyms==null){
                $synonyms = getSynonyms($word1);
            }
            //проходим по синонимам
            while($synonyms_counter<count($synonyms)){
                //если этот синоним не использовался
                if(!in_array($word1,$badWord)){
                    ////Меняем в тексте
                    $pos = strpos($text,$word1);
                    if ($pos !== false) {
                        $text = substr_replace($text,$synonyms[$synonyms_counter]
                            ,$pos,strlen($word1));
                        array_push($badWord,$word1);
                        echo "$word1 => $synonyms[$synonyms_counter]<br>";
                        $synonyms_counter++;
                        break;
                    }
                    ////Конец замены
                }
                $synonyms_counter++;
            }
        }
        $max_freq/=2;
    }
    return $text;

}

$text = "Ирина не смогла отвести взгляд от картины. Сидя на своем рабочем месте, она считается нарисованных карандашом фигуру обнаженной женщины, стоя на четвереньках на ее шее, украшенной воротником.Художник проявил очень компетентный послушание и похоть обращается рабов: осанка, выражение глаз, полуоткрытый призывно rot- беспорядок, что женщины это нравится, и что она жаждет грубый акт и собирается получить его. Тридцать пять Ирина никогда не думала, что может так быстро вызвали лишь одну цифру, хотя это часто заинтересованы в этих темах и беспокоятся, хотя в настоящее время он находится в этом никогда не признавал, объясняя, что его \"ненормальным\" привлек чисто академический интерес. Но самое замечательное и, следовательно, еще более захватывающим был еще одной особенностью tvoreniya- фигура была изображена сама. Да, может быть, немного идеализировал: грудь немного больше бедра немного шире, но путать женщина окрашены с кем-то было невозможно. во-первых, Ирина очень oskorbilas-, кто осмелился? что это коррупция? Но гнев быстро утих, сменившись непонятным волнением, открывает ворота в темные глубины своей души. И оттуда вылетел ответы, рождение ее второго глубине \"I\". -Что С этим? -slovno задаваемые вопросы разбудил ее подсознание. -Очень Хороший показатель, и очень хорошо прорисованы вас здесь. Да, -myslenno согласился Irina-цифра действительно сделал талантливого руку. Нежные, гладкие, но резкие изгибы может отобразить самую сущность женщины, носить ошейник. И, как услужливо запрос на что-нибудь, который не хотел умолкнуть подсознание, эта сущность была ее собственная. Ирина работала в фирме босс отдела, и она имела свою собственную отдельную комнату. Потому что никто не мог видеть, как рука женщина исчезла под юбку, а длинные пальцы, проходя трусики стал гладить воспаленной влагалище в отчаянной попытке остановить бушует страсть, как ее рот приоткрылся в поисках глоток свежего воздуха задыхается от страсти женщина. \"Кто нарисовал это?\" - Удивился хозяин, пытаясь отвлечь так жарко свои мысли на этапах эротических изображений. Мужчины отметается сразу слишком в отличие от них. Оставайтесь женщина. Но кто? На самом деле, без сомнения voznikalo- было Вик.Же Вика, который недавно пришел к ней в офис и перевернул все с ног на голову. она, кто все смотрел вниз с плохо скрываемым благотворительность.Весь отдел вместе это не любят такого поведения и часто намекал об этом боссу. Но Ира не могла сделать помощь, объясняя, что, мол, она умная работник и хороший, и награда они получили в прошлом месяце, благодаря своей проницательности, и если учесть, что это руководство в хорошем положении, то есть не может быть попрошайничество пошел с чем-то против истины, так как они не говорят, не спорят. Но была и другая причина, которая босс не сказал, когда nikomu- Вика посмотрела на нее презирал глаза, она сразу увлажняется. Ира пытался это как борьбу, но безрезультатно: полный презрения Glance серо-стального цвета глаза, казалось, проникнуть в самые сокровенные тайны ее, видя ее через, попал в самую глубь ее «злой» характер. \"Посему, кроме как с презрением, эти красивые глаза и не должны смотреть на него\", - так считает босс, в результате чего глаза, чтобы оправдать свое подчинение. Откуда взялась идея podomnaya Ирина не знаю, но это было подсознательно убеждены, что это дело, что в свою очередь вызывает босса, чтобы выделить еще больше жира, а затем стыдится своей реакции. Я должен сказать, что стыд только усугубляет волнение к сорту-невероятное, хотя на стенку лезь. Ира разваливается. Длинные ноги и стремимся сделать на колени, спина призывно изгиб и попку похотливо ottopyritsya- все как показано на рисунке. Уже слабой волей женщины почти оставила ее, угрожая оставить тело на растерзание своего вожделения. Перед глазами плавали в висках стучать кувалдой. Находясь в полной дезориентации, она не сразу поняла, что с ней случилось. Кто-то подошел сзади, плотно схватил ее растрепанные черные кудри и потянул вниз резко от того, что Ирина упала на пол. от удивления она даже не успела вымолвить ни слова. Неизвестный опирался на, срывая одежду директрисы. Ира отбивались, как могла, но ее движения были некоторые вялым и совершенно бесполезно. Тело отказался подчиниться своей госпоже, возможно, даже naoborot- это помогло нападавших. Очень скоро женщины, лежащей голой на полу, положив его лицо на полу, боясь перемешать Повсюду лежали обрывки ее одежды. -Вот Теперь вы находитесь в..";
//echo json_encode(getFrequencyWords("женщин танец женщина"));
//echo print_r(getNewFormText("Женщина"));
//echo correctText($text);
//echo mb_strlen("так","UTF-8");
echo correctText($text);
?>