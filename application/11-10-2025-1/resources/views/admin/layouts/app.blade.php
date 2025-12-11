<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="ColorlibHQ">
    <meta name="description" content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS.">
    <meta name="keywords" content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard"><!--end::Primary Meta Tags--><!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous"><!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css" integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous"><!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css" integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous"><!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fonts.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="{{asset('assets/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />

</head>


<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script> <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script> <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script> <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('assets/js/adminlte.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/sweetalert2@7.3.0/dist/sweetalert2.all.js"></script>
    <script src="{{ asset('assets/ckeditor/ckeditor.js') }}"></script>
    <script src="{{asset('assets/select2/select2.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        const SELECTOR_SIDEBAR_WRAPPER = ".sidebar-wrapper";
        const Default = {
            scrollbarTheme: "os-theme-light",
            scrollbarAutoHide: "leave",
            scrollbarClickScroll: true,
        };
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (
                sidebarWrapper &&
                typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== "undefined"
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });

        checked = false;

        function checkedAll() {
            if (checked == false) {
                checked = true
            } else {
                checked = false
            }
            for (var i = 0; i < document.getElementById('form_check').elements.length; i++) {
                document.getElementById('form_check').elements[i].checked = checked;
            }
        }


        function permissions(tables, status, statuscol) {
            var summeCode = document.getElementsByName("summe_code[]");
            var j = 0;
            var dataval = new Array();

            for (var i = 0; i < summeCode.length; i++) {
                if (summeCode[i].checked) {
                    dataval[j] = summeCode[i].value;
                    j++;
                }
            }
            if (dataval == "") {
                alert("Please check one or more!");
                return false;
            } else {
                var urls = "{{ url('admin/permissions') }}";
                var hrefdata = urls + "?approve_val=" + dataval + "&&tablename=" + tables + "&&status=" + status + "&&statuscol=" + statuscol;
                window.location.href = hrefdata;
            }
        }

        function deletedata(patch, tables) {
            var summeCode = document.getElementsByName("summe_code[]");
            var j = 0;
            var dataval = new Array();
            var furl = patch;
            var imagedel = false;
            for (var i = 0; i < summeCode.length; i++) {
                if (summeCode[i].checked) {
                    dataval[j] = summeCode[i].value;
                    j++;
                }
            }
            if (dataval == "") {
                swal({
                    title: "Unchecked credential!",
                    text: "Please check one or more!",
                    icon: "warning",
                });
                return false;
            } else {

                swal({
                    title: 'Are you sure?',
                    //type: 'warning',
                    imageUrl: "{{ asset('assets/images/favicons/favicon.png') }}",
                    confirmButtonText: 'Ok, Delete It',
                    showCloseButton: true,
                    showCancelButton: true,
                    text: "Do you want to Delete Selected Data !",
                    // input: 'checkbox',
                    //inputPlaceholder: ' Delete all Images for Selected Submission'
                }).then((result) => {
                    if (result.value) {
                        //swal({type: 'success', text: 'You have a bike!'});
                        imagedel = true;
                        $.ajax({
                            type: "GET",
                            url: furl,
                            data: {
                                'id': dataval,
                                'deletetype': 'multiple',
                                'deleteimage': imagedel,
                                'tablename': tables
                            },
                            cache: false,
                            success: function(html) {
                                swal({
                                    title: "Successfully Delete!",
                                    text: "All selected data are deleted",
                                    type: "success",
                                });
                                var len = html.length;
                                for (i in html) {
                                    $("#tablerow" + html[i]).fadeOut('slow');
                                }
                            }
                        });

                    } else {
                        console.log('modal was dismissed by ${result.dismiss}')
                    }

                });
            }
        }

        function deleteSingle(id, patch, tables) {

            var furl = patch;
            //alert(tables);
            var imagedel = false;

            swal({
                imageUrl: "{{ asset('assets/images/favicons/favicon.png') }}",
                title: 'Are you sure?',
                confirmButtonText: 'Ok, Delete It',
                //type: 'warning',
                showCloseButton: true,
                showCancelButton: true,
                text: "Do you want to Delete Selected data !",
                //input: 'checkbox',
                //inputPlaceholder: ' Delete all Images for Selected data'
            }).then((result) => {
                if (result.value) {
                    //swal({type: 'success', text: 'You have a bike!'});
                    imagedel = true;
                    $.ajax({
                        type: "GET",
                        url: furl,
                        data: {
                            'id': id,
                            'deletetype': 'single',
                            'deleteimage': imagedel,
                            'tablename': tables
                        },
                        cache: false,
                        success: function(html) {
                            swal({
                                title: "Successfully Delete!",
                                text: "All selected data are deleted",
                                type: "success",
                            });
                            $("#tablerow" + id).fadeOut('slow');
                        }
                    });

                } else if (result.value === 0) {
                    //swal({type: 'error', text: "You don't have a bike :("});
                    imagedel = false;
                    $.ajax({
                        type: "GET",
                        url: furl,
                        data: {
                            'id': id,
                            'deletetype': 'single',
                            'deleteimage': imagedel,
                            'tablename': tables
                        },
                        cache: false,
                        success: function(html) {
                            swal({
                                title: "Successfully Delete!",
                                text: "All selected Submission are deleted",
                                type: "success",
                            });
                            $("#tablerow" + id).fadeOut('slow');
                        }
                    });

                } else {
                    console.log('modal was dismissed by ${result.dismiss}')
                }

            });
        }


        $('#days_of_weeks').select2({
            placeholder: "Select an option",
            allowClear: true
        });
        $('.select2Data').select2({
            placeholder: "Select an option",
            allowClear: true
        });
    </script>
    <script>
        $(document).ready(function() {
            var cloneIndex = 0;
            $("#addMore").click(function() {
                cloneIndex++;
                var clone = $("#cloneRow").clone().attr("id", "cloneRow" + cloneIndex);
                clone.append($('<td><a class="btn btn-danger btn-sm" id="removeRow' + cloneIndex + '" href="javascript:void(0)"><i class="fa fa-trash"></i></a></td>'))
                    .appendTo("#itemTableData");

                $("#removeRow" + cloneIndex).click(function() {
                    $(this).closest("tr").remove();
                });
            });



            /** Add active class and stay opened when selected */
            var url = window.location;

            // For sidebar menu items
            $('ul.nav-sidebar a').filter(function() {
                return this.href == url;
            }).addClass('active');

            // For treeview (submenus)
            $('ul.nav-treeview a').filter(function() {
                return this.href == url;
            }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');


        });
    </script>

    <script>
        function addOrdinal(n) {
            let s = ["th", "st", "nd", "rd"],
                v = n % 100;
            return n + (s[(v - 20) % 10] || s[v] || s[0]);
        }

        $(".datepicker").datepicker({
            dateFormat: "DD, d MM, yy", // internal format
            changeMonth: true,
            changeYear: true,
            yearRange: "1900:" + new Date().getFullYear(),
            onSelect: function(dateText, inst) {
                let dateObj = $(this).datepicker("getDate");
                if (dateObj) {
                    let day = addOrdinal(dateObj.getDate());
                    let month = $.datepicker.formatDate("MM", dateObj);
                    let year = $.datepicker.formatDate("yy", dateObj);
                    $(this).val("The " + day + " " + month + ", " + year);
                }
            }
        });
    </script>
    @yield('page-script')
</body>

</html>