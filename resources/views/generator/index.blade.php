<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Laporan Harian</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Add your CSS links here --}}
    <style>
        /* Basic styling - adjust as needed */
        body { font-family: sans-serif; line-height: 1.6; padding: 20px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; max-width: 1200px; margin: auto;}
        .config-section, .preview-section { padding: 20px; border: 1px solid #eee; border-radius: 5px; background-color: #f9f9f9;}
        h1, h2 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input[type="date"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; margin-bottom: 10px; }
        #preview-area { white-space: pre-wrap; word-wrap: break-word; background-color: #fff; border: 1px solid #ddd; padding: 15px; min-height: 200px; font-family: monospace; font-size: 1em; margin-top: 15px; }
        .action-button { display: block; width: 100%; padding: 12px 15px; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 1.1em; margin-top: 10px; text-align: center;}
        .generate-button { background-color: #007bff; }
        .generate-button:hover { background-color: #0056b3; }
        .generate-button:disabled { background-color: #cccccc; cursor: not-allowed; }
        .copy-button { background-color: #17a2b8; }
        .copy-button:hover { background-color: #138496; }
        .copy-button:disabled { background-color: #cccccc; cursor: not-allowed; }
        .message { font-weight: bold; text-align: center; margin-top: 10px; padding: 10px; border-radius: 4px; }
        .message.success { color: green; background-color: #d4edda; }
        .message.error { color: red; background-color: #f8d7da; }
         @media (max-width: 768px) { body { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div x-data="reportGenerator()" x-init="init()">

        <div class="config-section">
            <h1>Generator Laporan</h1>

            {{-- Template Selector --}}
            <div class="form-group">
                <label for="template_select">Pilih Template:</label>
                <select id="template_select" x-model="selectedTemplateId">
                    <option value="">-- Pilih Template --</option>
                    <template x-for="template in templates" :key="template.id">
                        <option :value="template.id" x-text="template.name"></option>
                    </template>
                </select>
            </div>

            {{-- Context Input (Date) --}}
            <div class="form-group">
                <label for="report_date">Tanggal Laporan:</label>
                <input type="date" id="report_date" x-model="reportDate">
            </div>

            {{-- Generate Button --}}
            <button @click="generateReport"
                    class="action-button generate-button"
                    :disabled="!selectedTemplateId || !reportDate || isLoading">
                <span x-show="!isLoading">Generate Laporan</span>
                <span x-show="isLoading">Memuat...</span>
            </button>

             {{-- Loading/Error Message Area --}}
            <div x-show="message"
                 :class="isError ? 'message error' : 'message success'"
                 x-text="message"
                 x-transition>
            </div>
        </div>

        <div class="preview-section">
            <h2>Preview Laporan</h2>
            <pre id="preview-area" x-text="generatedReportText"></pre>

            <button @click="copyToClipboard"
                    class="action-button copy-button"
                    :disabled="!generatedReportText.trim() || isLoading">
                Salin Laporan ke Clipboard
            </button>
             <div x-show="copied" class="message success" x-transition>Berhasil disalin!</div>
        </div>

    </div> {{-- End Alpine.js Scope --}}



    <script>
        function reportGenerator() {
            return {
                 // --- State ---
                 templates: @json($templates), // From Laravel
                selectedTemplateId: '',
                reportDate: new Date().toISOString().slice(0, 10), // Default to today YYYY-MM-DD
                generatedReportText: '',
                isLoading: false,
                message: '', // For success/error messages during generation
                isError: false,
                copied: false, // For copy success message

                // --- Initialization ---
                init() {
                    // Load templates passed from Laravel controller
                    this.templates = @json($templates);
                },

                // --- Methods ---
                async generateReport() {
                    if (!this.selectedTemplateId || !this.reportDate) {
                        this.message = 'Silakan pilih template dan tanggal laporan.';
                        this.isError = true;
                        return;
                    }

                    this.isLoading = true;
                    this.message = ''; // Clear previous messages
                    this.isError = false;
                    this.generatedReportText = ''; // Clear previous report

                    try {
                        const response = await fetch('{{ route("generator.generate") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}' // Get CSRF token
                            },
                            body: JSON.stringify({
                                template_id: this.selectedTemplateId,
                                report_date: this.reportDate
                            })
                        });

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Gagal mengambil data laporan.');
                        }

                        this.generatedReportText = result.generated_report;
                        // Optional: Show temporary success message for generation
                        // this.message = 'Laporan berhasil dibuat!';
                        // this.isError = false;
                        // setTimeout(() => this.message = '', 3000);


                    } catch (error) {
                        console.error('Generation error:', error);
                        this.message = error.message || 'Terjadi kesalahan saat membuat laporan.';
                        this.isError = true;
                        this.generatedReportText = ''; // Clear preview on error
                    } finally {
                        this.isLoading = false;
                    }
                },

                // --- Methods ---
                updateTemplate() {
                    if (!this.selectedTemplateId) {
                        this.selectedTemplateContent = '';
                        this.placeholders = [];
                        this.inputs = {};
                        return;
                    }

                    // Find the full template object from the templates array
                    const selected = this.templates.find(t => t.id == this.selectedTemplateId); // Note: == might be needed for type coercion if ID is string
                    if (selected) {
                        this.selectedTemplateContent = selected.content;
                        this.extractPlaceholders();
                        this.resetInputs();
                    }
                },

                extractPlaceholders() {
                    const regex = /\{\{([a-zA-Z0-9_]+)\}\}/g;
                    // Use Set to automatically handle duplicates
                    const foundPlaceholders = new Set();
                    let match;
                    while ((match = regex.exec(this.selectedTemplateContent)) !== null) {
                        foundPlaceholders.add(match[1]); // Add the captured group (the name)
                    }
                    this.placeholders = Array.from(foundPlaceholders); // Convert Set back to Array
                },

                resetInputs() {
                    const newInputs = {};
                    this.placeholders.forEach(p => {
                        newInputs[p] = ''; // Initialize all inputs as empty strings
                    });
                    this.inputs = newInputs;
                },

                formatLabel(placeholder) {
                    // Simple formatter: replace underscores with spaces and capitalize words
                    return placeholder.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                },

                getInputType(placeholder) {
                    // Basic heuristic for input types (can be expanded)
                    if (placeholder.includes('jumlah') || placeholder.includes('total') || placeholder.includes('pasien') || placeholder.includes('kamar')) {
                        return 'number';
                    }
                    if (placeholder.includes('tanggal') || placeholder.includes('date')) {
                        return 'date';
                    }
                    return 'text'; // Default to text
                },

                copyToClipboard() {
                    if (!this.generatedReportText.trim()) return;

                    navigator.clipboard.writeText(this.generatedReportText)
                        .then(() => {
                            this.copied = true;
                            setTimeout(() => { this.copied = false; }, 2000);
                        })
                        .catch(err => {
                            console.error('Failed to copy text: ', err);
                            this.message = 'Gagal menyalin teks.'; // Show copy error
                            this.isError = true;
                        });
                }
            }
        }
    </script>

</body>
</html>