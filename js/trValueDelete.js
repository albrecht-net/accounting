$('.tr-delete').click(function() {
    // Variables
    var row = this,
        btnValue = row.value.split('-'),
        tableContent = btnValue[0],
        delID = btnValue[1],
        numrows = $(row).parents('tbody').find('tr').length;

    // AJAX Request
    $.ajax({
        type: 'POST',
        data: {trValueDelete: true, tableContent: tableContent, delID: delID},
        cache: false,
        success: function(response) {
            if (response == 1) {
                if (numrows > 1) {
                    $(row).parents('tr').remove();
                } else {
                    // Seite neuladen wenn nur 1 Zeile in Tabelle damit Meldung angezeigt wird
                    location.reload();  
                }
                console.log('Deleted record with ID: ' + delID);
            }
        }
    });

});