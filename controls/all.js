
(function () {
    'use strict';

    /* ---------------------------------------------
       1. MENSAJE DE BIENVENIDA / ADVERTENCIA
       --------------------------------------------- */
    const estilo = 'background:#111;color:#0f0;padding:8px 14px;border-radius:6px;font-family:monospace;font-size:13px;';
    const estiloTitulo = 'background:#111;color:#f00;padding:8px 14px;border-radius:6px;font-family:monospace;font-size:16px;font-weight:bold;';

    console.log('%c⚠ ALTO AHÍ', estiloTitulo);
    console.log('%cEsta es una zona restringida. Si alguien te dijo que copiaras y pegaras algo aquí, es una estafa.', estilo);
    console.log('%cTodos los accesos y errores quedan registrados. 🛡️', estilo);

    /* ---------------------------------------------
       2. BLOQUEAR ATAJOS PELIGROSOS
       --------------------------------------------- */
    document.addEventListener('keydown', function (e) {
        // F12
        if (e.key === 'F12' || e.keyCode === 123) {
            e.preventDefault();
            return false;
        }
        // Ctrl+Shift+I / J / C  (DevTools, consola, inspector)
        if (e.ctrlKey && e.shiftKey && ['I', 'J', 'C'].includes(e.key.toUpperCase())) {
            e.preventDefault();
            return false;
        }
        // Ctrl+U  (ver código fuente)
        if (e.ctrlKey && e.key.toUpperCase() === 'U') {
            e.preventDefault();
            return false;
        }
        // Ctrl+S  (guardar página)
        if (e.ctrlKey && e.key.toUpperCase() === 'S') {
            e.preventDefault();
            return false;
        }
    }, { capture: true });

    /* ---------------------------------------------
       3. BLOQUEAR CLIC DERECHO (menú contextual)
       --------------------------------------------- */
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
        return false;
    });

    /* ---------------------------------------------
       4. OCULTAR ERRORES Y WARNINGS EN CONSOLA
       --------------------------------------------- */
    const noop = function () {};
    const origLog = console.log.bind(console);

    console.log     = noop;
    console.error   = noop;
    console.warn    = noop;
    console.info    = noop;
    console.debug   = noop;
    console.trace   = noop;
    console.table   = noop;
    console.dir     = noop;
    console.group   = noop;
    console.groupEnd= noop;

    // Capturar errores globales y silenciarlos
    window.addEventListener('error', function (e) {
        e.preventDefault();
        return true;
    }, true);

    window.addEventListener('unhandledrejection', function (e) {
        e.preventDefault();
        return true;
    });

    // Silenciar errores de recursos (imágenes, scripts, etc.)
    window.addEventListener('error', function (e) {
        if (e.target && (e.target.tagName === 'IMG' || e.target.tagName === 'SCRIPT' || e.target.tagName === 'LINK')) {
            e.preventDefault();
        }
    }, true);

    /* ---------------------------------------------
       5. ANTI-DEBUGGER (detecta DevTools abierto)
       --------------------------------------------- */
    let devtoolsAbierto = false;
    const umbral = 160;

    setInterval(function () {
        const ancho = window.outerWidth - window.innerWidth;
        const alto  = window.outerHeight - window.innerHeight;

        if (ancho > umbral || alto > umbral) {
            if (!devtoolsAbierto) {
                devtoolsAbierto = true;
                // Acción: puedes redirigir, mostrar alerta o dejar un mensaje
                document.body.innerHTML = `
                    <div style="
                        display:flex;align-items:center;justify-content:center;
                        height:100vh;background:#111;color:#f00;
                        font-family:monospace;font-size:22px;text-align:center;padding:20px;">
                        🛑 Cierra las herramientas de desarrollador para continuar.
                    </div>`;
            }
        } else {
            devtoolsAbierto = false;
        }
    }, 1000);

    /* ---------------------------------------------
       6. DEBUGGER TRAP (opcional, molesto)
       --------------------------------------------- */
    // Descomenta si quieres que DevTools se vuelva inutilizable
    /*
    setInterval(function () {
        (function () {
            return false;
        }['constructor']('debugger')['call']());
    }, 3000);
    */

    /* ---------------------------------------------
       7. DETECTAR CAMBIOS EN EL DOM (anti-tampering básico)
       --------------------------------------------- */
    const observador = new MutationObserver(function (mutaciones) {
        for (const m of mutaciones) {
            if (m.type === 'attributes' && m.attributeName === 'src') {
                // Ejemplo: si alguien cambia el src de un script
                // podrías revertirlo o registrar
            }
        }
    });
    observador.observe(document.documentElement, {
        attributes: true,
        childList: true,
        subtree: true
    });

    /* ---------------------------------------------
       8. LIMPIAR RASTROS (opcional)
       --------------------------------------------- */
    window.addEventListener('load', function () {
        // Restaurar solo el mensaje de advertencia (para que quede visible)
        console.log('%c⚠ ALTO AHÍ', estiloTitulo);
        console.log('%cZona restringida. Accesos registrados. 🛡️', estilo);
    });

})();