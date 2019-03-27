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
        {'targets': 3, 'orderable': false}
    ],
    "dom": '<"float-right"f><"table-responsive"t><"float-left"i>'
});

//
// standingOrder, standingOrder.php
//
$('#dTableSavedSo').DataTable({
    paging: false,
    'columnDefs': [
        {'targets': [5, 6, 9], 'searchable': false},
        {'targets': 9, 'orderable': false}
    ],
    "dom": '<"float-right"f><"table-responsive"t><"float-left"i>'
});

});