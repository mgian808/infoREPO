<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['idUtente'])) {
    header('Location: ../include/loginForm.php');
    exit();
}


require_once 'dbHandler.php';
$conn = DBHandler::getPDO();

$idVoto    = intval($_POST['idVoto']       ?? 0);
$materia   = htmlspecialchars(trim($_POST['materia']   ?? ''));
$argomento = htmlspecialchars(trim($_POST['argomento'] ?? ''));
$voto      = floatval($_POST['voto']       ?? 0);
$tipo      = $_POST['tipo']                ?? 'NEUTRO';
$idUtente  = $_SESSION['idUtente'];

if (!$idVoto || empty($materia) || empty($argomento)) {
    header('Location: ../userpages/profile.php?msg=errore');
    exit();
}
if ($voto < 1 || $voto > 10) {
    header('Location: ../userpages/profile.php?msg=errore');
    exit();
}
if (!in_array($tipo, ['OFFRO', 'CERCO', 'NEUTRO'])) {
    $tipo = 'NEUTRO';
}

try {
    $conn->beginTransaction();

    // Verifica che il voto appartenga all'utente loggato
    $check = $conn->prepare(
        "SELECT idVoto FROM voti WHERE idVoto = :id AND idUtente = :uid"
    );
    $check->execute([':id' => $idVoto, ':uid' => $idUtente]);

    if (!$check->fetch()) {
        $conn->rollBack();
        header('Location: ../userpages/profile.php?msg=errore');
        exit();
    }

    $conn->prepare(
        "UPDATE voti
         SET materia = :materia, argomento = :argomento, voto = :voto, tipo = :tipo
         WHERE idVoto = :id AND idUtente = :uid"
    )->execute([
        ':materia'   => $materia,
        ':argomento' => $argomento,
        ':voto'      => $voto,
        ':tipo'      => $tipo,
        ':id'        => $idVoto,
        ':uid'       => $idUtente,
    ]);

    $conn->commit();
    header('Location: ../userpages/profile.php?msg=voto_modificato');
    exit();
} catch (PDOException $e) {
    $conn->rollBack();
    header('Location: ../userpages/profile.php?msg=errore');
    exit();
}
?>