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