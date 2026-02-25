@extends('layouts.app')

@section('title', 'Import Excel - Talent Mapping')

@section('content')
<div class="row justify-content-center animate-in">
    <div class="col-md-8">
        <div class="card-custom p-4">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('pegawai.index') }}" class="btn btn-outline-custom btn-sm me-3">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-excel me-2"></i>Import dari Excel</h5>
                    <small class="text-muted">Upload file Excel (.xlsx, .xls, .csv) untuk import data massal</small>
                </div>
            </div>

            <form action="{{ route('pegawai.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf

                <div class="upload-area mb-3" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                    <i class="bi bi-cloud-arrow-up d-block mb-2"></i>
                    <p class="fw-semibold mb-1">Klik atau drag & drop file di sini</p>
                    <small class="text-muted">Format: .xlsx, .xls, .csv (maks. 10MB)</small>
                    <input type="file" class="d-none @error('file') is-invalid @enderror"
                           id="fileInput" name="file" accept=".xlsx,.xls,.csv">
                    <div id="fileName" class="mt-2 fw-bold text-primary" style="display:none;"></div>
                </div>
                @error('file')
                    <div class="text-danger small mb-2">{{ $message }}</div>
                @enderror

                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-primary-custom" id="importBtn" disabled>
                        <i class="bi bi-upload me-1"></i>Import Data
                    </button>
                    <a href="{{ route('pegawai.template') }}" class="btn btn-outline-custom">
                        <i class="bi bi-download me-1"></i>Download Template
                    </a>
                    <a href="{{ route('pegawai.index') }}" class="btn btn-outline-custom">Batal</a>
                </div>
            </form>

            {{-- Format Info --}}
            <div class="border-top pt-3">
                <h6 class="fw-bold mb-2"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Format File yang Didukung</h6>
                <p class="text-muted small mb-2">File Excel harus memiliki kolom-kolom berikut (header di baris pertama):</p>
                <div class="table-responsive">
                    <table class="table table-sm ref-table border">
                        <thead><tr class="table-light"><th>Kolom</th><th>Wajib</th><th>Nilai yang Valid</th></tr></thead>
                        <tbody>
                            <tr>
                                <td><code>NIP</code></td>
                                <td><span class="badge bg-danger">Wajib</span></td>
                                <td>Nomor Induk Pegawai</td>
                            </tr>
                            <tr>
                                <td><code>Nama</code></td>
                                <td><span class="badge bg-secondary">Opsional</span></td>
                                <td>Nama pegawai</td>
                            </tr>
                            <tr>
                                <td><code>UE1</code></td>
                                <td><span class="badge bg-danger">Wajib</span></td>
                                <td>Angka <code>1</code> s/d <code>14</code>, atau nama unit, atau singkatan (DJP, DJBC, dll)</td>
                            </tr>
                            <tr>
                                <td><code>Kategori Kinerja</code></td>
                                <td><span class="badge bg-danger">Wajib</span></td>
                                <td><code>Di atas ekspektasi</code>, <code>Sesuai ekspektasi</code>, <code>Di bawah ekspektasi</code></td>
                            </tr>
                            <tr>
                                <td><code>Kategori Potensial</code></td>
                                <td><span class="badge bg-danger">Wajib</span></td>
                                <td><code>potensial rendah</code>, <code>potensial menengah</code>, <code>potensial tinggi</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info alert-custom mt-2 py-2">
                    <i class="bi bi-lightbulb me-1"></i>
                    <strong>Tips:</strong> Jika NIP sudah ada, data akan diperbarui. UE1 bisa diisi angka (1-14), singkatan (DJP, DJBC), atau nama lengkap unit.
                </div>
            </div>
        </div>

        {{-- UE1 Reference --}}
        <div class="card-custom p-3 mt-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-building me-1"></i>Kode Unit Eselon 1 (UE1)</h6>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm ref-table mb-0">
                        <thead><tr class="table-light"><th>Kode</th><th>Singkatan</th><th>Nama Unit</th></tr></thead>
                        <tbody>
                            @foreach(array_slice(\App\Models\Pegawai::UE1_LIST, 0, 7, true) as $code => $name)
                            <tr>
                                <td class="fw-bold">{{ $code }}</td>
                                <td><span class="badge-ue1">{{ \App\Models\Pegawai::UE1_SHORT[$code] }}</span></td>
                                <td style="font-size:0.75rem;">{{ $name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm ref-table mb-0">
                        <thead><tr class="table-light"><th>Kode</th><th>Singkatan</th><th>Nama Unit</th></tr></thead>
                        <tbody>
                            @foreach(array_slice(\App\Models\Pegawai::UE1_LIST, 7, 7, true) as $code => $name)
                            <tr>
                                <td class="fw-bold">{{ $code }}</td>
                                <td><span class="badge-ue1">{{ \App\Models\Pegawai::UE1_SHORT[$code] }}</span></td>
                                <td style="font-size:0.75rem;">{{ $name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');
    const importBtn = document.getElementById('importBtn');

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
            fileName.style.display = 'block';
            importBtn.disabled = false;
            uploadArea.style.borderColor = '#38a169';
            uploadArea.style.background = '#f0fff4';
        }
    });

    uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.classList.add('dragover'); });
    uploadArea.addEventListener('dragleave', () => { uploadArea.classList.remove('dragover'); });
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            fileName.textContent = e.dataTransfer.files[0].name;
            fileName.style.display = 'block';
            importBtn.disabled = false;
            uploadArea.style.borderColor = '#38a169';
            uploadArea.style.background = '#f0fff4';
        }
    });

    document.getElementById('importForm').addEventListener('submit', function() {
        importBtn.disabled = true;
        importBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Mengimport...';
    });
</script>
@endpush
