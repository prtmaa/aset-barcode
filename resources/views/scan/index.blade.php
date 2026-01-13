@extends('layouts.master')

@section('title', 'Scan QR Aset')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">

                @if (session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card card-primary card-outline">
                    <div class="card-header text-center">
                        <h3 class="card-title w-100">
                            <i class="fas fa-qrcode"></i> Scan QR Aset
                        </h3>
                    </div>

                    <div class="card-body">

                        {{-- PILIH MODE --}}
                        <div class="btn-group btn-block mb-3">
                            <button id="btn-camera" class="btn btn-primary active">
                                <i class="fas fa-camera"></i> Kamera
                            </button>
                            <button id="btn-upload" class="btn btn-outline-primary">
                                <i class="fas fa-upload"></i> Upload Gambar
                            </button>
                        </div>

                        {{-- MODE KAMERA --}}
                        <div id="mode-camera">
                            <p class="text-muted text-center">
                                Arahkan kamera ke QR Code aset
                            </p>

                            <div id="reader" class="border rounded mb-3" style="width:100%; min-height:280px;"></div>
                        </div>

                        {{-- MODE UPLOAD --}}
                        <div id="mode-upload" class="d-none text-center">
                            <p class="text-muted">
                                Upload gambar QR Code aset
                            </p>

                            <label class="btn btn-info">
                                <i class="fas fa-image"></i> Pilih Gambar
                                <input type="file" id="qr-image" hidden accept="image/*">
                            </label>

                            <div id="upload-status" class="mt-3 text-muted"></div>
                        </div>

                        <div id="scan-status" class="alert alert-info text-center mt-3">
                            <i class="fas fa-info-circle"></i> Menunggu QR Code...
                        </div>

                        <form id="scan-form" action="{{ route('scan.process') }}" method="POST">
                            @csrf
                            <input type="hidden" name="kode_aset" id="kode_aset">
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        // ELEMENT

        const btnCamera = document.getElementById('btn-camera');
        const btnUpload = document.getElementById('btn-upload');
        const modeCamera = document.getElementById('mode-camera');
        const modeUpload = document.getElementById('mode-upload');
        const statusBox = document.getElementById('scan-status');
        const kodeInput = document.getElementById('kode_aset');
        const form = document.getElementById('scan-form');
        const fileInput = document.getElementById('qr-image');

        const html5QrCode = new Html5Qrcode("reader");
        let cameraRunning = false;

        // HELPER
        function showInfo(msg) {
            statusBox.className = 'alert alert-info text-center';
            statusBox.innerHTML = '<i class="fas fa-info-circle"></i> ' + msg;
        }

        function showError(msg) {
            statusBox.className = 'alert alert-danger text-center';
            statusBox.innerHTML = '<i class="fas fa-times-circle"></i> ' + msg;
            navigator.vibrate?.(300);
        }

        function showSuccess(msg) {
            statusBox.className = 'alert alert-success text-center';
            statusBox.innerHTML = '<i class="fas fa-check-circle"></i> ' + msg;
            navigator.vibrate?.(200);
        }

        // MODE SWITCH

        btnCamera.onclick = () => {
            btnCamera.classList.add('active');
            btnUpload.classList.remove('active');

            modeCamera.classList.remove('d-none');
            modeUpload.classList.add('d-none');

            showInfo('Arahkan kamera ke QR Code aset');
            startCamera();
        };

        btnUpload.onclick = () => {
            btnUpload.classList.add('active');
            btnCamera.classList.remove('active');

            modeUpload.classList.remove('d-none');
            modeCamera.classList.add('d-none');

            stopCamera();
            showInfo('Silakan upload gambar QR Code');
        };

        // CAMERA SCAN
        function startCamera() {
            if (cameraRunning) return;

            Html5Qrcode.getCameras().then(devices => {
                if (!devices.length) {
                    showError('Kamera tidak ditemukan');
                    return;
                }

                // PRIORITAS KAMERA BELAKANG
                let cameraId = null;

                // Coba cari kamera belakang (mobile)
                const backCamera = devices.find(device =>
                    device.label.toLowerCase().includes('back') ||
                    device.label.toLowerCase().includes('rear')
                );

                if (backCamera) {
                    cameraId = backCamera.id;
                } else {
                    // fallback: kamera pertama
                    cameraId = devices[0].id;
                }

                html5QrCode.start(
                    cameraId, {
                        fps: 10,
                        qrbox: {
                            width: 220,
                            height: 220
                        },
                        aspectRatio: 1.0
                    },
                    onScanSuccess
                ).then(() => {
                    cameraRunning = true;
                }).catch(err => {
                    console.error(err);
                    showError('Gagal mengakses kamera');
                });
            });
        }


        function stopCamera() {
            if (!cameraRunning) return;

            html5QrCode.stop().then(() => {
                cameraRunning = false;
            }).catch(() => {});
        }

        function onScanSuccess(decodedText) {
            handleResult(decodedText);
        }

        // UPLOAD IMAGE SCAN

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            html5QrCode.scanFile(file, true)
                .then(decodedText => {
                    handleResult(decodedText);
                })
                .catch(() => {
                    showError('QR Code tidak terbaca dari gambar');
                });
        });

        // FINAL HANDLE RESULT

        function handleResult(text) {

            text = text.trim();

            // QR URL
            if (text.startsWith('http')) {
                text = text.split('/').pop();
            }

            showSuccess('QR Code valid, memproses data...');

            kodeInput.value = text;
            stopCamera();

            setTimeout(() => {
                form.submit();
            }, 700);
        }

        // INIT

        showInfo('Arahkan kamera ke QR Code aset');
        startCamera();
    </script>
    <script>
        setTimeout(() => {
            $('.alert-auto-hide').slideUp(400, function() {
                $(this).remove();
            });
        }, 3000);
    </script>
@endpush
