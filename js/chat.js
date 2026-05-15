let attualeLimit = 20; 
let autoScroll = true; 
const chatBox = document.getElementById('chat-box');


chatBox.addEventListener('scroll', () => {
    const isAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 100;
    autoScroll = isAtBottom;
});



function gestisciVisibilitaBottone(response) {
    const totaleNelDb = parseInt(response.headers.get('X-Total-Messages'));
    const bottone = document.getElementById('btn-carica-altro');
    if (attualeLimit >= totaleNelDb) {
        bottone.style.display = 'none';
    } else {
        bottone.style.display = 'inline-block';
    }
}

function caricaMessaggi() {
    let idDestinatario = document.getElementById('destinatario_id').value;
    
    fetch(`get_message.php?con=${idDestinatario}&limit=${attualeLimit}&offset=0`)
    .then(response => {
        gestisciVisibilitaBottone(response);
        return response.text();
    })
    .then(data => {
        let wrapper = document.getElementById('messaggi-wrapper');
        if (wrapper.innerHTML !== data) {
            wrapper.innerHTML = data;
            if (autoScroll) chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
}

function caricaCronologia() {
    let idDestinatario = document.getElementById('destinatario_id').value;
    attualeLimit += 20; 
    const altezzaPrima = chatBox.scrollHeight;

    fetch(`get_message.php?con=${idDestinatario}&limit=${attualeLimit}&offset=0`)
    .then(response => {
        gestisciVisibilitaBottone(response);
        return response.text();
    })
    .then(data => {
        let wrapper = document.getElementById('messaggi-wrapper');
        wrapper.innerHTML = data;
        
        setTimeout(() => {
            chatBox.scrollTop = chatBox.scrollHeight - altezzaPrima;
        }, 30);
    });
}


window.addEventListener('offline', () => mostraErroreConnessione("Connessione internet assente."));
window.addEventListener('online', () => nascondiErroreConnessione());
/**
 * @param {string} messaggio
 * @param {number} secondi
 */
function mostraErroreConnessione(messaggio, secondi = 0) {
    let alertBox = document.getElementById('connessione-alert');

    if (!alertBox) {
        alertBox = document.createElement('div');
        alertBox.id = 'connessione-alert';
        alertBox.style = `
            position: fixed; top: 0; left: 0; width: 100%; 
            background: #ff4d4d; color: white; padding: 15px; 
            text-align: center; z-index: 9999; font-weight: bold;
            transition: all 0.3s ease; box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        `;
        document.body.prepend(alertBox);
    }

    alertBox.innerText = messaggio;
    alertBox.style.display = 'block';
    alertBox.style.opacity = '1';
    alertBox.style.transform = 'translateY(0)';

    if (secondi > 0) {
        setTimeout(() => {
            alertBox.style.opacity = '0';
            alertBox.style.transform = 'translateY(-100%)';
            setTimeout(() => { alertBox.style.display = 'none'; }, 300);
        }, secondi * 1000);
    }
}


document.getElementById('messaggio_testo').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault(); 
        inviaMessaggio();
    }
});


let staInviando = false; 

function inviaMessaggio() {
    if (staInviando) return;

    let testoInput = document.getElementById('messaggio_testo');
    let testo = testoInput.value.trim();
    if (testo === "") return;

    staInviando = true;

    let formData = new FormData();
    formData.append('idDestinatario', document.getElementById('destinatario_id').value);
    formData.append('testo', testo);

    fetch('send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        if (!response.ok) {
            const errorData = await response.json();
            mostraErroreConnessione(errorData.message, 5);
            return;
        }
        testoInput.value = ''; 
        autoScroll = true; 
        caricaMessaggi(); 
    })
    .catch(error => {
        mostraErroreConnessione("Impossibile inviare: controlla la tua connessione.");
    })
    .finally(() => {
        staInviando = false;
    });
}


setInterval(caricaMessaggi, 3000);
caricaMessaggi();