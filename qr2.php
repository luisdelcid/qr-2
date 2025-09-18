<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
        <title>Hello, world!</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- html5-qrcode -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js" integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <style>
            #html5-qrcode, #html5-qrcode video {
                width: 100%;
                height: 100%;
            }
             #html5-qrcode video {
                 object-fit: cover;
             }
            /*#status {
                display: block;
                position: absolute;
                top: 0;
                left: 0;
                z-index: 1;
                background: #343a40;
                color: #FFECEC;
                text-align: center;
                width: 100%;
            }*/
            /* Cambiar color del thumb a gris (secondary) y hacerlo más grande */
            .custom-range::-webkit-slider-thumb {
                background-color: #343a40;
                width: 2.375rem;
              height: 1.5rem;
              margin-top: -0.5rem; /* centrado sobre el track */
              border-radius: 2rem; /* hace que se vea ovalado */
            }

            .custom-range::-webkit-slider-thumb:active {
              background-color: #7b7f83; /* un tono más oscuro al presionar */
            }

            .custom-range::-moz-range-thumb {
              background-color: #343a40;
              width: 2.375rem;
              height: 1.5rem;
              border-radius: 2rem;
            }

            .custom-range::-moz-range-thumb:active {
              background-color: #7b7f83;
            }

            .custom-range::-ms-thumb {
              background-color: #343a40;
              width: 2.375rem;
              height: 1.5rem;
              border-radius: 2rem;
              margin-top: 0; /* en IE/Edge no se necesita ajuste extra */
            }

            .custom-range::-ms-thumb:active {
              background-color: #7b7f83;
            }

        </style>
    </head>
    <body>
        <div class="container py-3">
            <div class="row">
                <div class="col-md-4 offset-md-4">

                    <div class="embed-responsive embed-responsive-1by1 bg-dark" style="border-radius: 36px;">
                        <div class="embed-responsive-item">
                            <div id="html5-qrcode" class="h-100 w-100"></div>
                            <!--<div id="status" class="text-truncate"></div>-->
                        </div>
                    </div>

                    <div class="mt-3 p-3 border bg-light rounded-pill d-flex justify-content-between align-items-center flex-row-reverse">
                        <button id="play" type="button" class="btn btn-dark px-2 rounded-pill" title="Play"><i class="fa-solid fa-play fa-fw"></i></button>
                        <button id="pause" type="button" class="btn btn-dark px-2 rounded-pill d-none" title="Pause"><i class="fa-solid fa-pause fa-fw"></i></button>
                        <div id="status" class="text-center text-truncate flex-grow-1 small text-muted mx-3" style="line-height: 1;"></div>
                        <button id="stop" type="button" class="btn btn-dark px-2 rounded-pill" title="Stop"><i class="fa-solid fa-stop fa-fw"></i></button>
                    </div>

                    <div id="zoom-in-out">
                        <div class="mt-3 p-3 border bg-light rounded-pill d-flex justify-content-between align-items-center">
                            <button id="torch" type="button" class="btn btn-dark px-2 rounded-pill" title="Torch"><i class="fa-solid fa-bolt-lightning fa-fw"></i></button>
                            <div class="mx-3 flex-grow-1 d-flex justify-content-between align-items-center">
                                <div id="zoom-min" class="small text-muted mr-3 text-nowrap" style="line-height: 1;">x</div>
                                <input id="zoom-range" type="range" class="custom-range flex-grow-1" />
                                <div id="zoom-max" class="small text-muted ml-3 text-nowrap" style="line-height: 1;">x</div>
                            </div>
                            <button id="zoom" type="button" class="btn btn-dark px-2 rounded-pill" title="Reset Zoom"><i class="fa-solid fa-magnifying-glass fa-fw"></i></button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- jQuery (slim) + Bootstrap bundle -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

        <script>
        (function() {
            // UI elements
            const el = (id) => document.getElementById(id);
            //const $zoomWrap = el('zoom-in-out');
            const $zoomRange = el('zoom-range');
            const $zoomMin = el('zoom-min');
            const $zoomMax = el('zoom-max');
            const $btnTorch = el('torch');
            const $btnPlay = el('play');
            const $btnPause = el('pause');
            const $btnStop = el('stop');
            const $btnZoomReset = el('zoom');
            const $status = el('status');

            // Scanner
            let html5QrCode = new Html5Qrcode("html5-qrcode");
            let state = 'stopped'; // 'stopped' | 'running' | 'paused'
            let hasZoom = false;
            let hasTorch = false;
            let torchOn = false;
            let zoomDefault = 1;
            let zoomMin = 1;
            let zoomMax = 1;
            let zoomStep = 0.1;
            let currentZoom = 1;
            let statusMessage = '';

            function setStatus(msg, persist = true) {
                if (persist) statusMessage = msg || '';
                $status.textContent = msg || '';
                //$status.style.display = state === 'paused' ? 'none' : 'block';
            }

            function updateButtons() {
                // Zoom controls
                const showZoom = hasZoom && state !== 'stopped';
                // Bootstrap display utilities: remove `d-flex` when hiding and
                // add it back when showing to avoid conflicts with `d-none`.

                //$zoomWrap.classList.toggle('d-block', showZoom);
                //$zoomWrap.classList.toggle('d-none', !showZoom);

                //$btnZoomReset.classList.toggle('d-none', !showZoom);
                // Disable zoom slider when paused or zoom unsupported
                $zoomRange.disabled = !hasZoom || state !== 'running';
                // Enable reset only when zoom differs from default
                $btnZoomReset.disabled = !hasZoom || state !== 'running' || currentZoom === zoomDefault;

                $btnStop.disabled = state === 'stopped';
                //$btnStop.classList.toggle('d-block', state !== 'stopped');

                // Torch
                $btnTorch.disabled = !hasTorch || state === 'stopped';
                //$btnTorch.classList.toggle('d-none', !hasTorch || state === 'stopped');

                // Play / Pause
                if (state === 'stopped') {
                    $btnPlay.classList.remove('d-none');
                    $btnPause.classList.add('d-none');
                } else if (state === 'running') {
                    $btnPlay.classList.add('d-none');
                    $btnPause.classList.remove('d-none');
                } else if (state === 'paused') {
                    $btnPlay.classList.remove('d-none');
                    $btnPause.classList.add('d-none');
                }
            }

            async function readCapabilitiesAndSetupUI() {
                try {
                    // These methods exist in recent versions of html5-qrcode
                    const caps = html5QrCode.getRunningTrackCapabilities
                        ? html5QrCode.getRunningTrackCapabilities()
                        : null;

                    const settings = html5QrCode.getRunningTrackSettings
                        ? html5QrCode.getRunningTrackSettings()
                        : null;

                    // Zoom
                    if (caps && typeof caps.zoom !== 'undefined') {
                        hasZoom = true;
                        // zoom can be a number or object (min/max/step). Handle both.
                        if (typeof caps.zoom === 'object') {
                            zoomMin = caps.zoom.min ?? 1;
                            zoomMax = caps.zoom.max ?? 1;
                            zoomStep = caps.zoom.step ?? 0.1;
                        } else {
                            // Some devices expose a simple numeric range, use defaults
                            zoomMin = 1;
                            zoomMax = caps.zoom || 1;
                            zoomStep = 0.1;
                        }
                        // Default/current
                        zoomDefault = (settings && settings.zoom) ? settings.zoom : 1;

                        // Init UI
                        $zoomRange.min = zoomMin;
                        $zoomRange.max = zoomMax;
                        $zoomRange.step = zoomStep;
                        $zoomRange.value = (settings && settings.zoom) ? settings.zoom : zoomDefault;
                        currentZoom = (settings && settings.zoom) ? settings.zoom : zoomDefault;

                        // Labels x multiplier (rounded to 2 decimals)
                        $zoomMin.textContent = (Math.round(zoomMin * 100) / 100) + 'x';
                        $zoomMax.textContent = (Math.round(zoomMax * 100) / 100) + 'x';
                    } else {
                        hasZoom = false;
                        currentZoom = zoomDefault;
                    }

                    // Torch
                    hasTorch = !!(caps && caps.torch);

                } catch (e) {
                    // If capabilities are not available, hide advanced controls
                    hasZoom = false;
                    hasTorch = false;
                } finally {
                    updateButtons();
                }
            }

            async function applyZoom(value) {
                if (!hasZoom) return;
                const v = Math.min(Math.max(Number(value), zoomMin), zoomMax);
                try {
                    await html5QrCode.applyVideoConstraints({ advanced: [{ zoom: v }] });
                    // keep slider synced
                    $zoomRange.value = v;
                    currentZoom = v;
                } catch (err) {
                    console.warn('Zoom not applied:', err);
                } finally {
                    updateButtons();
                }
            }

            async function setTorch(on) {
                if (!hasTorch) return;
                try {
                    await html5QrCode.applyVideoConstraints({ advanced: [{ torch: !!on }] });
                    torchOn = !!on;
                    $btnTorch.classList.toggle('btn-dark', !torchOn);
                    $btnTorch.classList.toggle('btn-warning', torchOn);
                } catch (err) {
                    console.warn('Torch not applied:', err);
                }
            }

            async function startScanner() {
                if (state !== 'stopped') return;

                setStatus('Iniciando cámara');
                // Prefer rear camera. Try strict environment; if it fails, use non-strict.
                const squareCameraConstraints = {
                    width: { ideal: 720 },
                    height: { ideal: 720 },
                    aspectRatio: 1
                };
                const cameraConfigStrict = { facingMode: { exact: "environment" } };
                const cameraConfigFallback = { facingMode: "environment" };

                const config = {
                    fps: 30,
                    aspectRatio: 1,
                    // Asegura un área de escaneo cuadrada para ocupar todo el contenedor
                    qrbox: (viewfinderWidth, viewfinderHeight) => {
                        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                        return { width: (minEdge / 3) * 2, height: (minEdge / 3) * 2 };
                    },

                    rememberLastUsedCamera: false,
                    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                    //videoConstraints: squareCameraConstraints

                    //qrbox: { width: 260, height: 260 },

                };

                const onScanSuccess = (decodedText, decodedResult) => {
                    // Aquí puedes manejar el resultado del QR
                    // console.log('QR:', decodedText, decodedResult);
                    setStatus('QR detectado: ' + decodedText);
                };
                const onScanFailure = (error) => {
                    // Errores de frames; suele ser ruidoso. Mantener en silencio o log suave.
                };

                try {
                    let started = false;
                    /*try {
                        const devices = await Html5Qrcode.getCameras();
                        const backCamera = devices.find((d) => /back|rear|environment|world|trase|poste/i.test(d.label))
                            || (devices.length > 1 ? devices[devices.length - 1] : null);
                        if (backCamera) {
                            await html5QrCode.start(
                                { deviceId: { exact: backCamera.id } },
                                config,
                                onScanSuccess,
                                onScanFailure
                            );
                            started = true;
                        }
                    } catch (_) {
                        // ignore and fallback
                    }*/
                    if (!started) {
                        try {
                            await html5QrCode.start(cameraConfigStrict, config, onScanSuccess, onScanFailure);
                        } catch (_) {
                            // If strict constraints fail, the html5-qrcode instance can remain
                            // in a transitional state. Recreate it before retrying to avoid
                            // "Cannot transition to a new state" errors.
                            try {
                                await html5QrCode.clear();
                            } catch (_) {
                                /* ignore */
                            }
                            html5QrCode = new Html5Qrcode("html5-qrcode");
                            await html5QrCode.start(cameraConfigFallback, config, onScanSuccess, onScanFailure);
                        }
                    }
                    state = 'running';
                    setStatus('Scanner iniciado');
                    await readCapabilitiesAndSetupUI();
                } catch (err) {
                    setStatus('No se pudo iniciar la cámara: ' + (err && err.message ? err.message : err));
                    state = 'stopped';
                } finally {
                    updateButtons();
                }
            }

            async function pauseScanner() {
                if (state !== 'running') return;
                try {
                    await html5QrCode.pause(true); // pause video feed
                    state = 'paused';
                    setStatus('Scanner en pausa');
                } catch (err) {
                    setStatus('No se pudo pausar: ' + err);
                } finally {
                    updateButtons();
                }
            }

            async function resumeScanner() {
                if (state !== 'paused') return;
                try {
                    await html5QrCode.resume();
                    state = 'running';
                    setStatus('Scanner reanudado');
                } catch (err) {
                    setStatus('No se pudo reanudar: ' + err);
                } finally {
                    updateButtons();
                }
            }

            async function stopScanner() {
                if (state === 'stopped') return;
                setStatus('Deteniendo');
                try {
                    await html5QrCode.stop();
                    await html5QrCode.clear();
                } catch (err) {
                    // noop
                } finally {
                    state = 'stopped';
                    // Reset zoom UI to default placeholders
                    hasZoom = false;
                    zoomDefault = 1;
                    zoomMin = 1;
                    zoomMax = 1;
                    zoomStep = 0.1;
                    currentZoom = zoomDefault;
                    $zoomRange.value = 1;
                    $zoomRange.min = 1;
                    $zoomRange.max = 1;
                    $zoomRange.step = 0.1;
                    $zoomMin.textContent = 'x';
                    $zoomMax.textContent = 'x';
                    // Reset torch state when no camera is active
                    hasTorch = false;
                    torchOn = false;
                    $btnTorch.classList.add('btn-dark');
                    $btnTorch.classList.remove('btn-warning');
                    setStatus('Scanner detenido');
                    updateButtons();
                }
            }

            // Event handlers
            $btnPlay.addEventListener('click', () => {
                if (state === 'stopped') startScanner();
                else if (state === 'paused') resumeScanner();
            });

            $btnPause.addEventListener('click', () => {
                if (state === 'running') pauseScanner();
            });

            $btnStop.addEventListener('click', () => {
                if (state === 'running') stopScanner();
                else if (state === 'paused') stopScanner();
            });

            $btnTorch.addEventListener('click', () => {
                if (!hasTorch || state === 'stopped') return;
                setTorch(!torchOn);
            });

            $btnZoomReset.addEventListener('click', () => {
                if (!hasZoom || state === 'stopped') return;
                applyZoom(zoomDefault || 1);
            });

            $zoomRange.addEventListener('input', (e) => {
                if (!hasZoom || state === 'stopped') return;
                const val = Math.round(e.target.value * 100) / 100;
                //setStatus('Zoom: ' + val + 'x', false);
                setStatus(val + 'x', false);
                applyZoom(e.target.value);
            });

            $zoomRange.addEventListener('change', () => {
                setStatus(statusMessage);
            });

            // Estado inicial
            updateButtons();
            //setStatus('Listo. Presiona “Play” para iniciar el lector.');
            setStatus('Scanner listo');
        })();
        </script>
    </body>
</html>
