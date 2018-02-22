<?php
session_start();
if(!isset($_SESSION['login'])) {
	header('LOCATION:index.php'); die();
}

include_once "connection.php";

$con = new mysqli($host, $user, $pass, $db);
$con->set_charset('utf8');
if(mysqli_connect_errno()){
    echo json_encode(array('success'=>false));
    exit;
}

$professors_result = mysqli_query($con, "SELECT * FROM professor") or die(mysqli_error($con));
$professors = array();
$number_professors = array();
while($professor = mysqli_fetch_assoc($professors_result)){
    $temp_professor = array();
    $temp_professor = array("id" => $professor["id"], "search_group_id" => $professor["search_group_id"], "name" => $professor["name"], "ccsa" => $professor["ccsa"], "master_phd" => $professor["master_phd"]);
    array_push($professors, $temp_professor);
    array_push($number_professors, $professor['name']);
}
$number_professors = array_change_key_case(array_icount_values($number_professors), CASE_UPPER);
$professor_more_than_two = array();
$professor_two = array();
foreach($number_professors as $key => $number_found){
    if($number_found == 2){
        array_push($professor_two, $key);
    } elseif($number_found > 2){
        array_push($professor_more_than_two, $key);
    } 
}

$insertions_result = mysqli_query($con, "SELECT * FROM insertion") or die(mysqli_error($con));
$insertions = array();
while($insertion = mysqli_fetch_array($insertions_result)){
    $temp_insertion = array();
    $temp_insertion = array("id" => $insertion["id"], "professor_search_group_id" => $insertion["professor_search_group_id"], "criterion" => $insertion["criterion"], "type" => $insertion["type"], "content" => $insertion["content"], "professor_id" => $insertion["professor_id"]);
    array_push($insertions, $temp_insertion);
}

$groups_result = mysqli_query($con, "SELECT * FROM search_group") or die (mysqli_error($con));
$groups = array();
while($group = mysqli_fetch_array($groups_result)){
    $temp_group = array();
    $points_for_rooms = $group['numGrad'] * 4 + $group['numPosGrad'] * 4;
    $points_every_four_years = 0;
    $temp_group = array("id" => $group["id"], "name" => $group['name'], "coordinator" => $group['coordinator'], "year" => $group['year'], "numGrad" => $group['numGrad'], "numPosGrad" => $group['numPosGrad'], "goals" => $group['goals'], "pointsRooms" => $points_for_rooms, "pointsFourYears" => $points_every_four_years, $pointsEachInsertion => Array("3" => 0, "4" => 0, "5" => 0, "6" => 0, "7" => 0, "8" => 0, "9" => 0, "10" => 0));
    foreach($professors as $professor){
        if($professor['search_group_id'] == $temp_group['id'] && $professor['ccsa'] == 1){
            $prof_points_rooms = 0;
            $prof_points_four_years = 0;
            if($professor['master_phd'] == 1){
                $prof_points_rooms += 4;
                $temp_group[$pointsEachInsertion]["3"] += 4;
            }
            foreach($insertions as $insertion){
                if($insertion['professor_id'] == $professor['id']){
                    if($insertion['criterion'] == 0){
                        $prof_points_rooms += 3;
                        $prof_points_four_years += 3;
                        $temp_group[$pointsEachInsertion]["4"] += 3;
                    }
                    if($insertion['criterion'] == 1){
                        $prof_points_rooms += 5;
                        $prof_points_four_years += 5;
                        $temp_group[$pointsEachInsertion]["5"] += 5;
                    }
                    if($insertion['criterion'] == 2){
                        $prof_points_rooms += 4;
                        $prof_points_four_years += 4;
                        $temp_group[$pointsEachInsertion]["6"] += 4;
                    }
                    if($insertion['criterion'] == 3){
                        $prof_points_rooms += 3;
                        $prof_points_four_years += 3;
                        $temp_group[$pointsEachInsertion]["7"] += 3;
                    }
                    if($insertion['criterion'] == 4){
                        $prof_points_rooms += 4;
                        $prof_points_four_years += 4;
                        $temp_group[$pointsEachInsertion]["8"] += 4;
                    }
                    if($insertion['criterion'] == 5){
                        $prof_points_rooms += 5;
                        $prof_points_four_years += 5;
                        $temp_group[$pointsEachInsertion]["9"] += 5;
                    }
                    if($insertion['criterion'] == 6){
                        $prof_points_rooms += 3;
                        $prof_points_four_years += 3;
                        $temp_group[$pointsEachInsertion]["10"] += 3;
                    }
                }
            }
	    unset($insertion);
            $temp_group['pointsRooms'] += $prof_points_rooms;
            $temp_group['pointsFourYears'] += $prof_points_four_years;
        }
    }
    unset($professor);
    array_push($groups, $temp_group);
}
foreach($groups as $group){
    if($group['year'] >= 2017 and $group['year'] <= 2020){
        foreach($groups as $group_2){
            if($group['id'] != $group_2['id'] and strtolower($group['name']) == strtolower($group_2['name'])){
                $group['pointsFourYears'] += $group_2['pointsFourYears'];
            }
        }
    }
}
unset($group);
$groups_this_year = Array();
foreach($groups as $group){
    if($group['year'] == (date("Y") - 1)){
        array_push($groups_this_year, $group);
    }
}
$con->close();
?>
<?php
function array_icount_values($array) {
    $ret_array = array();
    foreach($array as $value) {
        foreach($ret_array as $key2 => $value2) {
            if(strtolower($key2) == strtolower($value)) {
                $ret_array[$key2]++;
                continue 2;
            }
        }
        $ret_array[$value] = 1;
    }
    return $ret_array;
}
?>
<!DOCTYPE html>
<html>
   <head>
     <meta http-equiv='content-type' content='text/html;charset=utf-8' />
     <title>Formula</title>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
   </head>
   <style>
    tr.collapse.in {
        display:table-row; !important
    }
   </style>
