
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1, user-scalable=no" />
<title>Escáner QR — Arquitectura por Clases</title>

<!-- Librería Html5-QRCode -->
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
  :root { color-scheme: dark light; }
  * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }

  body { margin:0; font-family: system-ui, Segoe UI, Roboto, Helvetica, Arial, sans-serif; background:#0b1220; color:#e5e7eb; }
  header { padding:12px 16px; border-bottom:1px solid #1f2937; background:#0f172a; }
  main { padding:16px; display:grid; gap:12px; max-width:760px; margin:auto; }

  /* Contenedor cuadrado 1:1 del visor */
  .reader-wrap {
    position: relative; width: 100%; max-width: 520px; margin: auto;
    touch-action: pan-y; border-radius: 12px; overflow: hidden; background: #000;
    aspect-ratio: 1 / 1;
  }
  /* ¡Importante!: no fuerces layout interno del lib; sólo ajusta video/canvas a 100% */
  #reader, #reader video, #reader canvas {
    position: absolute; inset: 0; width: 100% !important; height: 100% !important;
  }
  #reader video { object-fit: cover; display:block; background:#000; }

  /* Overlay de enfoque + HUD */
  #focusOverlay { position:absolute; inset:0; pointer-events:none; overflow:hidden; z-index:3; }
  .focus-ring {
    position:absolute; width:40px; height:40px; border-radius:50%;
    border:2px solid rgba(125,211,252,.95); box-shadow:0 0 12px rgba(125,211,252,.55);
    transform: translateZ(0) scale(.7); opacity: 0;
  }
  .focus-ring.anim-in { animation: focus-pop 180ms cubic-bezier(.2,.8,.2,1) forwards; }
  @keyframes focus-pop { 0%{transform:scale(.7);opacity:0} 60%{transform:scale(1.08);opacity:1} 100%{transform:scale(1);opacity:1} }
  .focus-ring.ok {
    border-color:#10b981;
    box-shadow:0 0 14px rgba(16,185,129,.65), 0 0 36px rgba(16,185,129,.25);
    animation: focus-pop 180ms cubic-bezier(.2,.8,.2,1) forwards, focus-pulse 620ms ease-out 1 80ms;
  }
  @keyframes focus-pulse { 0%{transform:scale(1)} 50%{transform:scale(1.06)} 100%{transform:scale(1)} }
  .focus-ring.fail {
    border-color:#ef4444; box-shadow:0 0 14px rgba(239,68,68,.65);
    animation: focus-pop 160ms ease-out forwards, focus-shake .28s linear 1 60ms;
  }
  @keyframes focus-shake {
    0%{ transform: translateX(0) scale(1) }
    20%{ transform: translateX(-4px) }
    40%{ transform: translateX(4px) }
    60%{ transform: translateX(-3px) }
    80%{ transform: translateX(3px) }
    100%{ transform: translateX(0) }
  }
  .focus-ring.fade-out { animation: focus-fade .18s ease-out forwards; }
  @keyframes focus-fade { to { opacity: 0; transform: scale(.92) } }

  .zoom-hud{
    position:absolute; top:8px; right:8px; z-index:4;
    background:rgba(0,0,0,.45); backdrop-filter:blur(8px);
    border:1px solid rgba(255,255,255,.18); border-radius:12px; padding:8px 10px;
    font-weight:700; font-variant-numeric: tabular-nums; min-width: 64px; text-align:right;
  }

  /* Panel de controles */
  .controls {
    margin: 12px auto 0; width: min(680px, 96vw);
    background: rgba(10,14,22,.9); backdrop-filter: blur(14px);
    border: 1px solid rgba(255,255,255,.12); border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,.45); padding: 12px 14px;
  }

  .zoom-track-wrap { padding: 4px 0 10px; }
  .zoom-track {
    -webkit-appearance:none; appearance:none; width:100%; height: 8px; background: transparent; touch-action: pan-x;
  }
  .zoom-track::-webkit-slider-runnable-track{ height: 8px; border-radius:999px; background: rgba(255,255,255,.18); }
  .zoom-track::-webkit-slider-thumb{
    -webkit-appearance:none; appearance:none; width: 26px; height:26px; border-radius:50%;
    margin-top:-9px; border:0; background: linear-gradient(180deg, rgba(96,165,250,1), rgba(34,197,94,1));
    box-shadow: 0 6px 16px rgba(0,0,0,.35);
  }
  .zoom-legend{ display:flex; justify-content:space-between; font-size:.9rem; opacity:.85; margin-top:8px; font-variant-numeric: tabular-nums; }

  .controls-row { display:flex; align-items:center; gap:10px; justify-content:center; margin-top:6px; }
  .ctrl-btn{
    flex:0 0 auto; min-width:56px; height:48px; border-radius:12px;
    background:#0f172a; color:#e5e7eb; border:1px solid rgba(255,255,255,.15);
    display:flex; align-items:center; justify-content:center; font-size:22px; cursor:pointer;
    transition: transform .12s ease, box-shadow .12s ease, background .12s ease, opacity .12s;
  }
  .ctrl-btn:active { transform: translateY(1px); }
  .ctrl-btn:disabled{ opacity:.5; cursor:not-allowed }
  .ctrl-btn.primary{ background:#1d283a; border-color:#93c5fd; box-shadow: 0 0 0 2px rgba(59,130,246,.2) inset; }

  .ok{ color:#10b981 } .warn{ color:#f59e0b } .err{ color:#ef4444 }
  .meta{ font-size:.85rem; opacity:.85 } .result{ font-weight:600 }
</style>
</head>
<body>
  <header><strong>Escáner QR — Arquitectura por Clases</strong></header>

  <main>
    <div class="reader-wrap" id="tapZone">
      <div id="reader"></div>
      <div id="focusOverlay"></div>
      <div id="zoomHud" class="zoom-hud" hidden>1×</div>
    </div>

    <div id="controls" class="controls" role="group" aria-label="Controles del lector">
      <div class="zoom-track-wrap">
        <input id="zoomTrack" class="zoom-track" type="range" min="1" max="1" step="0.01" value="1" />
        <div class="zoom-legend">
          <span id="zoomMinLabel">1</span>
          <span id="zoomMaxLabel">1</span>
        </div>
      </div>
      <div class="controls-row">
        <button id="torchBtn"   class="ctrl-btn" title="Linterna" aria-pressed="false" disabled>🔦</button>
        <button id="resetZoomBtn" class="ctrl-btn" title="Restablecer zoom" disabled>↩️</button>
        <button id="playPauseBtn" class="ctrl-btn primary" title="Iniciar / Pausar / Reanudar">▶️</button>
        <button id="stopBtn"      class="ctrl-btn" title="Detener" disabled>⏹️</button>
      </div>
    </div>

    <div class="meta">
      • Slider controla el zoom (mín/máx dinámicos).<br>
      • Tap-to-focus (POI → zoom-bump → refocus), pinch, torch persistente, autopause/resume.
    </div>
    <div id="lastResult" class="result ok"></div>
    <div id="status" class="meta"></div>

    <div id="mi-status" class="meta"></div>
  </main>

<script>
/* ===========================================================
   Utilidades generales (estáticas) y constantes
   =========================================================== */
class Utils {
  /** Devuelve true si el texto parece una URL. */
  static isLikelyUrl(text) {
    return /^https?:\/\/|^[\w-]+\.[\w.-]+(\/|$)/i.test(text);
  }
  /** Limita un número entre min y max. */
  static clamp(n, min, max) { return Math.min(max, Math.max(min, n)); }
  /** Formatea un nivel de zoom, ej: 1,25× (con coma) */
  static formatZoom(z) { return (Math.round(z * 20) / 20 + '').replace('.', ',') + '×'; }
  /** Formatea con decimales fijos (coma). */
  static formatExact(n, dec=2) { return (Math.round(n*Math.pow(10,dec))/Math.pow(10,dec)).toString().replace('.', ','); }
  /** Vibración suave (ignorable si no hay soporte). */
  static haptic(ms=16){ try{ navigator.vibrate?.(ms); }catch{} }
}

/** Claves para localStorage */
const StorageKeys = Object.freeze({
  Zoom:  'qr_zoom_value',
  Torch: 'qr_torch_on'
});

/** Estados del escáner */
const ScannerState = Object.freeze({
  Idle: 'idle',
  Running: 'running',
  Paused: 'paused'
});

/* ===========================================================
   Controlador de la UI (DOM) – centraliza lecturas/escrituras
   =========================================================== */
class UIController {
  constructor() {
    // Contenedor de cámara y overlays
    this.readerContainer = document.getElementById('reader');
    this.tapZone         = document.getElementById('tapZone');
    this.focusOverlay    = document.getElementById('focusOverlay');
    this.zoomHud         = document.getElementById('zoomHud');

    // Texto de estado y resultado
    this.statusEl        = document.getElementById('status');
    this.lastResultEl    = document.getElementById('lastResult');

    // Controles
    this.zoomTrack       = document.getElementById('zoomTrack');
    this.zoomMinLabel    = document.getElementById('zoomMinLabel');
    this.zoomMaxLabel    = document.getElementById('zoomMaxLabel');

    this.torchButton     = document.getElementById('torchBtn');
    this.resetZoomButton = document.getElementById('resetZoomBtn');
    this.playPauseButton = document.getElementById('playPauseBtn');
    this.stopButton      = document.getElementById('stopBtn');

    // Temporales para HUD / focus ring
    this._hudTimer = null;
  }

  /* === Estado visual === */
  setStatus(text, styleClass='') {
    this.statusEl.textContent = text;
    this.statusEl.className   = 'meta ' + styleClass;
    console.log('[status]', text);
  }
  setResult(text, styleClass='ok') {
    this.lastResultEl.textContent = text || '';
    this.lastResultEl.className   = 'result ' + (styleClass || '');
  }

  /* === HUD zoom === */
  showZoomHud(valueOrText) {
    this.zoomHud.textContent = (typeof valueOrText === 'string')
      ? valueOrText
      : Utils.formatZoom(valueOrText);
    this.zoomHud.hidden = false;
    clearTimeout(this._hudTimer);
    this._hudTimer = setTimeout(() => { this.zoomHud.hidden = true; }, 800);
  }

  /* === Focus ring === */
  clearFocusRings(immediate=true) {
    const rings = this.focusOverlay.querySelectorAll('.focus-ring');
    rings.forEach(r => {
      if (immediate) r.remove();
      else { r.classList.add('fade-out'); setTimeout(() => r.remove(), 200); }
    });
  }
  spawnFocusRing(pageX, pageY) {
    const r = document.createElement('div');
    r.className = 'focus-ring';
    // Ajustar a coordenadas relativas al tapZone
    const rect = this.tapZone.getBoundingClientRect();
    r.style.left = (pageX - rect.left - 20) + 'px';
    r.style.top  = (pageY - rect.top  - 20) + 'px';
    this.focusOverlay.appendChild(r);
    requestAnimationFrame(() => r.classList.add('anim-in'));
    // Auto desvanecimiento por si no se marca ok/fail
    const t1=setTimeout(()=> r.classList.add('fade-out'), 900);
    const t2=setTimeout(()=> r.remove(), 1200);
    r._timeouts=[t1,t2];
    return r;
  }
  markFocusRing(r, kind) {
    if (!r) return;
    (r._timeouts||[]).forEach(t => clearTimeout(t));
    r.classList.remove('fade-out');
    r.classList.add(kind);
    setTimeout(() => r.classList.add('fade-out'), kind==='ok' ? 650 : 380);
    setTimeout(() => r.remove(), kind==='ok' ? 880 : 600);
  }

  /* === Controles === */
  setPlayPauseVisual(state) {
    if (state === ScannerState.Idle) {
      this.playPauseButton.textContent = '▶️';
      this.playPauseButton.title = 'Iniciar';
    } else if (state === ScannerState.Running) {
      this.playPauseButton.textContent = '⏸️';
      this.playPauseButton.title = 'Pausar';
    } else {
      this.playPauseButton.textContent = '▶️';
      this.playPauseButton.title = 'Reanudar';
    }
  }

  /* === Zoom UI === */
  setupZoomSlider(min, max, step, value) {
    this.zoomTrack.min   = String(min);
    this.zoomTrack.max   = String(max);
    this.zoomTrack.step  = String(step);
    this.zoomTrack.value = String(value);
    this.zoomMinLabel.textContent = Utils.formatExact(min);
    this.zoomMaxLabel.textContent = Utils.formatExact(max);
  }
  syncZoomSlider(value) {
    this.zoomTrack.value = String(value);
  }
  setZoomSliderEnabled(enabled) {
    this.zoomTrack.disabled = !enabled;
    this.zoomTrack.style.opacity = enabled ? 1 : 0.6;
  }
  setResetZoomEnabled(enabled) { this.resetZoomButton.disabled = !enabled; }

  /* === Torch UI === */
  setTorchEnabled(enabled)  { this.torchButton.disabled = !enabled; }
  setTorchPressed(pressed) {
    this.torchButton.classList.toggle('primary', !!pressed);
    this.torchButton.setAttribute('aria-pressed', pressed ? 'true' : 'false');
  }

  /* === Otros botones === */
  setStopEnabled(enabled) { this.stopButton.disabled = !enabled; }
}

/* ===========================================================
   Controlador de Zoom
   =========================================================== */
class ZoomController {
  constructor(ui) {
    this.ui = ui;
    this.videoTrack = null;
    this.capabilities = null; // {min,max,step}
    this.currentZoom = null;
    this.defaultZoom = 1;
  }

  /** Conecta el track de video y lee capacidades/settings. */
  attachTrack(videoTrack) {
    this.videoTrack = videoTrack;
    this.capabilities = this._readCapabilities();
    if (!this.capabilities) {
      this.currentZoom = null; this.defaultZoom = 1;
      this.ui.setupZoomSlider(1, 1, 0.01, 1);
      this.ui.setZoomSliderEnabled(false);
      this.ui.setResetZoomEnabled(false);
      return;
    }

    const s = this.videoTrack.getSettings?.() || {};
    // Default = valor real con el que arrancó el track (clampeado)
    this.defaultZoom = Utils.clamp(typeof s.zoom === 'number' ? s.zoom : 1, this.capabilities.min, this.capabilities.max);

    // Carga persistida o setting actual
    const saved = parseFloat(localStorage.getItem(StorageKeys.Zoom));
    const initial = (!isNaN(saved) && saved>=this.capabilities.min && saved<=this.capabilities.max)
      ? saved
      : (typeof s.zoom === 'number' ? s.zoom : this.capabilities.min);

    this.currentZoom = Utils.clamp(initial, this.capabilities.min, this.capabilities.max);

    // Inicializa slider
    this.ui.setupZoomSlider(this.capabilities.min, this.capabilities.max, this.capabilities.step, this.currentZoom);
    this.ui.setZoomSliderEnabled(true);
    this._updateResetButtonState();
  }

  /** Devuelve true/false si hay soporte de zoom. */
  hasZoom() { return !!this.capabilities; }

  /** Aplica zoom de manera robusta (actualiza UI y persistencia opcional en caller). */
  async applyZoom(value, { showHud=false } = {}) {
    if (!this.videoTrack || !this.capabilities) return;
    const z = Utils.clamp(parseFloat(value) || this.capabilities.min, this.capabilities.min, this.capabilities.max);
    try {
      await this.videoTrack.applyConstraints({ advanced: [{ zoom: z }] });
      this.currentZoom = z;
      if (showHud) this.ui.showZoomHud(z);
      this.ui.syncZoomSlider(z);
      this._updateResetButtonState();
    } catch {
      if (showHud) this.ui.showZoomHud('zoom no soportado');
    }
  }

  /** Restablece al zoom por defecto. Incluye “epsilon bump” por si el hardware no reacciona. */
  async resetZoom() {
    if (!this.capabilities) return;
    const target = this.defaultZoom;
    const eps = (this.capabilities.step || 0.01) / 2;

    if (this.currentZoom != null && Math.abs(this.currentZoom - target) <= 0.0005) {
      const bump = Utils.clamp(
        target + (target + eps <= this.capabilities.max ? eps : -eps),
        this.capabilities.min,
        this.capabilities.max
      );
      try { await this.applyZoom(bump, { showHud:false }); } catch {}
    }
    await this.applyZoom(target, { showHud:true });
  }

  /** Persistencia (guardar valor actual). */
  persistZoom() {
    if (!this.capabilities || this.currentZoom == null) return;
    try { localStorage.setItem(StorageKeys.Zoom, this.currentZoom); } catch {}
    this._updateResetButtonState();
  }

  /** Habilita/deshabilita slider externamente (ej. en pausa). */
  setEnabled(enabled) { this.ui.setZoomSliderEnabled(!!enabled && !!this.capabilities); }

  /* ---------- Privados ---------- */
  _readCapabilities() {
    try {
      const caps = this.videoTrack?.getCapabilities?.() || {};

      /*document.getElementById('mi-status').textContent = JSON.stringify(caps);

        const constraintList = document.querySelector("#mi-status");
        const supportedConstraints = navigator.mediaDevices.getSupportedConstraints();

        for (const constraint of Object.keys(supportedConstraints)) {
          const elem = document.createElement("li");
          elem.appendChild(document.createElement("code")).textContent = constraint;
          constraintList.appendChild(elem);
      }*/

      if (caps.zoom) {
        return {
          min:  typeof caps.zoom.min  === 'number' ? caps.zoom.min  : 1,
          max:  typeof caps.zoom.max  === 'number' ? caps.zoom.max  : 1,
          step: typeof caps.zoom.step === 'number' ? caps.zoom.step : 0.01,
        };
      }
      return null;
    } catch { return null; }
  }
  _updateResetButtonState() {
    if (!this.capabilities || this.currentZoom == null) { this.ui.setResetZoomEnabled(false); return; }
    const isDefault = Math.abs(this.currentZoom - this.defaultZoom) <= 0.001;
    this.ui.setResetZoomEnabled(!isDefault);
  }
}

/* ===========================================================
   Controlador de Torch (linterna)
   =========================================================== */
class TorchController {
  constructor(ui) {
    this.ui = ui;
    this.videoTrack = null;
    this.hasCapability = false;
    this.desiredOn = (localStorage.getItem(StorageKeys.Torch) === '1');
  }

  attachTrack(videoTrack) {
    this.videoTrack = videoTrack;
    const caps = this._readCapabilities();
    this.hasCapability = caps;
    this.ui.setTorchEnabled(!!caps);
  }

  /** Devuelve true/false si hay soporte de torch. */
  hasTorch() { return this.hasCapability; }

  /** Intenta aplicar el estado deseado al track y sincroniza UI + persistencia. */
  async ensureTorchState(on) {
    this.desiredOn = !!on;
    try { localStorage.setItem(StorageKeys.Torch, this.desiredOn ? '1' : '0'); } catch {}

    if (this.videoTrack && this.hasCapability) {
      try {
        const s = this.videoTrack.getSettings?.() || {};
        if (!!s.torch !== this.desiredOn) {
          await this.videoTrack.applyConstraints({ advanced: [{ torch: this.desiredOn }] });
        }
      } catch { /* algunos browsers bloquean errores silenciosamente */ }
    }
    this.ui.setTorchPressed(this.desiredOn);
  }

  /** Sincroniza UI/track después de resume. */
  async syncAfterResume() {
    if (!this.videoTrack || !this.hasCapability) { this.ui.setTorchPressed(false); return; }
    try {
      const s = this.videoTrack.getSettings?.() || {};
      if (!!s.torch !== this.desiredOn) {
        await this.videoTrack.applyConstraints({ advanced: [{ torch: this.desiredOn }] });
      }
      this.ui.setTorchPressed(this.desiredOn);
    } catch {
      try { const s2 = this.videoTrack.getSettings?.() || {}; this.ui.setTorchPressed(!!s2.torch); }
      catch { this.ui.setTorchPressed(false); }
    }
  }

  /* ---------- Privados ---------- */
  _readCapabilities() {
    try {
      const caps = this.videoTrack?.getCapabilities?.() || {};
      return !!caps.torch;
    } catch { return false; }
  }
}

/* ===========================================================
   Controlador de Enfoque (tap-to-focus + gestos de zoom)
   =========================================================== */
class FocusController {
  constructor(ui, zoomController) {
    this.ui = ui;
    this.zoomController = zoomController;

    this.videoTrack = null;
    this.tapStart = null;

    // Pinch-to-zoom
    this.pinchActive = false;
    this.pinchStartDistance = 0;
    this.pinchStartZoom = 1;
    this.pendingZoom = null;
    this.rafPending = false;
  }

  attachTrack(videoTrack) { this.videoTrack = videoTrack; }

  /** Registra listeners de gestos sobre el área del visor. */
  bindGestureEvents(getScannerState, quickRefocusFn) {
    // Tap-to-focus (solo toques cortos)
    this.ui.tapZone.addEventListener('pointerdown', (e) => {
      if (getScannerState() !== ScannerState.Running || e.pointerType !== 'touch') return;
      this.tapStart = { x: e.clientX, y: e.clientY, t: performance.now() };
    }, { passive: true });

    this.ui.tapZone.addEventListener('pointerup', async (e) => {
      if (getScannerState() !== ScannerState.Running || !this.tapStart) return;
      const dx = e.clientX - this.tapStart.x;
      const dy = e.clientY - this.tapStart.y;
      const dt = performance.now() - this.tapStart.t;
      this.tapStart = null;
      if (Math.hypot(dx, dy) > 10 || dt > 300) return;

      this.ui.clearFocusRings(false);
      const ring = this.ui.spawnFocusRing(e.clientX, e.clientY);

      if (!this.videoTrack) { this.ui.markFocusRing(ring, 'fail'); return; }

      const caps = this._getCapabilities();
      // 1) Puntos de interés (si existen)
      if (caps.pointsOfInterest) {
        const vrect = document.querySelector('#reader video').getBoundingClientRect();
        const x = (e.clientX - vrect.left) / vrect.width;
        const y = (e.clientY - vrect.top)  / vrect.height;
        try {
          await this.videoTrack.applyConstraints({ advanced: [{ focusMode: 'single-shot', pointsOfInterest: [{ x, y }] }] });
          this.ui.markFocusRing(ring, 'ok'); return;
        } catch {
          try {
            await this.videoTrack.applyConstraints({ advanced: [{ focusMode: 'manual', pointsOfInterest: [{ x, y }] }] });
            this.ui.markFocusRing(ring, 'ok'); return;
          } catch { /* fallback abajo */ }
        }
      }
      // 2) Fallback: “zoom-bump”
      if (this.zoomController.hasZoom()) {
        try {
          const s = this.videoTrack.getSettings?.() || {};
          const zCur = typeof s.zoom === 'number' ? s.zoom : this.zoomController.currentZoom ?? this.zoomController.capabilities.min;
          const step = this.zoomController.capabilities.step || 0.1;
          const bump = Utils.clamp(zCur + step, this.zoomController.capabilities.min, this.zoomController.capabilities.max);
          await this.videoTrack.applyConstraints({ advanced: [{ zoom: bump }] });
          await new Promise(r => setTimeout(r, 120));
          await this.videoTrack.applyConstraints({ advanced: [{ zoom: zCur }] });
          this.ui.markFocusRing(ring, 'ok'); return;
        } catch {}
      }
      // 3) Refocus rápido (parar/arrancar)
      try { await quickRefocusFn(); this.ui.markFocusRing(ring, 'ok'); }
      catch { this.ui.markFocusRing(ring, 'fail'); }
    }, { passive: true });

    this.ui.tapZone.addEventListener('pointercancel', () => { this.tapStart = null; }, { passive: true });

    // Pinch-to-zoom con dos dedos
    this.ui.tapZone.addEventListener('touchstart', (e) => {
      if (e.touches.length === 2) {
        if (!this.zoomController.hasZoom()) { this.ui.showZoomHud('zoom no soportado'); return; }
        this.pinchActive = true; e.preventDefault();
        const [a, b] = e.touches;
        this.pinchStartDistance = Math.hypot(a.clientX - b.clientX, a.clientY - b.clientY);
        const s = this.videoTrack?.getSettings?.() || {};
        const current = typeof s.zoom === 'number'
          ? s.zoom
          : (this.zoomController.currentZoom ?? this.zoomController.capabilities.min);
        this.pinchStartZoom = current;
        this.ui.showZoomHud(current);
      }
    }, { passive: false });

    this.ui.tapZone.addEventListener('touchmove', (e) => {
      if (!this.pinchActive) return;
      if (e.touches.length !== 2) { this._onPinchEnd(); return; }
      e.preventDefault();
      const [a, b] = e.touches;
      const curDist = Math.hypot(a.clientX - b.clientX, a.clientY - b.clientY);
      const scale = curDist / (this.pinchStartDistance || 1);
      const target = Utils.clamp(
        this.pinchStartZoom * scale,
        this.zoomController.capabilities.min,
        this.zoomController.capabilities.max
      );
      this._scheduleApplyZoom(target);
    }, { passive: false });

    this.ui.tapZone.addEventListener('touchend',   () => { if (this.pinchActive) this._onPinchEnd(); }, { passive: false });
    this.ui.tapZone.addEventListener('touchcancel',() => { if (this.pinchActive) this._onPinchEnd(); }, { passive: false });
  }

  /* ---------- Privados ---------- */
  _getCapabilities() {
    try { return this.videoTrack?.getCapabilities?.() || {}; } catch { return {}; }
  }
  _scheduleApplyZoom(z) {
    this.pendingZoom = z;
    if (this.rafPending) return;
    this.rafPending = true;
    requestAnimationFrame(async () => {
      this.rafPending = false;
      if (this.pendingZoom == null) return;
      await this.zoomController.applyZoom(this.pendingZoom, { showHud: true });
      this.pendingZoom = null;
    });
  }
  _onPinchEnd() {
    this.pinchActive = false;
    this.zoomController.persistZoom();
  }
}

/* ===========================================================
   Núcleo del escáner (orquestación)
   =========================================================== */
class QRScannerApp {
  constructor() {
    // Config base del lector
    this.configBase = {
      fps: 15,
      qrbox: { width: 200, height: 200 },
      aspectRatio: 1.0,
      rememberLastUsedCamera: true,
      videoConstraints: {
        facingMode: { ideal: "environment" },
        width:  { ideal: 1920 },
        height: { ideal: 1080 }
      }
    };

    // Componentes
    this.ui = new UIController();
    this.zoom = new ZoomController(this.ui);
    this.torch = new TorchController(this.ui);
    this.focus = new FocusController(this.ui, this.zoom);

    // Html5-Qrcode
    this.html5QrCode = new Html5Qrcode("reader");

    // Estado
    this.state = ScannerState.Idle;
    this.mediaStream = null;
    this.videoTrack = null;
    this.firstDecodeDone = false;
    this.autoResume = false;
    this._isStarting = false;

    // Enlazar eventos del UI
    this._bindUIEvents();

    // Pre-warm de permisos (no bloqueante)
    this._preWarmCameraPermission();

    // Mensaje inicial
    this.ui.setStatus('Listo');
  }

  /* ================== API pública de acciones ================== */

  /** Inicia el lector si está en Idle. */
  async start() {
    if (this._isStarting || this.state !== ScannerState.Idle) return;
    this._isStarting = true;
    this.firstDecodeDone = false;
    this.ui.setStatus('Iniciando…', 'warn');
    this.ui.setResult('');

    const dynamicConfig = {
      ...this.configBase,
      qrbox: this._computeQrbox()
    };

    try {
      await this.html5QrCode.start({ facingMode: "environment" }, dynamicConfig,
        (text) => this._onScanSuccess(text),
        () => {/* onScanFailure no-op */}
      );

      const videoElement = document.querySelector('#reader video');
      this.mediaStream = videoElement?.srcObject || null;
      this.videoTrack = this.mediaStream ? this.mediaStream.getVideoTracks()[0] : null;
      if (!this.videoTrack) throw new Error('No se obtuvo videoTrack');

      // Adjuntar track a controladores
      this.zoom.attachTrack(this.videoTrack);
      this.torch.attachTrack(this.videoTrack);
      this.focus.attachTrack(this.videoTrack);

      // Torch inicial (si hay soporte)
      if (this.torch.hasTorch()) {
        await this.torch.ensureTorchState(this.torch.desiredOn);
      } else {
        this.ui.setTorchPressed(false);
      }

      // Si hay zoom, aplica valor inicial y habilita slider
      if (this.zoom.hasZoom()) {
        await this.zoom.applyZoom(this.zoom.currentZoom, { showHud: true });
        this.ui.setResetZoomEnabled(true); // luego se ajusta internamente según default
        this.zoom.persistZoom();           // guarda el valor actual
        this.ui.setZoomSliderEnabled(true);
      } else {
        this.ui.showZoomHud('zoom no soportado');
        this.ui.setZoomSliderEnabled(false);
      }

      // Habilitar stop, actualizar play/pause y estado
      this.ui.setStopEnabled(true);
      this._setState(ScannerState.Running);
      this.ui.setStatus('Escaneando', 'ok');

    } catch (err) {
      console.error(err);
      this.ui.setStatus(this._describeStartError(err), 'err');
    } finally {
      this._isStarting = false;
    }
  }

  /** Pausa la lectura (mantiene cámara activa). */
  async pause() {
    if (this.state !== ScannerState.Running) return;
    try {
      this.html5QrCode.pause(true);
      this._setState(ScannerState.Paused);
      this.ui.setStatus('Pausado', 'warn');
      this.zoom.setEnabled(false);
      this.ui.setTorchEnabled(false);
    } catch (e) { console.warn(e); }
  }

  /** Reanuda la lectura. */
  async resume() {
    if (this.state !== ScannerState.Paused) return;
    try {
      this.html5QrCode.resume();
      await this.torch.syncAfterResume();
      this._setState(ScannerState.Running);
      this.ui.setStatus('Escaneando', 'ok');
      this.zoom.setEnabled(true);
      this.ui.setTorchEnabled(this.torch.hasTorch());
    } catch (e) { console.warn(e); }
  }

  /** Detiene por completo (libera recursos y limpia UI). */
  async stop() {
    if (this.state === ScannerState.Idle) return;
    try {
      await this.html5QrCode.stop();
      await this.html5QrCode.clear();
    } catch (e) { console.warn(e); }
    this.mediaStream = null; this.videoTrack = null;
    this._setState(ScannerState.Idle);
    this.ui.setStatus('Detenido');
    this.ui.setStopEnabled(false);
    this.ui.setZoomSliderEnabled(false);
    this.ui.setTorchEnabled(false);
    this.ui.clearFocusRings(true);
  }

  /** Restablece el zoom con UX robusta. */
  async resetZoom() {
    if (!this.zoom.hasZoom()) return;
    await this.zoom.resetZoom();
    this.zoom.persistZoom();
  }

  /** Alterna linterna (si hay soporte). */
  async toggleTorch() {
    if (!this.torch.hasTorch()) return;
    const next = !this.ui.torchButton.classList.contains('primary');
    await this.torch.ensureTorchState(next);
  }

  /** Refocus rápido (parar/arrancar). Útil como fallback de enfoque. */
  async quickRefocus() {
    if (!this.videoTrack) throw new Error('no track');
    await this.stop();
    await this.start();
  }

  /* ================== Privados / infraestructura ================== */

  _bindUIEvents() {
    // Play / Pause / Stop
    this.ui.playPauseButton.addEventListener('click', async () => {
      Utils.haptic();
      if (this.state === ScannerState.Idle)       await this.start();
      else if (this.state === ScannerState.Running) await this.pause();
      else if (this.state === ScannerState.Paused)  await this.resume();
    });
    this.ui.stopButton.addEventListener('click', async () => { Utils.haptic(20); await this.stop(); });

    // Reset zoom
    this.ui.resetZoomButton.addEventListener('click', async () => { Utils.haptic(); await this.resetZoom(); });

    // Torch
    this.ui.torchButton.addEventListener('click', async () => { Utils.haptic(); if (!this.ui.torchButton.disabled) await this.toggleTorch(); });

    // Slider de zoom (drag continuo + persistencia al soltar)
    this.ui.zoomTrack.addEventListener('input', async () => {
      if (this.ui.zoomTrack.disabled || !this.zoom.hasZoom()) return;
      const z = parseFloat(this.ui.zoomTrack.value);
      await this.zoom.applyZoom(z, { showHud: true });
    });
    const persist = () => this.zoom.persistZoom();
    this.ui.zoomTrack.addEventListener('change',   persist);
    this.ui.zoomTrack.addEventListener('pointerup',persist);
    this.ui.zoomTrack.addEventListener('touchend', persist);
    this.ui.zoomTrack.addEventListener('mouseup',  persist);

    // Gestos (tap-to-focus y pinch) delegados al FocusController
    this.focus.bindGestureEvents(() => this.state, () => this.quickRefocus());

    // Auto pause / resume por visibilidad
    document.addEventListener('visibilitychange', async () => {
      if (document.hidden) {
        if (this.state === ScannerState.Running) { await this.pause(); this.autoResume = true; }
        else { this.autoResume = false; }
      } else {
        if (this.autoResume && this.state === ScannerState.Paused) { await this.resume(); }
        this.autoResume = false;
      }
    });
    window.addEventListener('pagehide', async () => {
      if (this.state === ScannerState.Running) { await this.pause(); this.autoResume = true; }
      else { this.autoResume = false; }
    });
    window.addEventListener('pageshow', async () => {
      if (this.autoResume && this.state === ScannerState.Paused) { await this.resume(); }
      this.autoResume = false;
    });
  }

  _computeQrbox() {
    const side = this.ui.tapZone.getBoundingClientRect().width || 320;
    const size = Math.round(Math.min(side * 0.60, 320));
    return { width: size, height: size };
  }

  _setState(next) {
    this.state = next;
    this.ui.setPlayPauseVisual(this.state);
  }

  async _onScanSuccess(text) {
    // Anti-duplicados
    if (this.firstDecodeDone) return;
    this.firstDecodeDone = true;

    Utils.haptic(40);
    this.ui.setResult('Detectado: ' + text, 'ok');

    // One-shot: detener y actuar
    await this.stop();

    if (Utils.isLikelyUrl(text)) {
      const url = /^https?:\/\//i.test(text) ? text : ('https://' + text);
      location.href = url;
    } else {
      alert('Código detectado:\n' + text);
    }
  }

  _describeStartError(e) {
    const name = e?.name || e?.toString?.() || '';
    if (/NotAllowedError/i.test(name))       return 'Permiso de cámara denegado.';
    if (/NotFoundError|OverconstrainedError/i.test(name)) return 'No se encontró la cámara o no soporta las constraints.';
    if (/NotReadableError|TrackStartError/i.test(name))   return 'La cámara está en uso por otra app.';
    if (/SecurityError/i.test(name))         return 'Se requiere HTTPS para usar la cámara.';
    return 'Error al iniciar la cámara: ' + (e?.message || name);
  }

  async _preWarmCameraPermission() {
    try { await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }); } catch {}
  }
}

/* ===========================================================
   Bootstrap de la app
   =========================================================== */
const app = new QRScannerApp();
</script>
</body>
</html>
