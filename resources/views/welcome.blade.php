<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Add this line for auto-refresh --}}
    <meta http-equiv="refresh" content="6000"> {{-- 300 seconds = 5 minutes --}}

    <title>Laporan Excel Pasien KUBR</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">

    <header class="bg-white shadow-md sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0 flex items-center">
                    <img class="h-8 w-auto" src="/logo_kubr.png" alt="Logo">
                    <span class="text-xl font-semibold text-gray-800 ml-2">KUBR Report Generator</span>
                </div>

                <div class="text-sm">
                    @if (Route::has('login'))
                    <div class="space-x-4">
                        @auth
                        <span class="text-gray-600">Welcome, {{ Auth::user()->name }}</span>
                        <a href="{{ url('/home') }}" class="text-gray-700 hover:text-blue-600">Home</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="text-gray-700 hover:text-blue-600">
                                Log Out
                            </a>
                        </form>
                        @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">Log in</a>
                        @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 text-gray-700 hover:text-blue-600">Register</a>
                        @endif
                        @endauth
                    </div>
                    @endif
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-xl mx-auto"> {{-- Constrain width more if needed --}}

            {{-- Form Card --}}
            <div class="bg-white shadow-lg overflow-hidden sm:rounded-lg">

                {{-- Card Header --}}
                <div class="px-6 py-5 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-file-excel text-green-600 mr-3 text-2xl"></i> {{-- Icon added --}}
                        Export Laporan Pasien Rajal
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Pilih rentang tanggal untuk men-generate laporan dalam format Excel.
                    </p>
                </div>

                {{-- Card Body --}}
                <div class="px-6 py-6">

                    {{-- Display Success/Error Messages --}}
                    @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded text-sm">
                        {{ session('success') }}
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                        {{ session('error') }}
                    </div>
                    @endif
                    @if($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                        <p class="font-medium">Harap perbaiki kesalahan berikut:</p>
                        <ul class="list-disc list-inside mt-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Form --}}
                    <form action="/laporan" method="post" class="space-y-5">
                        @csrf

                        {{-- Date Inputs Side-by-Side on Medium Screens and Up --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                            {{-- Date 1 Input --}}
                            <div>
                                <label for="date1" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                    Mulai</label>
                                <input type="date" name="date1" id="date1" value="{{ old('date1', date('Y-m-01')) }}"
                                    required
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('date1') border-red-500 ring-1 ring-red-500 @enderror">
                                @error('date1')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Date 2 Input --}}
                            <div>
                                <label for="date2" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                                    Selesai</label>
                                <input type="date" name="date2" id="date2" value="{{ old('date2', date('Y-m-d')) }}"
                                    required
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('date2') border-red-500 ring-1 ring-red-500 @enderror">
                                {{-- Simple validation helper text --}}
                                <p class="mt-1 text-xs text-gray-500">Pastikan tanggal selesai tidak sebelum tanggal
                                    mulai.</p>
                                @error('date2')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="inline-flex items-center justify-center px-5 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition ease-in-out duration-150 disabled:opacity-50">
                                <i class="fas fa-download mr-2"></i>
                                Download Excel
                            </button>
                        </div>
                    </form>
                </div> {{-- End Card Body --}}
            </div> {{-- End Form Card --}}
        </div> {{-- End Max Width Container --}}
    </main>

    <footer class="mt-auto bg-white border-t border-gray-200 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Reyhan Asta. All rights reserved.
        </div>
    </footer>

</body>

</html>