<body>
  <nav class="navbar navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../ccsa.png" width="300" class="d-inline-block align-top" alt=""/>
            </a>
        </div>
    </nav>
    <div class="container"> 
        <div style="margin-top: 20px"></div>
        <div class="row">
        <div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading text-center">
				<h4>Dados</h4>
			</div>
			<table class="table">
				<thead>
					<th>Nome</th>
					<th>Coordenador</th>
					<th>Pontos anuais</th>
					<th>Pontos quadrineais</th>
					<th>Ano em questão</th>
				</thead>
				<?php foreach($groups_this_year as $group): ?>
                                <tr data-toggle="collapse" data-target=".<?= $group['id'] ?>">
					<td><?= $group['name'] ?></td>
					<td><?= $group['coordinator'] ?></td>
					<td><?= $group['pointsRooms'] ?></td>
                                        <td><?= $group['pointsFourYears'] ?></td>
                                        <td><?= $group['year'] ?></td>
                                    </tr>
                                    <tr class="collapse <?= $group['id'] ?>">
                                        <td>Critério 1 : <?= $group['numGrad'] * 4 ?></br>
                                        Critério 2 : <?= $group['numPosGrad'] * 4 ?></td>
                                        <td>Critério 3 : <?= $group[$pointsEachInsertion]["3"]?></br>
                                        Critério 4 : <?= $group[$pointsEachInsertion]["4"]?></td>
                                        <td>Critério 5 : <?= $group[$pointsEachInsertion]["5"]?></br>
                                        Critério 6 : <?= $group[$pointsEachInsertion]["6"]?></td>
                                        <td>Critério 7 : <?= $group[$pointsEachInsertion]["7"]?></br>
                                        Critério 8 : <?= $group[$pointsEachInsertion]["8"]?></td>
                                        <td>Critério 9 : <?= $group[$pointsEachInsertion]["9"]?></br>
                                        Critério 10 : <?= $group[$pointsEachInsertion]["10"]?></td>
                                    </tr>
				<?php endforeach; ?>
			</table>
		</div>
            </div>
        </div>
     </div>
  </div>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</body>
</html>
