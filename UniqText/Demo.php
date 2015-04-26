<?php
set_time_limit (0);

ini_set('memory_limit', '2048M');
error_reporting(-1);
ini_set('display_errors', 'On');
include_once('Proxies.php');
$GLOBALS['proxy'] = new Proxies("0Vuo4IzN7FnECznmQjcv");
include_once('simple_html_dom.php');
include_once('synonymaser.php');
include_once('WordSplitter.php');
include_once('StopWord.php');
include_once('MoreWordForm.php');

set_error_handler('exceptions_error_handler');
function exceptions_error_handler($severity, $message, $filename, $lineno) {
    if (error_reporting() == 0) {
        return;
    }
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $filename, $lineno);
    }
}


function isValidRequest($response){
    return $response!="" && strpos($response,"ya_.json.c(25)({\"align\":")!==false;
}
function log_tome($title,$text){
    $text = html_entity_decode(preg_replace("/U\+([0-9A-F]{4})/", "&#x\\1;",$text), ENT_NOQUOTES, 'UTF-8');
    //$text = str_replace("\\","\\\",$text);
    $query = "Insert into logger_refactortext VALUES (DEFAULT ,now(),'$text','$title')";
    mysql_query($query);
}
function getRandomUserAgent()
{
    $userAgents=array(
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
        "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
        "Opera/9.20 (Windows NT 6.0; U; en)",
        "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50",
        "Mozilla/4.0 (compatible; MSIE 6.0; MSIE 5.5; Windows NT 5.1) Opera 7.02 [en]",
        "Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; fr; rv:1.7) Gecko/20040624 Firefox/0.9",
        "Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/48 (like Gecko) Safari/48"
    );
    $random = rand(0,count($userAgents)-1);

    return $userAgents[$random];
}
function request($u)
{
    $i=0;
    $ch = curl_init();
    while(true) {

        log_tome("CUR proxy",$GLOBALS['proxy']->get());
        curl_setopt($ch, CURLOPT_PROXY, $GLOBALS['proxy']->get());
        curl_setopt($ch, CURLOPT_URL, $u);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/');
        curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");

        $result = curl_exec($ch);

        log_tome("CUR REQUEST",$result);
        if(!($result === false) && isValidRequest($result)){
            return $result;
        }
        $GLOBALS['proxy']->next();
        log_tome("BAD REQUEST",$result);
    }
    return $result;
}


function T_translate($text,$from,$to){

    $text = urlencode($text);
    $target = "http://translate.yandex.net/api/v1/tr.json/translate?callback=ya_.json.c(25)&lang=$from-$to&text=$text&srv=tr-text&id=0de0f539-8-0&reason=paste&options=4";

    $response = request($target);

    //echo $response;
    preg_match('/\\"text\\".*?:\\[\\"(.*?)\\"\\]/',$response,$matches);

    return $matches[1];
}
function translate($text,$from,$to){
    $counter = 0;
    $out_text = "";
    log_tome("START TRANSLATE $from => $to",$text);
    while($counter<999999) {
        $to_translate = "";
        $i = 0;
        try {
            for (; $i < 5000; $i++) {
                if ($i > 4500 && $text[$counter + $i] == '.') {
                    $to_translate .= $text[$counter + $i];
                    break;

                }
                $a = $text[$counter + $i];
                $to_translate .= $a;
            }
        } catch (Exception $e) {
            $out_text .= T_translate($to_translate, $from, $to);
            return $out_text;
        }
        //echo $to_translate;
        $counter += $i;
        $out_text .= T_translate($to_translate, $from, $to);

    }
    return $out_text;
}
function retranslate($text){
    $to = "de";
    $out = translate($text,"ru","en");
    //$out = translate($out,"de","fr");
    $out = translate($out,"en","ru");
    return $out;
}

