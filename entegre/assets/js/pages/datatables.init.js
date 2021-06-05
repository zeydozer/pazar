$.extend(true, $.fn.dataTable.defaults, {
    language: {
        paginate: {
            previous: "<i class='uil uil-angle-left'>",
            next: "<i class='uil uil-angle-right'>"
        },
        "decimal": "",
        "emptyTable": "Tabloda veri yok",
        "info": "_TOTAL_ veriden _START_ ile _END_ gösteriliyor",
        "infoEmpty": "0 veriden 0 ile 0 gösteriliyor",
        "infoFiltered": "(Toplam _MAX_ veriden filtrelendi)",
        "infoPostFix": "",
        "thousands": ",",
        "lengthMenu": "_MENU_ veri göster",
        "loadingRecords": "Yükleniyor...",
        "processing": "İşleniyor...",
        "search": "Ara:",
        "zeroRecords": "Eşleşen veri bulunamadı",
    },
    drawCallback: function() {
        $(".dataTables_paginate > .pagination").addClass("pagination-rounded")

        $('[data-plugin="customselect"]').each(function() {

            if ($(this).attr('data-value') != '')

                $(this).val($(this).attr('data-value'));

            $(this).select2();
        });

        var a = {};

        $('[data-toggle="touchspin"]').each(function(t, i) {

            var e = $.extend({}, a, $(i).data());

            $(i).TouchSpin(e);
        });

        var width = $('.select2-container').closest('.input-group').width() - $('.input-group-append').width();

        $('.select2-container').css('max-width', width);
    },
    pageLength: 25,
    bAutoWidth: false,
    processing: true,
});

$(document).ready(function() {
    $("#basic-datatable").DataTable({
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
        }
    });

    var a = $("#datatable-buttons").DataTable({
        lengthChange: !1,
        buttons: ["copy", "print"],
        language: {
            paginate: {
                previous: "<i class='uil uil-angle-left'>",
                next: "<i class='uil uil-angle-right'>"
            }
        },
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
        }
    });

    $("#selection-datatable").DataTable({
        select: { style: "multi" },
        language: {
            paginate: {
                previous: "<i class='uil uil-angle-left'>",
                next: "<i class='uil uil-angle-right'>"
            }
        },
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
        }
    });

    $("#key-datatable").DataTable({
        keys: !0,
        language: {
            paginate: {
                previous: "<i class='uil uil-angle-left'>",
                next: "<i class='uil uil-angle-right'>"
            }
        },
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
        }
    });

    a.buttons().container().appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)");

    $('#custom-datatable').on('page.dt', function() {
        $('html, body').animate({
            scrollTop: 0
        }, 150);
    });
});