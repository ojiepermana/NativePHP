<!-- macOS window controls -->
<div class="flex items-center gap-2 select-none">
    <!-- Close button (red) -->
    <button onclick="closeApp()"
            class="w-3.5 h-3.5 rounded-full bg-[#ff5f56] ring-1 ring-black/10 dark:ring-white/10 hover:bg-[#ff4136] transition-colors cursor-pointer flex items-center justify-center group"
            title="Close">
        <span class="opacity-0 group-hover:opacity-100 text-black text-xs font-bold transition-opacity">×</span>
    </button>

    <!-- Minimize button (yellow) -->
    <button onclick="minimizeApp()"
            class="w-3.5 h-3.5 rounded-full bg-[#ffbd2e] ring-1 ring-black/10 dark:ring-white/10 hover:bg-[#ffb000] transition-colors cursor-pointer flex items-center justify-center group"
            title="Minimize">
        <span class="opacity-0 group-hover:opacity-100 text-black text-xs font-bold transition-opacity">−</span>
    </button>

    <!-- Maximize/Restore button (green) -->
    <button onclick="maximizeApp()"
            class="w-3.5 h-3.5 rounded-full bg-[#27c93f] ring-1 ring-black/10 dark:ring-white/10 hover:bg-[#00d515] transition-colors cursor-pointer flex items-center justify-center group"
            title="Maximize">
        <span class="opacity-0 group-hover:opacity-100 text-black text-xs font-bold transition-opacity">+</span>
    </button>
</div>

<script>
    // NativePHP Window Control Functions
    function closeApp() {
        fetch('/native/close', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => console.log('Close requested:', data))
            .catch(error => console.error('Error:', error));
    }

    function minimizeApp() {
        fetch('/native/minimize', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => console.log('Minimize requested:', data))
            .catch(error => console.error('Error:', error));
    }

    function maximizeApp() {
        fetch('/native/maximize', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => console.log('Maximize requested:', data))
            .catch(error => console.error('Error:', error));
    }

    // Alternative direct approach using window methods if available
    document.addEventListener('DOMContentLoaded', function() {
        // Try different possible API methods
        if (typeof window !== 'undefined') {
            // Check for electron APIs
            if (window.require) {
                const { remote } = window.require('electron');
                if (remote) {
                    window.closeApp = () => remote.getCurrentWindow().close();
                    window.minimizeApp = () => remote.getCurrentWindow().minimize();
                    window.maximizeApp = () => {
                        const win = remote.getCurrentWindow();
                        if (win.isMaximized()) {
                            win.unmaximize();
                        } else {
                            win.maximize();
                        }
                    };
                }
            }

            // Check for NativePHP specific APIs
            if (window.nativephp) {
                window.closeApp = () => window.nativephp.closeWindow();
                window.minimizeApp = () => window.nativephp.minimizeWindow();
                window.maximizeApp = () => window.nativephp.maximizeWindow();
            }
        }
    });
</script>
