<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data="{ sidebarOpen: false, darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                            400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                            800: '#1e40af', 900: '#1e3a8a', 950: '#172554'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        
        /* Table Card Container - Responsive */
        .table-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table-card-body {
            padding: 1rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        @media (min-width: 640px) {
            .table-card-body { padding: 1.5rem; }
        }
        .dark .table-card { background-color: #1f2937; }
        
        /* DataTables Base Styling */
        .dataTables_wrapper { font-size: 0.875rem; }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label { 
            color: #374151; 
            font-weight: 500;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .dataTables_wrapper .dataTables_length select { 
            border-radius: 0.375rem; 
            border-color: #d1d5db; 
            font-size: 0.875rem; 
            padding: 0.25rem 0.5rem; 
            margin-right: 0.5rem;
            background-color: white;
            color: #111827;
        }
        .dataTables_wrapper .dataTables_filter input { 
            border-radius: 0.375rem; 
            border: 1px solid #d1d5db; 
            font-size: 0.875rem; 
            padding: 0.5rem 0.75rem; 
            margin-left: 0.5rem;
            background-color: white;
            color: #111827;
            width: 100%;
            max-width: 200px;
        }
        @media (max-width: 640px) {
            .dataTables_wrapper .dataTables_filter input {
                margin-left: 0;
                margin-top: 0.5rem;
                max-width: 100%;
            }
            .dataTables_wrapper .dataTables_filter label {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        .dataTables_wrapper .dataTables_info { 
            color: #4b5563; 
            padding: 1rem 0;
            font-size: 0.75rem;
        }
        .dataTables_wrapper .dataTables_paginate { 
            padding: 1rem 0;
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button { 
            padding: 0.375rem 0.625rem; 
            margin: 0 0.125rem; 
            border-radius: 0.375rem; 
            color: #374151; 
            cursor: pointer;
            border: none !important;
            background: transparent !important;
            font-size: 0.875rem;
        }
        @media (max-width: 640px) {
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.5rem 0.75rem;
            }
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { 
            background-color: #f3f4f6 !important; 
            color: #111827 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { 
            background-color: #2563eb !important; 
            color: white !important; 
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { 
            background-color: #1d4ed8 !important; 
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { 
            color: #9ca3af !important; 
            cursor: not-allowed; 
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover { 
            background-color: transparent !important; 
        }
        
        table.dataTable { width: 100%; border-collapse: collapse; min-width: 600px; }
        table.dataTable thead th { 
            padding: 0.75rem 0.75rem; 
            text-align: left; 
            font-size: 0.7rem; 
            font-weight: 600; 
            color: #4b5563; 
            text-transform: uppercase; 
            letter-spacing: 0.05em; 
            background-color: #f9fafb; 
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        table.dataTable tbody td { 
            padding: 0.75rem 0.75rem; 
            font-size: 0.8125rem; 
            color: #374151; 
            border-bottom: 1px solid #f3f4f6;
        }
        @media (min-width: 640px) {
            table.dataTable thead th { padding: 0.75rem 1rem; font-size: 0.75rem; }
            table.dataTable tbody td { padding: 0.75rem 1rem; font-size: 0.875rem; }
        }
        table.dataTable tbody tr { 
            background-color: white; 
            transition: background-color 0.15s; 
        }
        table.dataTable tbody tr:hover { background-color: #f9fafb; }
        table.dataTable tbody tr.odd { background-color: rgba(249, 250, 251, 0.5); }
        table.dataTable.no-footer { border-bottom: none; }
        
        .dt-buttons { 
            margin-bottom: 0.75rem; 
            display: flex; 
            flex-wrap: wrap;
            gap: 0.375rem; 
        }
        .dt-buttons .dt-button { 
            padding: 0.375rem 0.625rem; 
            font-size: 0.75rem; 
            border-radius: 0.375rem; 
            background-color: #f3f4f6; 
            color: #374151; 
            transition: background-color 0.15s;
            border: none !important;
        }
        @media (min-width: 640px) {
            .dt-buttons .dt-button { 
                padding: 0.5rem 0.75rem; 
                font-size: 0.875rem; 
            }
        }
        .dt-buttons .dt-button:hover { background-color: #e5e7eb; }
        
        .dataTables_empty { text-align: center; color: #6b7280; padding: 2rem 0; }

        /* Dark Mode DataTables */
        .dark .dataTables_wrapper .dataTables_length label,
        .dark .dataTables_wrapper .dataTables_filter label { 
            color: #d1d5db; 
        }
        .dark .dataTables_wrapper .dataTables_length select { 
            border-color: #4b5563; 
            background-color: #374151; 
            color: white;
        }
        .dark .dataTables_wrapper .dataTables_filter input { 
            border-color: #4b5563; 
            background-color: #374151; 
            color: white;
        }
        .dark .dataTables_wrapper .dataTables_filter input::placeholder {
            color: #9ca3af;
        }
        .dark .dataTables_wrapper .dataTables_info { color: #9ca3af; }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button { 
            color: #d1d5db !important; 
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button:hover { 
            background-color: #374151 !important; 
            color: white !important;
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.current { 
            background-color: #2563eb !important; 
            color: white !important; 
        }
        .dark .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { 
            color: #4b5563 !important; 
        }
        
        .dark table.dataTable thead th { 
            color: #d1d5db; 
            background-color: #1f2937; 
            border-bottom-color: #374151;
        }
        .dark table.dataTable tbody td { 
            color: #d1d5db; 
            border-bottom-color: #374151;
        }
        .dark table.dataTable tbody tr { background-color: #1f2937; }
        .dark table.dataTable tbody tr:hover { background-color: rgba(55, 65, 81, 0.5); }
        .dark table.dataTable tbody tr.odd { background-color: rgba(31, 41, 55, 0.8); }
        
        .dark .dt-buttons .dt-button { 
            background-color: #374151; 
            color: #d1d5db; 
        }
        .dark .dt-buttons .dt-button:hover { background-color: #4b5563; }
        
        .dark .dataTables_empty { color: #9ca3af; }

        /* DataTables sorting icons fix for dark mode */
        .dark table.dataTable thead .sorting:before,
        .dark table.dataTable thead .sorting:after,
        .dark table.dataTable thead .sorting_asc:before,
        .dark table.dataTable thead .sorting_asc:after,
        .dark table.dataTable thead .sorting_desc:before,
        .dark table.dataTable thead .sorting_desc:after {
            opacity: 0.5;
        }
        .dark table.dataTable thead .sorting_asc:before,
        .dark table.dataTable thead .sorting_desc:after {
            opacity: 1;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    <div class="min-h-full">
        @auth
            @include('layouts.partials.sidebar')
            <div class="lg:pl-64">
                @include('layouts.partials.header')
                <main class="py-6">
                    <div class="mx-auto px-4 sm:px-6 lg:px-8">
                        @if(session('success'))
                            <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-4 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </main>
            </div>
        @else
            @yield('content')
        @endauth
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script>
        $.extend(true, $.fn.dataTable.defaults, {
            responsive: true,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok",
                emptyTable: "Tidak ada data tersedia",
                paginate: {
                    first: "Awal",
                    previous: "Sebelumnya",
                    next: "Selanjutnya",
                    last: "Akhir"
                }
            },
            dom: '<"flex flex-wrap items-center justify-between gap-4 mb-4"<"flex items-center gap-2"l><"flex items-center gap-2"Bf>>rtip',
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            buttons: [
                { extend: 'copy', text: 'Copy', className: 'dt-button' },
                { extend: 'excel', text: 'Excel', className: 'dt-button' },
                { extend: 'pdf', text: 'PDF', className: 'dt-button' },
                { extend: 'print', text: 'Print', className: 'dt-button' }
            ]
        });
    </script>
    @stack('scripts')
</body>
</html>
