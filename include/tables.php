

<?php
require_once 'dbHandler.php';

// RECUPERA LA CONNESSIONE DALLA CLASSE
$conn = DBHandler::getPDO();

if (!$conn) {
    die("Impossibile stabilire una connessione al database.");
}

$statements = [
    'CREATE TABLE IF NOT EXISTS utenti (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        classe VARCHAR(10),
        bio TEXT
    )',
    'CREATE TABLE IF NOT EXISTS competenze (
        id_competenza INT AUTO_INCREMENT PRIMARY KEY,
        id_utente INT,
        materia VARCHAR(50) NOT NULL,
        argomento VARCHAR(100) NOT NULL,
        voto INT NOT NULL,
        tipo ENUM("OFFRO", "CERCO") NOT NULL,
        FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE
    )',
    'CREATE TABLE IF NOT EXISTS stress_log (
        id_stress INT AUTO_INCREMENT PRIMARY KEY,
        id_utente INT,
        livello INT CHECK (livello BETWEEN 1 AND 10),
        data_voto DATE DEFAULT CURRENT_DATE,
        FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE
    )',
    'CREATE TABLE IF NOT EXISTS sondaggi_domande (
        id_domanda INT AUTO_INCREMENT PRIMARY KEY,
        testo_domanda VARCHAR(255) NOT NULL,
        attiva BOOLEAN DEFAULT TRUE
    )',
    'CREATE TABLE IF NOT EXISTS sondaggi_risposte (
        id_risposta INT AUTO_INCREMENT PRIMARY KEY,
        id_domanda INT,
        id_utente INT,
        risposta VARCHAR(100), 
        FOREIGN KEY (id_domanda) REFERENCES sondaggi_domande(id_domanda) ON DELETE CASCADE,
        FOREIGN KEY (id_utente) REFERENCES utenti(id) ON DELETE CASCADE
    )'

];

try {
    foreach ($statements as $sql) {
        $conn->exec($sql);
    }
    echo "Tabelle create correttamente.";
} catch (PDOException $e) {
    echo "Errore durante la creazione: " . $e->getMessage();
}

$conn = null;
?>
