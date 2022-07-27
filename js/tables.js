$(document).ready(function() {
    $('#jobs').DataTable({
        "searching":     false,
        "paging":       false
    });
    $('#customers').DataTable({
        "searching":     false,
        "paging":       false 
    });
    $('#kit').DataTable({
        "searching":     false,
        "paging":       false  
    });
    $('#revenue').DataTable({
        "searching":     true,
        "paging":       false  
    });
    $('#repairs').DataTable({
        "searching":     true,
        "paging":       false  
    });
} );