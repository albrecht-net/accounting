// Radiobutton Toggle
$("input[name=validToType]").change(function() {
    switch ($(this).val()) {
        case '1':
            $(".radio-toggle-2").prop('disabled', true);
            $(".radio-toggle-4").prop('disabled', true);
            break;
        case '2':
            $(".radio-toggle-2").prop('disabled', false);
            $(".radio-toggle-4").prop('disabled', true);
            break;
        case '4':
            $(".radio-toggle-2").prop('disabled', true);
            $(".radio-toggle-4").prop('disabled', false);
            break;
    }
});

// Begrenzung der Auswahl durch Periodizitättyp
$("#periodicityType").change(function() {
    switch ($(this).val()) {
        case '1':
            $("#validFromType1").prop('checked', true);
            $("#validFromType2").prop('disabled', true);
            $("#periodicityValue").prop('disabled', false);
            break;
        case '2':
            $("#validFromType1").prop('checked', true);
            $("#validFromType2").prop('disabled', true);
            $("#periodicityValue").prop('disabled', false);
            break;
        case '4':
            $("#validFromType2").prop('disabled', false);
            $("#periodicityValue").prop('disabled', false);
            break;
        case '8':
            $("#validFromType2").prop('disabled', false);
            $("#periodicityValue").prop('disabled', false);
            break;
        case '16':
            $("#validFromType1").prop('checked', true);
            $("#validFromType2").prop('disabled', true);
            $("#periodicityValue").prop('value', 1);
            $("#periodicityValue").prop('disabled', true);
            break;
    }
});