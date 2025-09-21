<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $setting->nama_perusahaan }} | @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="icon" href="{{ url($setting->path_logo) }}" type="image/png">

    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/dist/css/skins/_all-skins.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('/AdminLTE-2/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    
    {{-- TAMBAHAN: Select2 CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    @stack('css')

    <!-- Mobile Responsive CSS -->
    <style>
        /* Mobile responsive untuk tabel */
        @media (max-width: 768px) {
            .table-responsive {
                border: 0;
                margin-bottom: 0;
            }
            
            .table-responsive .table {
                margin-bottom: 0;
            }
            
            /* Pastikan scroll horizontal smooth di mobile */
            .table-responsive {
                -webkit-overflow-scrolling: touch;
                overflow-x: auto;
            }
            
            /* Optimasi untuk layar kecil */
            .box-body {
                padding: 10px;
            }
            
            /* Button group responsive */
            .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
            
            .btn-group .btn {
                flex: 1;
                min-width: auto;
            }
        }
        
        /* DataTables scroll horizontal */
        .dataTables_wrapper .dataTables_scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Pastikan tabel tidak terpotong */
        .dataTables_scrollBody {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body class="hold-transition skin-purple-light sidebar-mini">
    <div class="wrapper">

        @includeIf('layouts.header')

        @includeIf('layouts.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    @yield('title')
                </h1>
                <ol class="breadcrumb">
                    @section('breadcrumb')
                        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                    @show
                </ol>
            </section>

            <!-- Main content -->
            <section class="content">
                
                @yield('content')

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        @includeIf('layouts.footer')
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 3 -->
    <script src="{{ asset('AdminLTE-2/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('AdminLTE-2/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- Moment -->
    <script src="{{ asset('AdminLTE-2/bower_components/moment/min/moment.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('AdminLTE-2/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('AdminLTE-2/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    
    {{-- TAMBAHAN: Select2 JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    
    {{-- TAMBAHAN: SortableJS untuk drag & drop favorit --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    
    {{-- TAMBAHAN: SweetAlert2 untuk notifikasi cantik --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- AdminLTE App -->
    <script src="{{ asset('AdminLTE-2/dist/js/adminlte.min.js') }}"></script>
    <!-- jQuery Validation Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
    <!-- Global Notifications -->
    <script src="{{ asset('js/notifications.js') }}"></script>

    <script>
      $.extend(true, $.fn.dataTable.defaults, {
    lengthMenu: [
        [7,10,25, 50, 100, 500, -1], 
        [7,10,25, 50, 100, 500, "Semua"]
    ],
    pageLength: 10,
    language: {
        lengthMenu: "Tampilkan _MENU_ data per halaman",
        zeroRecords: "Tidak ada data yang ditemukan",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
        infoFiltered: "(disaring dari _MAX_ total data)",
        search: "Cari:",
        paginate: {
            first: "Pertama",
            last: "Terakhir",
            next: "Selanjutnya",
            previous: "Sebelumnya"
        },
        processing: '<i class="fa fa-spinner fa-spin"></i> Memuat data...',
        emptyTable: "Tidak ada data yang tersedia"
    }
});
        function preview(selector, temporaryFile, width = 200)  {
            $(selector).empty();
            $(selector).append(`<img src="${window.URL.createObjectURL(temporaryFile)}" width="${width}">`);
        }

        {{-- TAMBAHAN: Global configuration untuk Select2 --}}
        $(document).ready(function() {
            // Set default theme untuk Select2
            $.fn.select2.defaults.set("theme", "bootstrap");
            
            // Configuration untuk semua Select2
            $('.select2').select2({
                theme: 'bootstrap',
                width: '100%'
            });

            {{-- TAMBAHAN: Helper function untuk format currency Indonesia --}}
            window.formatRupiah = function(amount, prefix = 'Rp. ') {
                let number_string = amount.toString().replace(/[^,\d]/g, ''),
                    split = number_string.split(','),
                    sisa = split[0].length % 3,
                    rupiah = split[0].substr(0, sisa),
                    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                return prefix + rupiah;
            };

            {{-- TAMBAHAN: Global AJAX error handler --}}
            $(document).ajaxError(function(event, xhr, settings) {
                if (xhr.status === 419) {
                    showErrorAlert('Sesi telah berakhir. Silakan refresh halaman.');
                    setTimeout(() => location.reload(), 2000);
                }
            });
        });

        // ========== SWEETALERT2 FUNCTIONS ==========
        
        // Fungsi untuk notifikasi sukses
        window.showSuccessAlert = function(message = 'Data berhasil disimpan!') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        };

        // Fungsi untuk notifikasi error
        window.showErrorAlert = function(message = 'Terjadi kesalahan!') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        };

        // Fungsi untuk notifikasi warning
        window.showWarningAlert = function(message = 'Peringatan!') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        };

        // Fungsi untuk notifikasi info
        window.showInfoAlert = function(message = 'Informasi') {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: message,
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        };

        // Fungsi untuk konfirmasi delete
        window.showDeleteConfirm = function(callback, message = 'Yakin ingin menghapus data ini?') {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        };

        // Fungsi untuk konfirmasi umum
        window.showConfirm = function(callback, message = 'Yakin ingin melanjutkan?', title = 'Konfirmasi') {
            Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        };

        // Notifikasi loading
        window.showLoadingAlert = function(message = 'Memproses data...') {
            Swal.fire({
                title: message,
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        };

        // Tutup loading
        window.closeLoadingAlert = function() {
            Swal.close();
        };

        // Override alert default dengan SweetAlert2
        window.alert = function(message) {
            showInfoAlert(message);
        };

        // Override confirm default dengan SweetAlert2
        window.confirmOriginal = window.confirm;
        window.confirm = function(message) {
            return new Promise((resolve) => {
                showConfirm(() => resolve(true), message);
                // Jika tidak dikonfirmasi, resolve false setelah delay
                setTimeout(() => resolve(false), 100);
            });
        };

    </script>
    
    {{-- TAMBAHAN: Global Delete Handler --}}
    <script src="{{ asset('/js/delete-handler.js') }}"></script>
    @stack('scripts')
</body>
</html>