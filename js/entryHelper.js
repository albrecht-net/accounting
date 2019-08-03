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
            } else {
                console.log('Failed to skip record with ID: ' + skipID);
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

//
// Subtotal entryReference
//
function subtotalOfSelect(prop, attr) {
    var values = $(prop).map(function() {
        return parseFloat($(this).attr(attr));
    }).get(),
    sum = 0;

    console.groupCollapsed('Values of selection');
    console.log(values);
    console.groupEnd();

    values.forEach(function (item) {
        sum += item;
    });

    return sum;
};

// Format number to locale settings and 2 fixed-point notation
function numfmt(num) {
    return num.toLocaleString(document.documentElement.lang, {maximumFractionDigits: 2, minimumFractionDigits: 2});
}

function entryReferenceDifference(sumEntryReference) {
    if($('#entryReference option:selected').length == 0) {
        $('#entryReferenceDifference').removeAttr('class');
        $('#entryReferenceDifference').html('<i>Keine Buchungsreferenz gewählt</i>');
    } else {
        var sumEntryDifference = $('#grandTotal').val() - sumEntryReference;

        if(sumEntryDifference == 0) {
            $('#entryReferenceDifference').attr({
                'class' : 'text-success'
            });
        } else {
            $('#entryReferenceDifference').attr({
                'class' : 'text-danger'
            });
        }
        $('#entryReferenceDifference').text('CHF ' + numfmt(sumEntryDifference));
    }
}

// On document ready
$(document).ready(function() {
    var sumEntryReference = subtotalOfSelect($('#entryReference option:selected'), 'data-grandTotal');
    $('#entryReferenceSubtotal').text(numfmt(sumEntryReference));
    entryReferenceDifference(sumEntryReference);
});

// On selection change
$("#entryReference").change(function() {
    var sumEntryReference = subtotalOfSelect($('#entryReference option:selected'), 'data-grandTotal');
    $('#entryReferenceSubtotal').text(numfmt(sumEntryReference));
    entryReferenceDifference(sumEntryReference);
});

// On input change of grandTotal
$('#grandTotal').on('change paste keyup', function() {
    var sumEntryReference = subtotalOfSelect($('#entryReference option:selected'), 'data-grandTotal');
    entryReferenceDifference(sumEntryReference);
});