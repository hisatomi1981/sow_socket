<?php

require_once("../../../function/database.php");
try {
  if (!isset($_POST['server'], $_POST['school_id'], $_POST['chat_group'])) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Missing parameters']);
    exit;
  }
  $sqlst = "SELECT *
            FROM clientadata
            LEFT OUTER JOIN serverdata
                    ON clientadata.serial_number_clienta = (
                        CASE 
                            WHEN serverdata.source_id_server ~ '^[0-9]+$' 
                            THEN serverdata.source_id_server::INTEGER
                            ELSE NULL
                        END
                    )
              WHERE serverdata.source_id_server IS NULL
                AND clientadata.server_clienta = :server
                AND clientadata.school_id_clienta = :school_id
                AND clientadata.chat_group_clienta = :chat_group
                AND clientadata.time_clienta >= (NOW() AT TIME ZONE 'Asia/Tokyo') - INTERVAL '1 minute'
            ORDER BY clientadata.serial_number_clienta ASC";

  
  $stmt = $pdo->prepare($sqlst);
  $stmt->bindValue(':server',    $_POST['server'],    PDO::PARAM_STR);
  $stmt->bindValue(':school_id', $_POST['school_id'], PDO::PARAM_STR);
  $stmt->bindValue(':chat_group',$_POST['chat_group'],PDO::PARAM_STR);
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['error' => true, 'message' => $e->getMessage()]);
  exit;
} finally {
  $pdo = null;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($rows);
exit;
