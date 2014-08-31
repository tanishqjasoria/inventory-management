<?php

$app->post('/add/submit', function() use ($app) {
    // Set Database credentials
    if(!isset($path)){ $path = $_SERVER['DOCUMENT_ROOT'].'/php/'; }
    require $path . 'db-conn.php';

    var_dump($data);
    
    $data = json_decode($_POST['data']);
    $part = $data->parts[0];
    //add code here that check to ensure only 1 part was sent / use a for each structure

    if ($stmt = $CONN->prepare("SELECT COUNT(*) FROM `parts` WHERE part_num=?")) {
        $stmt->bind_param("s", $part->part_num);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } else {
        echo "Prepare failed: (" . $CONN->errno . ") " . $CONN->error . "<br>";
        $app->response->setStatus(500);
        return;
    }

    if ($count == 0){
        echo "Insert New Row\n";
        
        if ($stmt = $CONN->prepare("INSERT INTO parts (part_num, part_name, category, description, datasheet, location)
            Values (?,?,?,?,?,?)")){
            $stmt->bind_param("s", $part->part_num);
            $stmt->bind_param("s", $part->part_name);
            $stmt->bind_param("s", $part->category);
            $stmt->bind_param("s", $part->description);
            $stmt->bind_param("s", $part->datasheet);
            $stmt->bind_param("s", $part->location);
            $stmt->execute();
            $stmt->fetch();
            $stmt->close();
        } else {
            echo "Prepare failed: (" . $CONN->errno . ") " . $CONN->error . "<br>";
            $app->response->setStatus(500);
            return;
        }
        
    } else {
        //Error out if part already exists
        printf("Error: Part %s already exists:", $partNum);
        $app->response->setStatus(409);
        return;
    }  
    
});