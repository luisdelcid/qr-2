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
            #html5-qrcode,
            #html5-qrcode video {
                width: 100%;
                height: 100%;
            }

            #html5-qrcode video {
                object-fit: cover;
            }

            .scanner-stage {
                position: relative;
                width: 100%;
                height: 100%;
                border-radius: inherit;
                overflow: hidden;
            }

            #tap-to-focus-overlay {
                position: absolute;
                inset: 0;
                z-index: 5;
                border-radius: inherit;
                pointer-events: none;
                touch-action: manipulation;
                background: transparent;
            }

            #tap-to-focus-overlay.focus-overlay--active {
                pointer-events: auto;
            }

            #tap-to-focus-overlay.focus-overlay--available {
                cursor: crosshair;
            }

            .focus-ring {
                position: absolute;
                left: 0;
                top: 0;
                display: block;
                width: 120px;
                height: 120px;
                border-radius: 50%;
                border: 1px solid currentColor;
                color: #f8d75c;
                pointer-events: none;
                transform: translate(-50%, -50%) scale(0.85);
                opacity: 0;
                transition: opacity 0.18s ease, transform 0.18s ease;
            }

            .focus-ring.focus-ring--visible {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }

            .focus-ring.focus-ring--success {
                color: #f5d64c;
            }

            .focus-ring.focus-ring--error {
                color: #ff6b6b;
            }

            .focus-ring.focus-ring--fade-out {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.9);
            }

            .focus-ring__tick {
                position: absolute;
                background: currentColor;
                opacity: 0.9;
            }

            .focus-ring__tick--top,
            .focus-ring__tick--bottom {
                width: 22%;
                height: 1px;
                left: 50%;
                transform: translateX(-50%);
            }

            .focus-ring__tick--top {
                top: 16%;
            }

            .focus-ring__tick--bottom {
                bottom: 16%;
            }

            .focus-ring__tick--left,
            .focus-ring__tick--right {
                width: 1px;
                height: 22%;
                top: 50%;
                transform: translateY(-50%);
            }

            .focus-ring__tick--left {
                left: 16%;
            }

            .focus-ring__tick--right {
                right: 16%;
            }

            .custom-range::-webkit-slider-thumb {
                background-color: #343a40;
                width: 2.375rem;
                height: 1.5rem;
                margin-top: -0.5rem;
                border-radius: 2rem;
            }

            .custom-range::-webkit-slider-thumb:active {
                background-color: #7b7f83;
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
                margin-top: 0;
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
                        <div class="embed-responsive-item scanner-stage">
                            <div id="html5-qrcode" class="h-100 w-100"></div>
                            <div id="tap-to-focus-overlay"></div>
                        </div>
                    </div>

                    <div class="mt-3 p-3 border bg-light rounded-pill d-flex justify-content-between align-items-center flex-row-reverse">
                        <button id="play" type="button" class="btn btn-dark px-2 rounded-pill" title="Play"><i class="fa-solid fa-play fa-fw"></i></button>
                        <button id="pause" type="button" class="btn btn-dark px-2 rounded-pill d-none" title="Pause"><i class="fa-solid fa-pause fa-fw"></i></button>
                        <div id="status" class="text-center text-truncate flex-grow-1 small text-muted mx-3" style="line-height: 1;"></div>
                        <button id="stop" type="button" class="btn btn-dark px-2 rounded-pill" title="Stop"><i class="fa-solid fa-stop fa-fw"></i></button>
                    </div>

                    <div id="torch-zoom-in-out">
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
            const el = (id) => document.getElementById(id);
            const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

            const $zoomRange = el('zoom-range');
            const $zoomMin = el('zoom-min');
            const $zoomMax = el('zoom-max');
            const $btnTorch = el('torch');
            const $btnPlay = el('play');
            const $btnPause = el('pause');
            const $btnStop = el('stop');
            const $btnZoomReset = el('zoom');
            const $status = el('status');
            const $torchZoomWrap = el('torch-zoom-in-out');
            const $focusOverlay = el('tap-to-focus-overlay');

            const zoomRangeDefaults = {
                min: $zoomRange.getAttribute('min'),
                max: $zoomRange.getAttribute('max'),
                step: $zoomRange.getAttribute('step'),
                value: $zoomRange.value || '50'
            };

            let html5QrCode = new Html5Qrcode("html5-qrcode");
            let state = 'stopped';
            let hasZoom = false;
            let hasTorch = false;
            let torchOn = false;
            let zoomDefault = 1;
            let zoomMin = 1;
            let zoomMax = 1;
            let zoomStep = 0.1;
            let currentZoom = 1;
            let statusMessage = '';
            let tapToFocusSupported = false;

            const focusCapabilities = {
                hasPointsOfInterest: false,
                modes: []
            };
            let focusTapStart = null;
            let focusInProgress = false;
            let activeFocusRing = null;

            function setStatus(msg, persist = true) {
                if (persist) statusMessage = msg || '';
                $status.textContent = msg || '';
            }

            function resetZoomUI() {
                if (zoomRangeDefaults.min !== null) $zoomRange.setAttribute('min', zoomRangeDefaults.min);
                else $zoomRange.removeAttribute('min');
                if (zoomRangeDefaults.max !== null) $zoomRange.setAttribute('max', zoomRangeDefaults.max);
                else $zoomRange.removeAttribute('max');
                if (zoomRangeDefaults.step !== null) $zoomRange.setAttribute('step', zoomRangeDefaults.step);
                else $zoomRange.removeAttribute('step');
                $zoomRange.value = zoomRangeDefaults.value;
                $zoomMin.textContent = 'x';
                $zoomMax.textContent = 'x';
            }

            function clearFocusRing() {
                if (activeFocusRing && activeFocusRing.parentNode) {
                    activeFocusRing.parentNode.removeChild(activeFocusRing);
                }
                activeFocusRing = null;
            }

            function spawnFocusRing(clientX, clientY) {
                if (!$focusOverlay) return null;
                const overlayRect = $focusOverlay.getBoundingClientRect();
                if (!overlayRect.width || !overlayRect.height) return null;

                const minEdge = Math.min(overlayRect.width, overlayRect.height);
                const size = clamp(minEdge * 0.35, 72, 180);
                const half = size / 2;
                const localX = clamp(clientX - overlayRect.left, half, overlayRect.width - half);
                const localY = clamp(clientY - overlayRect.top, half, overlayRect.height - half);

                clearFocusRing();

                const ring = document.createElement('div');
                ring.className = 'focus-ring';
                ring.style.width = `${size}px`;
                ring.style.height = `${size}px`;
                ring.style.left = `${localX}px`;
                ring.style.top = `${localY}px`;

                ['top', 'right', 'bottom', 'left'].forEach((pos) => {
                    const tick = document.createElement('span');
                    tick.className = `focus-ring__tick focus-ring__tick--${pos}`;
                    ring.appendChild(tick);
                });

                $focusOverlay.appendChild(ring);
                requestAnimationFrame(() => ring.classList.add('focus-ring--visible'));
                activeFocusRing = ring;
                return ring;
            }

            function markFocusRing(ring, status) {
                if (!ring) return;
                ring.classList.add(status === 'success' ? 'focus-ring--success' : 'focus-ring--error');
                const fadeDelay = status === 'success' ? 700 : 500;
                setTimeout(() => ring.classList.add('focus-ring--fade-out'), fadeDelay);
                setTimeout(() => {
                    if (ring.parentNode) ring.parentNode.removeChild(ring);
                    if (ring === activeFocusRing) activeFocusRing = null;
                }, fadeDelay + 220);
            }

            function updateFocusOverlayState() {
                if (!$focusOverlay) return;
                const overlayActive = state === 'running';
                $focusOverlay.classList.toggle('focus-overlay--active', overlayActive);
                $focusOverlay.classList.toggle('focus-overlay--available', overlayActive && tapToFocusSupported);
                if (!overlayActive) clearFocusRing();
            }

            function updateControlsVisibility() {
                if (!$torchZoomWrap) return;
                const showWrap = state !== 'stopped' && (hasZoom || hasTorch);
                $torchZoomWrap.classList.toggle('d-none', !showWrap);
            }

            function updateButtons() {
                $zoomRange.disabled = !hasZoom || state !== 'running';
                $btnZoomReset.disabled = !hasZoom || state !== 'running' || currentZoom === zoomDefault;
                $btnStop.disabled = state === 'stopped';
                $btnTorch.disabled = !hasTorch || state === 'stopped';

                if (state === 'stopped') {
                    $btnPlay.classList.remove('d-none');
                    $btnPause.classList.add('d-none');
                } else if (state === 'running') {
                    $btnPlay.classList.add('d-none');
                    $btnPause.classList.remove('d-none');
                } else {
                    $btnPlay.classList.remove('d-none');
                    $btnPause.classList.add('d-none');
                }

                updateControlsVisibility();
                updateFocusOverlayState();
            }

            async function readCapabilitiesAndSetupUI() {
                try {
                    const caps = html5QrCode.getRunningTrackCapabilities
                        ? html5QrCode.getRunningTrackCapabilities()
                        : null;

                    const settings = html5QrCode.getRunningTrackSettings
                        ? html5QrCode.getRunningTrackSettings()
                        : null;

                    if (caps && typeof caps.zoom !== 'undefined') {
                        hasZoom = true;
                        if (typeof caps.zoom === 'object') {
                            zoomMin = caps.zoom.min ?? 1;
                            zoomMax = caps.zoom.max ?? 1;
                            zoomStep = caps.zoom.step ?? 0.1;
                        } else {
                            zoomMin = 1;
                            zoomMax = caps.zoom || 1;
                            zoomStep = 0.1;
                        }

                        zoomDefault = (settings && typeof settings.zoom === 'number') ? settings.zoom : 1;
                        currentZoom = (settings && typeof settings.zoom === 'number') ? settings.zoom : zoomDefault;

                        $zoomRange.min = zoomMin;
                        $zoomRange.max = zoomMax;
                        $zoomRange.step = zoomStep;
                        $zoomRange.value = currentZoom;

                        $zoomMin.textContent = `${Math.round(zoomMin * 100) / 100}x`;
                        $zoomMax.textContent = `${Math.round(zoomMax * 100) / 100}x`;
                    } else {
                        hasZoom = false;
                        zoomDefault = 1;
                        zoomMin = 1;
                        zoomMax = 1;
                        zoomStep = 0.1;
                        currentZoom = zoomDefault;
                        resetZoomUI();
                    }

                    hasTorch = !!(caps && caps.torch);
                    if (!hasTorch) {
                        torchOn = false;
                        $btnTorch.classList.add('btn-dark');
                        $btnTorch.classList.remove('btn-warning');
                    }

                    focusCapabilities.hasPointsOfInterest = !!(caps && caps.pointsOfInterest);
                    const focusModes = caps && caps.focusMode;
                    if (Array.isArray(focusModes)) {
                        focusCapabilities.modes = focusModes.slice();
                    } else if (typeof focusModes === 'string') {
                        focusCapabilities.modes = [focusModes];
                    } else {
                        focusCapabilities.modes = [];
                    }
                    tapToFocusSupported = focusCapabilities.hasPointsOfInterest || focusCapabilities.modes.length > 0;
                } catch (e) {
                    hasZoom = false;
                    hasTorch = false;
                    tapToFocusSupported = false;
                    focusCapabilities.hasPointsOfInterest = false;
                    focusCapabilities.modes = [];
                    resetZoomUI();
                } finally {
                    updateButtons();
                }
            }

            async function applyZoom(value) {
                if (!hasZoom) return;
                const v = clamp(Number(value), zoomMin, zoomMax);
                try {
                    await html5QrCode.applyVideoConstraints({ advanced: [{ zoom: v }] });
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

            async function tryFocusWithPoint(x, y) {
                const point = { x, y };
                const attempts = [];
                if (focusCapabilities.modes.includes('single-shot')) {
                    attempts.push({ focusMode: 'single-shot', pointsOfInterest: [point] });
                }
                if (focusCapabilities.modes.includes('manual')) {
                    attempts.push({ focusMode: 'manual', pointsOfInterest: [point] });
                }
                if (!attempts.length) {
                    attempts.push({ pointsOfInterest: [point] });
                }

                for (const constraint of attempts) {
                    try {
                        await html5QrCode.applyVideoConstraints({ advanced: [constraint] });
                        return true;
                    } catch (_) {
                        // continue
                    }
                }
                return false;
            }

            async function tryFocusByMode() {
                const order = ['single-shot', 'continuous', 'auto'];
                for (const mode of order) {
                    if (!focusCapabilities.modes.includes(mode)) continue;
                    try {
                        await html5QrCode.applyVideoConstraints({ advanced: [{ focusMode: mode }] });
                        return true;
                    } catch (_) {
                        // continue
                    }
                }
                return false;
            }

            async function handleTapToFocus(clientX, clientY) {
                if (focusInProgress) return;
                const ring = spawnFocusRing(clientX, clientY);
                if (!ring) return;

                if (!tapToFocusSupported) {
                    markFocusRing(ring, 'error');
                    return;
                }

                const video = document.querySelector('#html5-qrcode video');
                if (!video) {
                    markFocusRing(ring, 'error');
                    return;
                }

                const rect = video.getBoundingClientRect();
                if (!rect.width || !rect.height) {
                    markFocusRing(ring, 'error');
                    return;
                }

                const normX = clamp((clientX - rect.left) / rect.width, 0, 1);
                const normY = clamp((clientY - rect.top) / rect.height, 0, 1);

                focusInProgress = true;
                let success = false;

                try {
                    if (focusCapabilities.hasPointsOfInterest) {
                        success = await tryFocusWithPoint(normX, normY);
                    }
                    if (!success) {
                        success = await tryFocusByMode();
                    }
                } catch (err) {
                    console.warn('Tap-to-focus error:', err);
                } finally {
                    focusInProgress = false;
                }

                markFocusRing(ring, success ? 'success' : 'error');
            }

            function bindTapToFocusEvents() {
                if (!$focusOverlay) return;

                $focusOverlay.addEventListener('pointerdown', (e) => {
                    if (state !== 'running') return;
                    if (e.pointerType === 'mouse' && e.button !== 0) return;
                    focusTapStart = { x: e.clientX, y: e.clientY, t: performance.now() };
                });

                $focusOverlay.addEventListener('pointerup', (e) => {
                    if (state !== 'running' || !focusTapStart) return;
                    if (e.pointerType === 'mouse' && e.button !== 0) {
                        focusTapStart = null;
                        return;
                    }
                    const dt = performance.now() - focusTapStart.t;
                    const distance = Math.hypot(e.clientX - focusTapStart.x, e.clientY - focusTapStart.y);
                    focusTapStart = null;
                    if (dt > 280 || distance > 12) return;
                    handleTapToFocus(e.clientX, e.clientY).catch((err) => console.warn('Tap-to-focus failed:', err));
                });

                $focusOverlay.addEventListener('pointercancel', () => { focusTapStart = null; });
                $focusOverlay.addEventListener('pointerleave', () => { focusTapStart = null; });
            }

            async function startScanner() {
                if (state !== 'stopped') return;

                setStatus('Iniciando cámara');
                const cameraConfigStrict = { facingMode: { exact: 'environment' } };
                const cameraConfigFallback = { facingMode: 'environment' };

                const config = {
                    fps: 30,
                    aspectRatio: 1,
                    qrbox: (viewfinderWidth, viewfinderHeight) => {
                        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                        return { width: (minEdge / 3) * 2, height: (minEdge / 3) * 2 };
                    },
                    rememberLastUsedCamera: false,
                    supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
                };

                const onScanSuccess = (decodedText) => {
                    setStatus('QR detectado: ' + decodedText);
                };
                const onScanFailure = () => {};

                try {
                    try {
                        await html5QrCode.start(cameraConfigStrict, config, onScanSuccess, onScanFailure);
                    } catch (_) {
                        try {
                            await html5QrCode.clear();
                        } catch (_) {
                            // ignore
                        }
                        html5QrCode = new Html5Qrcode('html5-qrcode');
                        await html5QrCode.start(cameraConfigFallback, config, onScanSuccess, onScanFailure);
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
                    await html5QrCode.pause(true);
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
                    // ignore
                } finally {
                    state = 'stopped';
                    hasZoom = false;
                    hasTorch = false;
                    tapToFocusSupported = false;
                    focusCapabilities.hasPointsOfInterest = false;
                    focusCapabilities.modes = [];
                    zoomDefault = 1;
                    zoomMin = 1;
                    zoomMax = 1;
                    zoomStep = 0.1;
                    currentZoom = zoomDefault;
                    resetZoomUI();
                    torchOn = false;
                    $btnTorch.classList.add('btn-dark');
                    $btnTorch.classList.remove('btn-warning');
                    clearFocusRing();
                    setStatus('Scanner detenido');
                    updateButtons();
                }
            }

            $btnPlay.addEventListener('click', () => {
                if (state === 'stopped') startScanner();
                else if (state === 'paused') resumeScanner();
            });

            $btnPause.addEventListener('click', () => {
                if (state === 'running') pauseScanner();
            });

            $btnStop.addEventListener('click', () => {
                if (state !== 'stopped') stopScanner();
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
                if (!hasZoom || state !== 'running') return;
                const val = Math.round(e.target.value * 100) / 100;
                setStatus(val + 'x', false);
                applyZoom(e.target.value);
            });

            $zoomRange.addEventListener('change', () => {
                setStatus(statusMessage);
            });

            bindTapToFocusEvents();
            updateButtons();
            setStatus('Scanner listo');
        })();
        </script>
    </body>
</html>
