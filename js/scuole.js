let datiScuoleCache = []; 

// Funzione di utilità per decidere se una scuola è "Superiore"
// La usiamo in entrambe le funzioni per essere coerenti al 100%
function isScuolaSuperiore(scuola) {
    const desc = (scuola["miur:DESCRIZIONETIPOLOGIAGRADOISTRUZIONESCUOLA"] || "").toUpperCase();
    const nome = (scuola["miur:DENOMINAZIONESCUOLA"] || "").toUpperCase();

    const paroleChiave = [
        "SUPERIORE", "II GRADO", "ISTITUTO TECNICO", "ISTITUTO PROFESSIONALE", 
        "LICEO", "MAGISTRALE", "ARTISTICO", "CLASSICO", "SCIENTIFICO", "LINGUISTICO",
        "TECNICO", "PROFESSIONALE", "I.I.S.", "I.S.", "I.P.S.", "I.T.I.", "I.T.C.","IST PROF PER I SERVIZI ALBERGHIERI E RISTORAZIONE"
    ];

    const isSuperiore = paroleChiave.some(parola => desc.includes(parola) || nome.includes(parola));
    
    const isDaEscludere = desc.includes("PRIMARIA") || desc.includes("I GRADO") || desc.includes("INFANZIA");

    return isSuperiore && !isDaEscludere;
}

export async function caricaComuni() {
    const provinceSelect = document.getElementById('province');
    const comuneSelect = document.getElementById('comune');
    const provinciaScelta = provinceSelect.value;

    comuneSelect.innerHTML = '<option>Caricamento comuni...</option>';
    comuneSelect.disabled = true;

    try {
        if (datiScuoleCache.length === 0) {
            const response = await fetch('../js/scuole.json');
            const data = await response.json();
            datiScuoleCache = data["@graph"];
        }

        // Filtriamo: Stessa provincia E deve essere superiore
        const scuoleFiltrate = datiScuoleCache.filter(scuola => 
            scuola["miur:PROVINCIA"] === provinciaScelta && isScuolaSuperiore(scuola)
        );

        const comuniUnici = [...new Set(scuoleFiltrate.map(s => s["miur:DESCRIZIONECOMUNE"]))].sort();

        comuneSelect.innerHTML = '<option value="" selected disabled>Seleziona comune...</option>';
        if (comuniUnici.length > 0) {
            comuniUnici.forEach(comune => {
                const option = document.createElement('option');
                option.value = comune;
                option.textContent = comune;
                comuneSelect.appendChild(option);
            });
            comuneSelect.disabled = false;
        } else {
            comuneSelect.innerHTML = '<option>Nessun comune trovato</option>';
        }
    } catch (error) {
        console.error("Errore comuni:", error);
    }
}

export async function caricaScuolePerComune() {
    const provinceSelect = document.getElementById('province');
    const comuneSelect = document.getElementById('comune');
    const scuoleSelect = document.getElementById('listaScuole');
    
    const provinciaScelta = provinceSelect.value;
    const comuneScelto = comuneSelect.value;

    scuoleSelect.innerHTML = '<option>Caricamento scuole...</option>';
    scuoleSelect.disabled = true;

    
    const scuoleFinali = datiScuoleCache.filter(scuola => 
        scuola["miur:PROVINCIA"] === provinciaScelta && 
        scuola["miur:DESCRIZIONECOMUNE"] === comuneScelto &&
        isScuolaSuperiore(scuola)
    );

    scuoleSelect.innerHTML = '<option value="" selected disabled>Seleziona la scuola...</option>';

    if (scuoleFinali.length > 0) {
        scuoleFinali.forEach(scuola => {
            const option = document.createElement('option');
            option.value = scuola["miur:DENOMINAZIONESCUOLA"] + " (" + scuola["miur:CODICESCUOLA"] + ")";
            option.textContent = scuola["miur:DENOMINAZIONESCUOLA"] + " (" + scuola["miur:CODICESCUOLA"] + ")";
            scuoleSelect.appendChild(option);
        });
        scuoleSelect.disabled = false;
    } else {
        scuoleSelect.innerHTML = '<option>Nessuna scuola trovata</option>';
    }
}