<?php  
session_start();   
if (!isset($_SESSION['idUtente'])) {
    header('Location:../include/loginForm.php'); 
    exit();
}
require_once '../include/dbHandler.php'; 
$conn = DBHandler::getPDO();

$myId = $_SESSION['idUtente'];
$receiverId = intval($_GET['con']);

$countSql = "SELECT COUNT(*) FROM messaggi 
             WHERE (idMittente = :myId AND idDestinatario = :receiverId) 
                OR (idMittente = :receiverId AND idDestinatario = :myId)";
$countStmt = $conn->prepare($countSql);
$countStmt->execute([':myId' => $myId, ':receiverId' => $receiverId]);
$totaleMessaggi = $countStmt->fetchColumn();

// si invia il totale al JS con un header
header("X-Total-Messages: $totaleMessaggi");


$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

$sql = "SELECT * FROM (
            SELECT * FROM messaggi 
            WHERE (idMittente = :myId AND idDestinatario = :receiverId) 
               OR (idMittente = :receiverId AND idDestinatario = :myId) 
            ORDER BY data_invio DESC 
            LIMIT :limit OFFSET :offset
        ) AS sub 
        ORDER BY data_invio ASC";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':myId', $myId);
$stmt->bindValue(':receiverId', $receiverId);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$messaggi = $stmt->fetchAll(PDO::FETCH_ASSOC);

$ultimaDataStampata = null;

foreach ($messaggi as $mex) {
    
    $dataCorrente = date('Y-m-d', strtotime($mex['data_invio']));
    $oraCorrente = date('H:i', strtotime($mex['data_invio']));

    if ($dataCorrente !== $ultimaDataStampata) {
        $testoData = formattaData($dataCorrente);
        echo "<div style='text-align: center; margin: 20px 0;'>
                    <span style='background-color: #cfcbcbff;
                    padding: 5px 10px;
                    border-bottom: 1px solid #eee;
                    border-radius: 15px;
                    margin: 10px 0;
                    text-align: center;
                    font-weight: bold;
                    color: #777;'>$testoData</span>
                </div>";
        $ultimaDataStampata = $dataCorrente;
    }
    $classe = ($mex['idMittente'] == $myId) ? 'inviato' : 'ricevuto';
    echo "<div class='messaggio $classe'>";
    echo "<p>" . htmlspecialchars($mex['testo']) . "</p>";
    echo "<small>$oraCorrente</small>"; 
    echo "</div>";
}

function formattaData($data) {
    $oggi = date('Y-m-d');
    $ieri = date('Y-m-d', strtotime('-1 day'));

    if ($data == $oggi) return "Oggi";
    if ($data == $ieri) return "Ieri";

    return date('d/m/Y', strtotime($data));
}
?>