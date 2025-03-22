const CLIENT_ID = '630487166496-6fdut87qhs5ktv1jpqvac9n5384t0kq6.apps.googleusercontent.com';
const API_KEY = 'AIzaSyA9QaESX27kHlI235NiEfAmTdT-QNbNlMM';
const DISCOVERY_DOC = ['https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest'];
const SCOPES = "https://www.googleapis.com/auth/calendar.events https://www.googleapis.com/auth/calendar.readonly";

let tokenClient;
let accessToken = null;

// Warte, bis Google API geladen ist
function ensureGoogleLoaded(callback) {
    if (typeof google !== "undefined" && typeof gapi !== "undefined") {
        callback();
    } else {
        setTimeout(() => ensureGoogleLoaded(callback), 100);
    }
}

// Google API initialisieren
function handleClientLoad() {
    ensureGoogleLoaded(() => {
        gapi.load('client', initClient);
    });
}

// Initialisierung der API & OAuth-Client
function initClient() {
    gapi.client.init({
        apiKey: API_KEY,
        discoveryDocs: DISCOVERY_DOC
    }).then(() => {
        console.log("Google API geladen.");
        document.getElementById("authorize_button").style.display = "block";

        // Google Identity Services OAuth-Client initialisieren
        tokenClient = google.accounts.oauth2.initTokenClient({
            client_id: CLIENT_ID,
            scope: SCOPES,
            prompt: "consent",
            callback: (response) => {
                if (response.error) {
                    console.error("Fehler bei der Authentifizierung", response);
                    return;
                }
                accessToken = response.access_token;
                console.log("Anmeldung erfolgreich.");
                document.getElementById("authorize_button").style.display = "none";
                loadCalendarEvents();
            }
        });
    });
}

// Anmeldung starten (öffnet Popup)
function handleAuthClick() {
    tokenClient.requestAccessToken();
}

// Termin hinzufügen
function addEvent() {
    if (!accessToken) {
        alert("Bitte zuerst anmelden!");
        return;
    }

    const eventTitle = document.getElementById("eventTitle").value;
    const eventStart = document.getElementById("eventStart").value + ":00+01:00";
    const eventEnd = document.getElementById("eventEnd").value + ":00+01:00";

    var event = {
        'summary': eventTitle,
        'start': {
            'dateTime': eventStart,
            'timeZone': 'Europe/Berlin'
        },
        'end': {
            'dateTime': eventEnd,
            'timeZone': 'Europe/Berlin'
        }
    };

    gapi.client.calendar.events.insert({
        'calendarId': 'primary',
        'resource': event
    }).then(response => {
        alert("Termin hinzugefügt: " + response.result.htmlLink);
        loadCalendarEvents();
    }).catch(err => {
        console.error("Fehler beim Erstellen des Termins", err);
    });
}

// Kalender-Ereignisse laden und als Kalender anzeigen
function loadCalendarEvents() {
    if (!accessToken) {
        alert("Bitte zuerst anmelden!");
        return;
    }

    gapi.client.calendar.events.list({
        'calendarId': 'primary',
        'timeMin': (new Date()).toISOString(),
        'showDeleted': false,
        'singleEvents': true,
        'orderBy': 'startTime'
    }).then(response => {
        const events = response.result.items;
        const calendarDiv = document.getElementById('calendar');

        if (events.length > 0) {
            let calendarHTML = '<table><thead><tr><th>Start</th><th>Ende</th><th>Titel</th></tr></thead><tbody>';
            for (let i = 0; i < events.length; i++) {
                const event = events[i];
                const start = event.start.dateTime || event.start.date;
                const end = event.end.dateTime || event.end.date;
                calendarHTML += `<tr><td>${start}</td><td>${end}</td><td>${event.summary}</td></tr>`;
            }
            calendarHTML += '</tbody></table>';
            calendarDiv.innerHTML = calendarHTML;
        } else {
            calendarDiv.innerHTML = '<p>Keine kommenden Termine gefunden.</p>';
        }
    }).catch(err => {
        console.error("Fehler beim Laden der Kalender-Ereignisse", err);
    });
}