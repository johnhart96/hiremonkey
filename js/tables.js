$(document).ready(function() {
    $('#jobs').DataTable({
        "searching":     false   
    });
    $('#customers').DataTable({
        "searching":     false   
    });
    $('#kit').DataTable({
        "searching":     false   
    });
    $('#revenue').DataTable({
        "searching":     true   
    });
    $('#repairs').DataTable({
        "searching":     true   
    });
} );