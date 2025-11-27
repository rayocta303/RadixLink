@extends('layouts.app')

@section('title', 'Router Script Result')
@section('page-title', 'Hasil Generate Script')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                @if(isset($nas))
                    Script untuk: {{ $nas->name }} ({{ $nas->nasname }})
                @elseif(isset($customerCount))
                    Script untuk {{ $customerCount }} Customer
                @else
                    Script Generated
                @endif
            </h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tipe: 
                @switch($scriptType)
                    @case('full') Full Configuration @break
                    @case('radius') RADIUS Server @break
                    @case('pppoe') PPPoE Server @break
                    @case('hotspot') Hotspot Server @break
                    @case('firewall') Firewall Rules @break
                    @case('profiles') Service Profiles @break
                    @case('customers') Customer Scripts @break
                    @case('radius-client') FreeRADIUS Client @break
                    @default {{ ucfirst($scriptType) }}
                @endswitch
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('tenant.router-scripts.index') }}" class="inline-flex items-center gap-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
                Kembali
            </a>
            <button onclick="copyScript()" id="copyBtn" class="inline-flex items-center gap-2 rounded-md bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                </svg>
                <span id="copyBtnText">Copy Script</span>
            </button>
            <button onclick="downloadScript()" class="inline-flex items-center gap-2 rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Download .rsc
            </button>
            <button onclick="downloadScript('txt')" class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Download .txt
            </button>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Script Output</h3>
            <span id="copyStatus" class="text-sm text-green-600 dark:text-green-400 hidden">Copied!</span>
        </div>
        <div class="p-6">
            <pre id="scriptContent" class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm font-mono whitespace-pre-wrap">{{ $script }}</pre>
        </div>
    </div>

    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Perhatian</h3>
                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Backup konfigurasi router sebelum menjalankan script ini</li>
                        <li>Review dan sesuaikan IP address, interface, dan parameter lainnya</li>
                        <li>Jalankan script baris per baris untuk menghindari error</li>
                        <li>Test konektivitas RADIUS setelah konfigurasi selesai</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Cara Menjalankan Script</h3>
        </div>
        <div class="p-6 prose dark:prose-invert max-w-none">
            <h4>Via Winbox</h4>
            <ol>
                <li>Buka Winbox dan login ke router</li>
                <li>Buka menu <strong>System > Scripts</strong></li>
                <li>Klik <strong>Add New</strong></li>
                <li>Paste script dan jalankan</li>
            </ol>

            <h4>Via Terminal</h4>
            <ol>
                <li>Login ke router via SSH atau Winbox Terminal</li>
                <li>Copy dan paste script baris per baris</li>
                <li>Atau upload file .rsc dan import dengan: <code>/import file=router-script.rsc</code></li>
            </ol>

            <h4>Via FTP</h4>
            <ol>
                <li>Upload file .rsc ke router via FTP</li>
                <li>Login ke router terminal</li>
                <li>Jalankan: <code>/import file=router-script.rsc</code></li>
            </ol>
        </div>
    </div>
</div>

<script>
const scriptType = '{{ $scriptType }}';
const scriptFilename = 'router-script-' + scriptType + '-{{ date("Ymd-His") }}';

function copyScript() {
    const scriptContent = document.getElementById('scriptContent').innerText;
    navigator.clipboard.writeText(scriptContent).then(() => {
        const status = document.getElementById('copyStatus');
        const btnText = document.getElementById('copyBtnText');
        const originalText = btnText.innerText;
        
        status.classList.remove('hidden');
        btnText.innerText = 'Copied!';
        
        setTimeout(() => {
            status.classList.add('hidden');
            btnText.innerText = originalText;
        }, 2000);
    }).catch(err => {
        alert('Gagal menyalin script. Silakan copy manual.');
    });
}

function downloadScript(format = 'rsc') {
    const scriptContent = document.getElementById('scriptContent').innerText;
    const filename = scriptFilename + '.' + format;
    
    const blob = new Blob([scriptContent], { type: 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
    
    showNotification('File ' + filename + ' berhasil didownload!');
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300';
    notification.innerText = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('opacity-0');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<div class="fixed bottom-6 right-6 flex flex-col gap-2 z-40">
    <button onclick="downloadScript()" title="Download .rsc" class="p-3 bg-green-600 text-white rounded-full shadow-lg hover:bg-green-500 transition-all hover:scale-110">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
    </button>
    <button onclick="copyScript()" title="Copy Script" class="p-3 bg-primary-600 text-white rounded-full shadow-lg hover:bg-primary-500 transition-all hover:scale-110">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
        </svg>
    </button>
</div>
@endsection
