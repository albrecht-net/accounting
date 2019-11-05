$(document).ready(function (){

//
// lastEntries, index.php
//
dTableLastEntries = $('#dTableLastEntries').DataTable({
    searching: true,
    ordering: true,
    "order": [
        1, 'desc'
    ],
    scrollX: true,
    "columns": [
        {"data": "entryID"},
        {"data": {
            "_": "created.display",
            "sort": "created.timestamp"
        }},
        {"data": {
            "_": "date.display",
            "sort": "date.timestamp"
        }},
        {"data": "period"},
        {"data": "recipient"},
        {"data": "invoiceNo"},
        {"data": "entryText"},
        {"data": "debitAccount.display"},
        {"data": "creditAccount.display"},
        {"data": {
            "_": "grandTotal.display",
            "sort": "grandTotal.value"
        }, "className": "text-right", "type": "num-fmt"},
        {"data": "classification1"},
        {"data": "classification2"},
        {"data": "classification3"},
        {"data": "entryReference"},
        {"data": "reconcilation"},
    ],
    "createdRow": function(row) {
        $(row).addClass('text-nowrap');
    },
    "ajax": {
        "url": "includes/fetchLastEntriesData.inc.php",
        "type": "POST",
        "data": function(data) {
            return $.extend( {}, data, {
                'account': $('#selAccountLastEntries').val(),
                'period': $('#selPeriodLastEntries').val()
            });
        }
    },
    "dom": "<'row justify-content-between'<'col-auto'l><'col-auto'f>><'row'<'col-12'<'table-responsive't>>><'row'<'col-12'<'float-left'i><'float-right'p>>>"
});

//
// Overview loss, index.php
//
dTableLoss = $('#dTableLoss').DataTable({
    searching: false,
    ordering: false,
    "columns": [
        {"data": "categoryLabel.display",
        "className": "p-0"
        },
        {"data": {
            "_": "balance.display",
            "sort": "balance.value"
            },
            "className": "p-0 text-nowrap text-right",
            "type": "num-fmt"
        },
    ],
    "ajax": {
        "url": "includes/fetchLossData.inc.php",
        "type": "POST",
        "data": function(data) {
            return $.extend( {}, data, {
                'period': $('#selPeriodLoss').val()
            });
        }
    },
    "dom": "<'row'<'col-12't>>"
});


//
// templates, templates.php
//
$('#dTableTemplates').DataTable({
    paging: false,
    "columnDefs": [
        {'targets': 3, 'searchable': false},
        {'targets': [2, 3], 'orderable': false}
    ],
    "order": [
        1, 'asc'
    ],
    "dom": "<'float-right'f><'table-responsive't><'float-left'i>"
});

//
// standingOrder, standingOrder.php
//
$('#dTableSavedSo').DataTable({
    paging: false,
    "columnDefs": [
        {'targets': [5, 6, 9, 10], 'searchable': false},
        {'targets': [9, 10], 'orderable': false}
    ],
    "dom": "<'float-right'f><'table-responsive't><'float-left'i>"
});

//
// recipient, recipient.php
//
$('#dTableRecipient').DataTable({
    paging: false,
    "dom": "<'float-right'f><'table-responsive't><'float-left'i>"
});

//
// classification, classification.php
//
$('#dTableClassification').DataTable({
    paging: false,
    "dom": "<'float-right'f><'table-responsive't><'float-left'i>"
});

//
// database, settings/database.php
//
$('#dTableDatabase').DataTable({
    paging: false,
    searching: false,
    info: false,
    ordering: false,
    "dom": "<'float-right'f><'table-responsive't><'float-left'i>"
});

});