
<html>
<head>

</head>
<body>

<table border="5px">
    <thead >
    <tr>
                <td>Слово</td>

           <td>Часть речи</td>

           <td>Род</td>

           <td>Число</td><

            <td>Падеж</td>

   <td>Одушевленность</td><td></td></tr>
    </thead>
    <tbody>




    <?php
    /**
     * Created by PhpStorm.
     * User: Alexeev
     * Date: 28-Apr-15
     * Time: 04:59 PM
     */



    include_once 'db.php';
    $page = 1;
    if(isset($_GET['page'])){
        $page = $_GET['page'];
    }
    function createSelectFromProp($prop){
        $arr = getPropertie($prop);
        $s = "<td><select class='".$prop."'>";
        foreach($arr as $pr){
            $s.="<option value=".$pr['id'].">".$pr['prop']."</option>";
        }

        $s.="</select></td>";
        return $s;
    }

    $p1= createSelectFromProp('p_speech');
    $p2 = createSelectFromProp('kind');
    $p3 = createSelectFromProp('number');
    $p4=createSelectFromProp('case');
    $p5=createSelectFromProp('naturable');
    if(!isset($_GET['word'])) {
        $ar = getBadWords($page);
    }else{
        $ar = getWord($_GET['word']);
    }


    foreach($ar as $a){
        echo "<tr id='tr_".$a['idword']."'>";
        echo "<td class='w'>".$a['text']."</td>".$p1.$p2.$p3.$p4.$p5."<td><button onclick=\"update('".$a['idword']."')\">UPDATE</button></td>";
        echo "</tr>";

    }


    ?>
    </tbody>
</table>
<script>
    function update(id)
    {
        var data = {
            id:id,
            p_speech:$("#tr_"+id+" .p_speech").val(),
            kind:$("#tr_"+id+" .kind").val(),
            number:$("#tr_"+id+" .number").val(),
            case:$("#tr_"+id+" .case").val(),
            naturable:$("#tr_"+id+" .naturable").val()};
        console.log(data);
       $.get('./update.php',data,
                function(data){

                    alert(data);
                    $("#tr_"+id).hide();
                });



    }


</script>
<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
</body>
</html>

