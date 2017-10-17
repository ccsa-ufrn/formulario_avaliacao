<?php
header('Content-Type: application/json');

include_once "connection.php";

$con = new mysqli($host, $user, $pass, $db);
$con->set_charset('utf8');
if(mysqli_connect_errno()){
    echo json_encode(array('success'=>false));
    exit;
}

$content = file_get_contents("php://input");
$content = json_decode($content);

$group_name = $content->group_name;
$coordinator = $content->coordinator;

$queryNewBase = "INSERT INTO `search_group` (`id`, `name`, `coordinator`) VALUES(NULL, '$group_name', '$coordinator');";
$saveGroup = $con->query($queryNewBase);
$base_id = $con->insert_id;
// Save the base infos and store ID
foreach($content->professors as $prof) {
    $prof_name = $prof->name;
    
    $queryNewProf = "INSERT INTO `professor` (`id`, `search_group_id`, `name`) VALUES(NULL, $base_id, '$prof_name');";
    $saveProfessor = $con->query($queryNewProf);
    $prof_id = $con->insert_id;
    
    $current_criterion = 0;
    foreach($prof->criterions as $criterion) {
        foreach($criterion as $type) {
            $type_name = $type->type;
            foreach($type->insertions as $insertion) {
                $queryNewInsertion = "INSERT INTO `insertion`(`id`, `criterion`, `type`, `content`, `professor_id`, `professor_search_group_id`) 
                                      VALUES(NULL, $current_criterion, '$type_name', '$insertion', $prof_id, $base_id);";
                $saveInsertion = $con->query($queryNewInsertion);
            }
        }

        $current_criterion++;
    }
}

echo json_encode(array('success' => true));

$con->close();
?>