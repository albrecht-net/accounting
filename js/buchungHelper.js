// 
// Toggle button for template
// 
$(".chk-toggle-master").on('click', function() {
    if($(this).prop('checked')) {
        $(".chk-toggle-dis-slave").prop('disabled', false);
        $(".chk-toggle-dis-invert-slave").prop('disabled', true);
        $(".chk-toggle-req-slave").prop('required', false);
    } else {
        $(".chk-toggle-dis-slave").prop('disabled', true);
        $(".chk-toggle-dis-invert-slave").prop('disabled', false);
        $(".chk-toggle-req-slave").prop('required', true);
    }
});