$('.tr-delete').click(function() {
    // Variables
    var row = this,
        tableID = $(row).closest('table').attr('id'),
        table = $('#' + tableID).DataTable(),
        btnValue = row.value,
        tableContent = btnValue.split('-')[0],
        delID = btnValue.split('-')[1],
        numrows = table.rows().count();

    // Bestätigung beim Löschen einer Datenbank
    if (tableContent == 'Database' && !window.confirm('Wollen Sie die Verknüpfung dieser Datenbank wirklich entfernen?')) {
        return;
    }

    // AJAX Request
    $.ajax({
        type: 'POST',
        data: {trValueDelete: true, tableContent: tableContent, delID: delID},
        cache: false,
        success: function(response) {
            if (response == 1) {
                if (numrows > 1) {
                    table.row('#' + btnValue).remove().draw(false);
                } else {
                    // Seite neuladen wenn nur 1 Zeile in Tabelle damit Meldung angezeigt wird
                    location.reload();  
                }
                console.log('Deleted record with ID: ' + delID);
            }
        }
    });

});