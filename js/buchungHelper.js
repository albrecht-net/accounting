//
// Toggle button for template
//
function chktoggle(prop) {
    if(prop) {
        // Vorlage Beschreibung, Vorlage Radiobuttons
        $(".chk-toggle-dis-slave").prop({
            'disabled': false,
            'required': true
        });
        // Datum, Buchungsreferenz
        $(".chk-toggle-dis-invert-slave").prop('disabled', true);
        // Konto Haben, Konto Soll, Betrag
        $(".chk-toggle-req-slave").prop('required', false);
    } else {
        // Vorlage Beschreibung, Vorlage Radiobuttons
        $(".chk-toggle-dis-slave").prop({
            'disabled': true,
            'required': false
        });
        // Datum, Buchungsreferenz
        $(".chk-toggle-dis-invert-slave").prop('disabled', false);
        // Konto Haben, Konto Soll, Betrag
        $(".chk-toggle-req-slave").prop('required', true);
    }
};

// Properties beim Laden der Seite anwenden
$(document).ready(function() {
    chktoggle($("#chkAddTemplate").prop('checked'));
});

// Properties bei Änderung der Auswahl anwendung
$("#chkAddTemplate").change(function() {
    chktoggle($(this).prop('checked'));
});

//
// Skip standing-order
//
function getURLParameter(par) {
    var pageURL = window.location.search.substring(1),
        URLVar = pageURL.split('&'),
        result = undefined;
    for (var i = 0; i < URLVar.length; i++) {
        var URLParameter = URLVar[i].split('=');
        if (URLParameter[0] == par) {
            result = URLParameter[1];
            break;
        }
    }
    return result;
};

$('.skip-standingOrder').click(function() {
    // Variables
    var so = this,
        skipID = so.value,
        numrows = $(so).parents('#accordionStandingOrder').find('.card').length;

    // Button "Dauerauftrag auswählen" deaktivieren
    $('#chStOrBtn' + skipID).addClass('disabled');

    // AJAX Request
    $.ajax({
        url: './includes/skipStandingOrder.inc.php',
        type: 'POST',
        data: {skipID: skipID},
        dataType: 'json',
        cache: false,
        success: function(response) {
            if (response.success) {
                if (getURLParameter('standingOrder') == skipID) {
                    // Seite ohne Parameter neuladen wenn zu überspringender Dauerauftrag ausgewählt ist
                    location = location.pathname;
                } else if (numrows > 1 && response.removeRow) {
                    $(so).parents('div.card').remove();
                } else if (!response.removeRow) {
                    $('#dueDate' + skipID).text(response.nextExecutionDate);
                } else {
                    // Seite neuladen wenn nur 1 Zeile in Tabelle damit Meldung angezeigt wird
                    location.reload();
                }
                console.log('Skipped record with ID: ' + skipID);
            }
        },
        error: function() {
            console.log('Failed to skip record with ID: ' + skipID);
        },
        complete: function() {
            // Button "Dauerauftrag auswählen" aktivieren
            $('#chStOrBtn' + skipID).removeClass('disabled');
        }
        
    });
});