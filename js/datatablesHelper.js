$(document).ready(function (){

//
// lastEntries, index.php
//
$('#dTableLastEntries').DataTable({
    paging: false,
    searching: true,
    info: false,
    ordering: true,
    'order': [
        1, 'desc'
    ],
    scrollX: true,
    "dom": '<"float-right"f>t'
});

//
// templates, templates.php
//
$('#dTableTemplates').DataTable({
    paging: false,
    'columnDefs': [
        {'targets': 3, 'searchable': false},
        {'targets': [2, 3], 'orderable': false}
    ],
    'order': [
        1, 'asc'
    ],
    "dom": '<"float-right"f><"table-responsive"t><"float-left"i>'
});

//
// standingOrder, standingOrder.php
//
$('#dTableSavedSo').DataTable({
    paging: false,
    'columnDefs': [
        {'targets': [5, 6, 9, 10], 'searchable': false},
        {'targets': [9, 10], 'orderable': false}
    ],
    "dom": '<"float-right"f><"table-responsive"t><"float-left"i>'
});

//
// recipient, recipient.php
//
$('#dTableRecipient').DataTable({
    paging: false,
    "dom": '<"float-right"f><t><"float-left"i>'
});

//
// classification, classification.php
//
$('#dTableClassification').DataTable({
    paging: false,
    "dom": '<"float-right"f><t><"float-left"i>'
});

//
// database, settings/database.php
//
$('#dTableDatabase').DataTable({
    paging: false,
    searching: false,
    info: false,
    ordering: false,
    "dom": '<"float-right"f><"table-responsive"t><"float-left"i>'
});

});