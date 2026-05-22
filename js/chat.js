let attualeLimit = 20;
let autoScroll   = true;
const chatBox    = document.getElementById('chat-box');

chatBox.addEventListener('scroll', function() {
    var isAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 100;
    autoScroll = isAtBottom;
});

function gestisciBottone(response) {
    var totale  = parseInt(response.headers.get('X-Total-Messages') || '0');
    var bottone = document.getElementById('btn-carica-altro');
    bottone.style.display = (attualeLimit >= totale) ? 'none' : 'inline-block';
}

function caricaMessaggi() {
    var idDest = document.getElementById('destinatario_id').value;
    fetch('get_message.php?con=' + idDest + '&limit=' + attualeLimit + '&offset=0')
        .then(function(response) {
            gestisciBottone(response);
            return response.text();
        })
        .then(function(data) {
            var wrapper = document.getElementById('messaggi-wrapper');
            if (wrapper.innerHTML !== data) {
                wrapper.innerHTML = data;
                if (autoScroll) chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
}

function caricaCronologia() {
    var idDest        = document.getElementById('destinatario_id').value;
    attualeLimit     += 20;
    var altezzaPrima  = chatBox.scrollHeight;

    fetch('get_message.php?con=' + idDest + '&limit=' + attualeLimit + '&offset=0')
        .then(function(response) {
            gestisciBottone(response);
            return response.text();
        })
        .then(function(data) {
            document.getElementById('messaggi-wrapper').innerHTML = data;
            setTimeout(function() {
                chatBox.scrollTop = chatBox.scrollHeight - altezzaPrima;
            }, 30);
        });
}

document.getElementById('messaggio_testo').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        inviaMessaggio();
    }
});

var staInviando = false;

function inviaMessaggio() {
    if (staInviando) return;
    var testoInput = document.getElementById('messaggio_testo');
    var testo      = testoInput.value.trim();
    if (!testo) return;

    staInviando = true;

    var formData = new FormData();
    formData.append('idDestinatario', document.getElementById('destinatario_id').value);
    formData.append('testo',          testo);
    formData.append('is_swap',        document.getElementById('is_swap').value);

    fetch('send_message.php', { method: 'POST', body: formData })
        .then(function(response) {
            if (response.ok) {
                testoInput.value = '';
                // Dopo il primo invio non è più un messaggio swap
                document.getElementById('is_swap').value = '0';
                autoScroll = true;
                caricaMessaggi();
            }
        })
        .finally(function() {
            staInviando = false;
        });
}

setInterval(caricaMessaggi, 3000);
caricaMessaggi();
