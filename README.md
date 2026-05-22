# infoREPO


```markdown
# SkillSwap

Sito web per lo scambio di aiuto nello studio tra studenti.
L'idea: "io ti insegno quello che so fare bene, tu mi insegni quello che non capisco."

## Funzionalità

- **Matching** tra studenti basato sui voti inseriti (OFFRO / CERCO)
- **Tre livelli di match**: Top Match, Perfect Match, Match normale
- **Chat** integrata tra utenti
- **Stress bar** giornaliera della community
- **Domanda del giorno** con risultati in percentuale
- **Sistema di punti** e livelli (Studente, Compagno, Tutor, Mentor)
- **Pannello admin** con statistiche sulla piattaforma

## Tecnologie

- PHP 8
- MySQL / MariaDB
- HTML, CSS, JavaScript vanilla
- Font Awesome (icone)

## Struttura del progetto

```
www/
├── index.php               Homepage
├── style.css               Foglio di stile
├── include/                File PHP condivisi e di azione
│   ├── config.php          Credenziali database
│   ├── dbHandler.php       Connessione PDO (Singleton)
│   ├── menuChoice.php      Framework: gestione accessi via pages.json
│   ├── navbar.php          Navbar condivisa
│   ├── pages.json          Classificazione pagine per il framework
│   ├── login.php           Autenticazione
│   ├── logout.php          Disconnessione
│   ├── signup.php          Registrazione
│   ├── addGrade.php        Inserimento voto
│   ├── addSubject.php      Inserimento materia
│   ├── deleteGrade.php     Eliminazione voto
│   ├── updateGrade.php     Modifica voto
│   ├── updatePassword.php  Modifica password
│   ├── salva_stress.php    Voto stress giornaliero
│   └── match.php           Pagina matching
├── userpages/              Pagine per utenti loggati
│   ├── profile.php         Profilo utente
│   ├── lista_chat.php      Lista conversazioni
│   ├── chat.php            Chat singola
│   ├── get_message.php     API caricamento messaggi
│   ├── send_message.php    API invio messaggi
│   ├── domanda.php         Domanda del giorno
│   └── salva_risposta.php  Salvataggio risposta
├── adminpages/             Pagine riservate agli admin
│   └── stats.php           Statistiche piattaforma
├── pages/                  Pagine informative pubbliche
│   ├── come-funziona.php   Spiegazione del sito
│   └── contattaci.php      Contatti
└── js/
    ├── chat.js             Logica JavaScript della chat
    └── scuole.js           Caricamento dinamico comuni e scuole
```

## Setup

### 1. Database

Importa il file SQL in MySQL:

```bash
mysql -u root -p < skillswap.sql
```

Poi esegui la stored procedure aggiornata:

```bash
mysql -u root -p skillswapprova < fix_sp.sql
```

### 2. Configurazione

Modifica `include/config.php` con le tue credenziali:

```php
$host     = 'localhost';
$db       = 'skillswapprova';
$user     = 'root';
$password = '';
```

### 3. Server

Richiede Apache con PHP 8+ e mod_rewrite attivo.
Per sviluppo locale puoi usare XAMPP, WAMP o Docker.

## Account di default

Dopo aver importato il DB, imposta il tuo account come admin:

```sql
UPDATE utenti SET ruolo = 'admin' WHERE email = 'tua@email.com';
```

## Autore

Mattia Giancristofaro — Progetto finale di Informatica 2024/2025
```