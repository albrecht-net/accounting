$(document).ready(function() { 
    $('.tr-delete').click(function() {
        // Variables
        var row = this;
        var btnValue = this.value.split('-');
        var tableContent = btnValue[0];
        var delID = btnValue[1];
        var numrows = $(this).parents('tbody').find('tr').length

        // AJAX Request
        $.ajax({
            type: 'POST',
            data: {tableContent: tableContent, delID: delID},
            success: function(response) {
                if (response == 1) {
                    if (numrows > 1) {
                        $(row).parents('tr').remove();
                    } else {
                        // Seite neuladen wenn nur 1 Zeile in Tabelle damit Meldung angezeigt wird
                        location.reload();  
                    }
                }
            }
        });

    });
});