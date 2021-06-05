$(document).ready(function () 
{ 
    $("#summernote-editor").summernote(
    { 
        height: 250, 
        minHeight: null, 
        maxHeight: null, 
        focus: !1, 
        tooltip: false, 
        name: 'description',
        lang: 'tr-TR',
    });
    
    $("#summernote-inline").summernote({ airMode: !0 });
});