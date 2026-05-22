
# SkillSwap

## 📚 Panoramica del Progetto

**SkillSwap** è una piattaforma web collaborativa pensata per aiutare gli studenti a supportarsi a vicenda attraverso un sistema intelligente di scambio competenze.

La piattaforma permette agli utenti di:

* offrire aiuto nelle materie in cui sono più preparati
* cercare supporto nelle materie in cui hanno difficoltà
* ricevere match automatici tra studenti compatibili
* comunicare tramite una chat integrata
* monitorare il livello di stress della community
* partecipare a domande giornaliere
* ottenere punti esperienza e salire di livello

L’obiettivo del progetto è trasformare lo studio in un’esperienza più collaborativa, sociale e coinvolgente.

---

# ✨ Funzionalità Principali

## 🔄 Sistema di Matching Intelligente

SkillSwap analizza le materie e i voti inseriti dagli utenti per generare automaticamente abbinamenti compatibili.

### Tipologie di Match

| Tipo              | Descrizione                                                       |
| ----------------- | ----------------------------------------------------------------- |
| **Top Match**     | Scambio reciproco in cui entrambi gli utenti hanno voti alti (8+) |
| **Perfect Match** | Scambio reciproco senza vincoli sui voti                          |
| **Match**         | Compatibilità a senso unico                                       |

### Logica del Matching

Il sistema valuta:

* materie offerte (`OFFRO`)
* materie cercate (`CERCO`)
* voti degli utenti
* scuola frequentata
* classe frequentata

Bonus aggiuntivi:

| Condizione    | Bonus     |
| ------------- | --------- |
| Stessa scuola | +50 punti |
| Stessa classe | +20 punti |

---

## 💬 Sistema di Chat

Gli utenti possono comunicare direttamente tramite una chat integrata nella piattaforma.

Funzionalità principali:

* chat private
* lista conversazioni
* storico messaggi
* aggiornamento messaggi tramite AJAX
* invio asincrono dei messaggi

File principali:

```text
/userpages/chat.php
/userpages/send_message.php
/userpages/get_message.php
/userpages/lista_chat.php
/js/chat.js
```

---

## 📈 Sistema di Gamification

Gli utenti ottengono punti partecipando attivamente alla piattaforma.

### Sistema Punti

| Azione                       | Punti |
| ---------------------------- | ----- |
| Registrazione                | +10   |
| Inserimento voto OFFRO/CERCO | +2    |
| Nuova proposta SkillSwap     | +5    |

### Livelli Utente

| Punti    | Livello  |
| -------- | -------- |
| 0 – 20   | Studente |
| 21 – 50  | Compagno |
| 51 – 100 | Tutor    |
| 100+     | Mentor   |

---

## 😵 Barra dello Stress

La piattaforma include un sistema giornaliero di monitoraggio dello stress.

Gli utenti possono:

* votare il proprio livello di stress (1–10)
* contribuire alla media globale della community
* visualizzare il livello medio di stress nella homepage

La barra cambia dinamicamente colore in base al livello medio registrato.

---

## ❓ Domanda del Giorno

Ogni giorno gli utenti possono rispondere a una domanda della community per aumentare l’interazione tra studenti.

---

## 👤 Gestione Profilo Utente

Ogni profilo contiene:

* informazioni personali
* scuola e classe
* voti inseriti
* materie offerte/richieste
* livello utente
* statistiche
* storico match

---

# 🏗️ Struttura del Progetto

```text
SkillSwap/
│
├── adminpages/
│   └── stats.php
│
├── images/
│   └── logo_bozza.png
│
├── include/
│   ├── addGrade.php
│   ├── addSubject.php
│   ├── config.php
│   ├── dbHandler.php
│   ├── login.php
│   ├── logout.php
│   ├── match.php
│   └── ...
│
├── js/
│   ├── chat.js
│   ├── scuole.js
│   └── scuole.json
│
├── pages/
│   ├── come-funziona.php
│   └── contattaci.php
│
├── userpages/
│   ├── chat.php
│   ├── domanda.php
│   ├── profile.php
│   ├── send_message.php
│   └── ...
│
├── index.php
├── style.css
└── skillswap_db.sql
```

---

# 🛠️ Tecnologie Utilizzate

## Backend

* PHP
* MySQL
* PDO

## Frontend

* HTML5
* CSS3
* JavaScript
* AJAX

## Database

* Database relazionale MySQL
* Stored procedure per la logica di matching

---

# 🗄️ Database

Il progetto include un dump SQL completo:

```text
skillswap_db.sql
```

Il database gestisce:

* utenti
* voti
* materie
* match
* messaggi
* stress giornaliero
* domande giornaliere
* punti e livelli

---

# ⚙️ Installazione

## 1. Clonare il Repository

```bash
git clone https://github.com/tuo-username/skillswap.git
```

---

## 2. Configurare il Database

Creare un database MySQL e importare:

```text
skillswap_db.sql
```

---

## 3. Configurare la Connessione

Modificare:

```text
/include/config.php
```

Esempio:

```php
$host = 'localhost';
$db = 'skillswap';
$user = 'root';
$password = '';
```

---

## 4. Avviare il Server

Esempio con XAMPP:

1. Avviare Apache
2. Avviare MySQL
3. Spostare il progetto dentro:

```text
htdocs/
```

4. Aprire:

```text
http://localhost/SkillSwap
```

---

# 🔐 Sistema di Autenticazione

La piattaforma include:

* registrazione utenti
* login/logout
* gestione sessioni
* modifica password
* protezione pagine riservate

File principali:

```text
/include/login.php
/include/signup.php
/include/logout.php
/include/editPassword.php
```

---

# 🎨 Interfaccia Grafica

L’interfaccia è progettata per essere:

* moderna
* responsive
* intuitiva
* accessibile
* adatta agli studenti

Foglio di stile principale:

```text
style.css
```

---

# 📊 Funzionalità Admin

La sezione amministratore include statistiche e monitoraggio della piattaforma.

```text
/adminpages/stats.php
```

Possibili statistiche:

* utenti attivi
* numero totale di match
* andamento stress community
* coinvolgimento utenti

---


# ❤️ Note Finali

SkillSwap nasce dall’idea che gli studenti imparino meglio collaborando.

Invece di competere, gli utenti possono aiutarsi a vicenda condividendo conoscenze e migliorando insieme.
