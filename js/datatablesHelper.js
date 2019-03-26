//
// last10Entries, index.php
//
$(document).ready(function (){
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
});