function getSklonenWords($words){
    $sklonen  = array();

    foreach($words as $word) {
        //echo $word."<br>";
        $word1 = mb_strtolower($word,"UTF-8");
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
function cmp($a, $b)
{
    return $b["count"] - $a["count"];
}
function getFrequencyWords($words){

    $sklonen = getSklonenWords($words);

    $frequencyArray = array();
    foreach($words as $word){
        if(isStopWord($word)){
            continue;
        }
        $word1 = mb_strtolower($word,"UTF-8");
        for ($sklonen_i = 0; $sklonen_i < count($sklonen); $sklonen_i++) {
            if (in_array($word1, $sklonen[$sklonen_i]['sklonen'])) {
                if(!isset($frequencyArray["$sklonen_i"]['words'])){
                    $frequencyArray["$sklonen_i"]['words'] = array();
                }

                $frequencyArray["$sklonen_i"]['count']
                    = $sklonen[$sklonen_i]['count'];
                array_push($frequencyArray["$sklonen_i"]['words'],$word);
                break;
            }
        }
    }

    usort($frequencyArray,"cmp");
    return $frequencyArray;

}

function correctText($text)
{
    log_tome("START CORRECTING",$text);
    //Get frequency word from text
    $words = splitWord($text);

    $frequencyWords = getFrequencyWords($words);

    log_tome("FREAQ",count($frequencyWords));
    //log
    //foreach($frequencyWords as $word=>$freaq){
    //    echo "$word => $freaq"."<br>";
    //}
    //Синонимы которые мы уже заменяли(что бы не сломать послежющию замену)
    $badWord = array();
    //Гурвица частота которая будет уменьшаться в 2 раза
    $max_freq = $frequencyWords[0]['count'];//MAX
    //По всем словам проход
    $max_replacement = 200000;
    $count_replacement = 0;
    foreach ($frequencyWords as $freq) {
        if($freq['count']==1){
            break;
        }
        //   log_tome("WORD GROUP TO REPLACE",$freq['words'][0]);
        if($count_replacement++>$max_replacement){
            break;
        }
        if($max_freq==0)$max_freq=1;
        //синнонимы нашего слова
        $synonyms = null;
        $synonyms_counter = 0;
        $cur_word_counter = 0;

        $synonyms_counter = 0;
        $synonyms =array();
        foreach($freq['words'] as $word_need_synonym){
            //log_tome("FIND SYNONYM",$word_need_synonym);
            try{
                $this_syn = getSynonyms($word_need_synonym);

                foreach($this_syn as $cur_syn){
                    array_push($synonyms,$cur_syn);
                }
            }catch(Exception $e){}

        }
        //заменяем плохие слова
        for ($i = intval($freq['count']); $i > $max_freq
        && $cur_word_counter < count($freq['words']); $i--) {
            //    log_tome("GO TO BLOCK WORD = ",$freq['words'][$cur_word_counter]);
            $cur_word = $freq['words'][$cur_word_counter++];
            //загружаем синонимы
            // if ($synonyms == null) {

            // }
            //log_tome("CUR FREQ",$max_freq);
            //проходим по синонимам

            while ($synonyms_counter < count($synonyms)) {
                //$badWord=array();
                //если этот синоним не использовался
                if (!isset($badWord[$synonyms[$synonyms_counter]])) {
                    // log_tome("TRY TO CHANGE",$cur_word);

                    //$pos = mb_strpos($text,  $cur_word,0,"UTF-8");
                    $pos = strpos($text,  $cur_word);
                    if ($pos !== false) {
                        $text = substr_replace($text, $synonyms[$synonyms_counter]
                            , $pos, strlen( $cur_word));
                        //array_push($badWord,  $synonyms[$synonyms_counter]);
                        //array_push($badWord,  $cur_word);
                        $badWord[$synonyms[$synonyms_counter]] = true;
                        $badWord[$cur_word] = true;
                        //   log_tome("CHANGE WORDS","$cur_word => $synonyms[$synonyms_counter]");
                        //echo "$cur_word => $synonyms[$synonyms_counter]<br>";
                        $synonyms_counter++;
                        break;
                    }
                }
                $synonyms_counter++;
            }
        }
        $max_freq = intval($max_freq/2);
    }
    return $text;

}

/*
$syn = getSynonyms("мои");
foreach($syn as $s){
	echo $s."<br>";
}
die();*/
$text = "Ирина не смогла отвести взгляд от картины. Сидя на своем рабочем месте, она считается нарисованных карандашом фигуру обнаженной женщины, стоя на четвереньках на ее шее, украшенной воротником.Художник проявил очень компетентный послушание и похоть обращается рабов: осанка, выражение глаз, полуоткрытый призывно rot- беспорядок, что женщины это нравится, и что она жаждет грубый акт и собирается получить его. Тридцать пять Ирина никогда не думала, что может так быстро вызвали лишь одну цифру, хотя это часто заинтересованы в этих темах и беспокоятся, хотя в настоящее время он находится в этом никогда не признавал, объясняя, что его \"ненормальным\" привлек чисто академический интерес. Но самое замечательное и, следовательно, еще более захватывающим был еще одной особенностью tvoreniya- фигура была изображена сама. Да, может быть, немного идеализировал: грудь немного больше бедра немного шире, но путать женщина окрашены с кем-то было невозможно. во-первых, Ирина очень oskorbilas-, кто осмелился? что это коррупция? Но гнев быстро утих, сменившись непонятным волнением, открывает ворота в темные глубины своей души. И оттуда вылетел ответы, рождение ее второго глубине \"I\". -Что С этим? -slovno задаваемые вопросы разбудил ее подсознание. -Очень Хороший показатель, и очень хорошо прорисованы вас здесь. Да, -myslenno согласился Irina-цифра действительно сделал талантливого руку. Нежные, гладкие, но резкие изгибы может отобразить самую сущность женщины, носить ошейник. И, как услужливо запрос на что-нибудь, который не хотел умолкнуть подсознание, эта сущность была ее собственная. Ирина работала в фирме босс отдела, и она имела свою собственную отдельную комнату. Потому что никто не мог видеть, как рука женщина исчезла под юбку, а длинные пальцы, проходя трусики стал гладить воспаленной влагалище в отчаянной попытке остановить бушует страсть, как ее рот приоткрылся в поисках глоток свежего воздуха задыхается от страсти женщина. \"Кто нарисовал это?\" - Удивился хозяин, пытаясь отвлечь так жарко свои мысли на этапах эротических изображений. Мужчины отметается сразу слишком в отличие от них. Оставайтесь женщина. Но кто? На самом деле, без сомнения voznikalo- было Вик.Же Вика, который недавно пришел к ней в офис и перевернул все с ног на голову. она, кто все смотрел вниз с плохо скрываемым благотворительность.Весь отдел вместе это не любят такого поведения и часто намекал об этом боссу. Но Ира не могла сделать помощь, объясняя, что, мол, она умная работник и хороший, и награда они получили в прошлом месяце, благодаря своей проницательности, и если учесть, что это руководство в хорошем положении, то есть не может быть попрошайничество пошел с чем-то против истины, так как они не говорят, не спорят. Но была и другая причина, которая босс не сказал, когда nikomu- Вика посмотрела на нее презирал глаза, она сразу увлажняется. Ира пытался это как борьбу, но безрезультатно: полный презрения Glance серо-стального цвета глаза, казалось, проникнуть в самые сокровенные тайны ее, видя ее через, попал в самую глубь ее «злой» характер. \"Посему, кроме как с презрением, эти красивые глаза и не должны смотреть на него\", - так считает босс, в результате чего глаза, чтобы оправдать свое подчинение. Откуда взялась идея podomnaya Ирина не знаю, но это было подсознательно убеждены, что это дело, что в свою очередь вызывает босса, чтобы выделить еще больше жира, а затем стыдится своей реакции. Я должен сказать, что стыд только усугубляет волнение к сорту-невероятное, хотя на стенку лезь. Ира разваливается. Длинные ноги и стремимся сделать на колени, спина призывно изгиб и попку похотливо ottopyritsya- все как показано на рисунке. Уже слабой волей женщины почти оставила ее, угрожая оставить тело на растерзание своего вожделения. Перед глазами плавали в висках стучать кувалдой. Находясь в полной дезориентации, она не сразу поняла, что с ней случилось. Кто-то подошел сзади, плотно схватил ее растрепанные черные кудри и потянул вниз резко от того, что Ирина упала на пол. от удивления она даже не успела вымолвить ни слова. Неизвестный опирался на, срывая одежду директрисы. Ира отбивались, как могла, но ее движения были некоторые вялым и совершенно бесполезно. Тело отказался подчиниться своей госпоже, возможно, даже naoborot- это помогло нападавших. Очень скоро женщины, лежащей голой на полу, положив его лицо на полу, боясь перемешать Повсюду лежали обрывки ее одежды. -Вот Теперь вы находитесь в..";
//echo json_encode(getFrequencyWords("женщин танец женщина"));
//echo print_r(getNewFormText("Женщина"));
//echo correctText($text);
//echo print_r(getMorph("получить"));

//echo mb_strlen("так","UTF-8");
//$text = "Женщина глаза"
//echo json_encode(getFrequencyWords(splitWord($text)));
//echo json_encode(getFrequencyWords(splitWord("Ирина не смогла отвести взгляд от картины.Тридцать пять Ирина никог")));
//var_dump(getMorph("Ира"));


///http://seogenerator.ru/api/synonym/?text=%u043B%u0438%u043B&method_replace=first&base_used=big1&highlight=1&format=json
$text="Ирина не могла отвести взгляд от картины. Сидя на рабочем месте, считается, нарисованные карандашом фигуру обнаженной женщины, стоящей на четвереньках, ее шею украшал ошейник. Художник-это очень хорошо отражено послушание и похоть обращается рабов: поза, выражение, глаза, полуоткрытый рот призывно есть свидетельства того, что женщина нравится, и то, что она она жаждет грубого полового акта, и теперь она будет получать. Tridtsatipjatiletnego Ирина никогда не думала, что он может так быстро вызвали только от одной картинки, хотя он часто удивлялся и беспокоился о подобных темах, хотя она никогда не признал, пояснив, что его \"crazy\" аттракцион чисто академический интерес. Но самое замечательное и поэтому еще более захватывающей была еще одна черта творчества - на рисунке была изображена сама. Да, может быть, немного idealizirovan: грудь побольше, бедра чуть шире, но путать окрашенные женщина с кем-то другим было невозможно.
Сначала Ирина очень обидел, - кто осмелится? Что это за мерзость? Но гнев быстро стих последовало странное волнение, открывая ворота в темных глубинах ее души. И тогда пришел ответ, родился ее второй глубина \"я\".
- В чем проблема? как если бы я спросил ее, спит подсознание. Очень красивая картинка, и очень правильно вы здесь нарисовали.
\"Да, - мысленно согласилась Ирина - картина действительно талантливой рукой. Мягкие, плавные, а резкие изгибы смогли показать саму суть женщины в воротник. И как услужливо сказал никогда не хотел молчать подсознание, это было ее собственное лицо.
Ирина работала в компании, начальник одного из отделов, и у нее был собственный личный кабинет. Потому что никто не мог увидеть, как рука женщина исчезла под юбкой, как длинные пальцы мимо нее трусики и стал гладить воспаление влагалища в безнадежной попытке остановить волну похоти, как ее рот открылся в поисках глоток свежего воздуха для asphyxiated от страсти женщины. \"Кто это нарисовал? \"- спросил босс, стараясь жарко, чтобы отвлечь свои мысли от каскадов эротических картинок. Мужчины были уволены сразу - слишком разные. Останутся женщины. Но кто?";
//echo correctText($text);
function getId(){
    $q = "select s.value from settings s where s.kkey like 'parse_storyTable_lastid' ";
    $result = mysql_query($q);

    $array = mysql_fetch_array($result);
    if (count($array) == 0) {
        return null;
    }
    return $array[0];
}
function updateId($id)
{
    $q = "update settings s set s.value=$id where s.kkey like 'parse_storyTable_lastid'";
    $result = mysql_query($q);
    return $result;
}
function saveText($title,$text)
{
    $q = "insert into story_corrected values(default,'$title','$text')";
    mysql_query($q);
}
$t = 'Я не знаю, как это назвать любые слова не отражают реальности - безумного желания отдать всю свою нежность, чтобы Вы не входит в привычный набор слов. Я хотел ответить на Ваш вопрос. Ты спросил его, смеясь и глядя большими глазами. Ты видел меня голой? Вопрос заставил меня смеяться. Я видел все, что можно. Я видел, как ты с ее юбка, apart от нежности ноги, серотип и упал truescale. Я знаю, что вы вкус, когда вы закроете глаза и бросить его голову руками. Я знаю, какие слова вы начинаете кричать (или шепотом) удовольствие от меня. Знаю, wince ягодиц и бедер от прикосновения моих губ. Знаете, ваши смешные путаницы с влажными звуками лаская друг друга телефон я успел увидеть твое лицо, касаясь рта, груди и живота. Знаю прикосновение соска к пальцам и ладони я знаю, на ощупь и на вес я вскинутой на плечи ноги. Знаю твою улыбку, когда ты расстегнуть молнию на моих брюках и начала ласкать обеими руками. Знаете, ваши глаза и посмотрите, когда вы начинаете раздеваться для меня. Знаете, ваше лицо, смущение и радость, когда мои руки попасть внутрь вашего truesec и ты не удержался (совсем наоборот) они движутся все дальше и глубже. Знаете, ваше лицо, когда вы, что-то шепча, хорошо разводят ножки, подставляя под ласки и глаз ничего. Знаете, ваше лицо в откровенной, прямой примитивные удовольствия. Я видел, как ты рубашку с моего плеча и нижнее белье. И видел, как ты откровенно голые и открыл глаза, руки, ноги по отношению ко мне. Помните, когда вы открыли в первый раз? Я попросил тебя, и ты просто потянув за лист. Наконец он воздел руки к небу, слегка раздвинутых ног и закрыл глаза. Я, улыбаясь, глядя на вас. Похоже, вы физически чувствовал, лаская глаза и, наконец, смеясь: прозрачная, потянул меня к нему. Схожу с ума ваши прикосновения. Расставив ноги для вас - руки, глаза, губы. Вы знаете, что лечить, чтобы поднять голову, чтобы направлять себя, чтобы Вы, чтобы увидеть постепенное проникновение и ваше лицо. Некоторое время я даже смотреть на то, что вы делаете. Наслаждайтесь каждым ритмичным движением поглотила мои губы. И бить в горло. Тогда удовольствие становится неконтролируемым. Вы skydives торжествующей, испачканное лицо, я улыбаюсь вам встретиться. Вы не видите улыбки - не могу открыть глаза. Обнять меня и опустил голову где-то в нижней части живота. Я покрываю вас с руками и не хотела отпускать. Отшатнувшись от меня и матери. Вы знаете, что я чувствую с тобой, кажется, не было имени.. это сильнее, чем страсть. Сильнее, чем то, что называется любовью. Письмо оказалось довольно чувственный, но я хотел, чтобы вы знали об этом. Я думаю, я ответил на вопрос?';

//$text = $line['text'];
// $t = retranslate($text);


while(true) {

$id = getId();
$query = "select  *  from story where id>=$id limit 1";

$result = mysql_query($query);

log_tome("BEGIN id ", $id);



if($line = mysql_fetch_array($result, MYSQL_ASSOC)) {


try{
$text = $line['text'];
$t = retranslate($text);

$text = correctText($t);

//$text = str_replace("\\","",$text);
$text = str_replace("'","",$text);
log_tome('try to save',$text);
saveText($line['title'],$text);
}catch(Exception $e){
    log_tome('erro!',$e);
}
    die();
    return;
updateId($line['id']+1);
}else{
log_tome("END PARSING!","END");
die();
}

}




?>