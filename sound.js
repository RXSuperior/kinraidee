/* ========== KinRaiDee — เสียงตอนกด (สังเคราะห์ด้วย Web Audio ไม่ต้องมีไฟล์เสียง) ==========
 * - กดปุ่ม/ลิงก์/การ์ด = เสียงป๊อปสั้น ๆ
 * - ปุ่มหลัก (สุ่ม, ค้นหา, สร้าง ฯลฯ) = เสียงกริ๊งสดใส
 * - แจ้งเตือน (toast) สำเร็จ = เสียงติ๊งขึ้น, ผิดพลาด = เสียงต่ำสั้น
 * - ปุ่มลำโพงบนเมนูเปิด/ปิดเสียง (จำค่าไว้ในเบราว์เซอร์) · ค่าเริ่มต้น = เปิด
 */
(function () {
  var KEY = 'krd_sound', ctx = null, on = true;
  try { on = localStorage.getItem(KEY) !== 'off'; } catch (e) {}
  var Ctor = window.AudioContext || window.webkitAudioContext;

  function ac() {
    if (!Ctor) return null;
    if (!ctx) { try { ctx = new Ctor(); } catch (e) { return null; } }
    if (ctx.state === 'suspended') ctx.resume();
    return ctx;
  }
  // เล่นโน้ตหนึ่งตัว: ความถี่เริ่ม→จบ, ความยาว, เวลาเริ่ม (วินาที), ความดัง, ชนิดคลื่น
  function note(c, f0, f1, dur, at, vol, type) {
    var o = c.createOscillator(), g = c.createGain(), t = c.currentTime + (at || 0);
    o.type = type || 'sine';
    o.frequency.setValueAtTime(f0, t);
    if (f1 && f1 !== f0) o.frequency.exponentialRampToValueAtTime(f1, t + dur);
    g.gain.setValueAtTime(0.0001, t);
    g.gain.exponentialRampToValueAtTime(vol, t + 0.008);
    g.gain.exponentialRampToValueAtTime(0.0001, t + dur);
    o.connect(g); g.connect(c.destination);
    o.start(t); o.stop(t + dur + 0.02);
  }
  var SOUNDS = {
    tap:     function (c) { note(c, 520, 340, 0.07, 0, 0.09, 'sine'); },
    primary: function (c) { note(c, 660, 880, 0.09, 0, 0.1, 'triangle'); note(c, 990, 990, 0.12, 0.07, 0.08, 'sine'); },
    success: function (c) { note(c, 660, 660, 0.1, 0, 0.09, 'sine'); note(c, 880, 880, 0.1, 0.09, 0.09, 'sine'); note(c, 1175, 1175, 0.18, 0.18, 0.08, 'sine'); },
    error:   function (c) { note(c, 220, 160, 0.16, 0, 0.1, 'triangle'); note(c, 180, 130, 0.18, 0.12, 0.09, 'triangle'); },
    toggle:  function (c) { note(c, 400, 600, 0.06, 0, 0.08, 'square'); }
  };
  function play(name) {
    if (!on) return;
    var c = ac(); if (!c) return;
    try { SOUNDS[name](c); } catch (e) {}
  }
  window.KRD_sound = play;

  // ---------- ปุ่มเปิด/ปิดเสียง ----------
  var btn = document.getElementById('soundToggle');
  function paint() {
    if (!btn) return;
    btn.setAttribute('aria-pressed', on);
    btn.classList.toggle('muted', !on);
    btn.setAttribute('title', on ? 'ปิดเสียงตอนกด' : 'เปิดเสียงตอนกด');
  }
  if (btn) btn.addEventListener('click', function (e) {
    e.stopPropagation();           // ไม่ให้ตัวจับการคลิกทั่วไปเล่นเสียงซ้ำ
    on = !on; try { localStorage.setItem(KEY, on ? 'on' : 'off'); } catch (err) {}
    paint();
    if (on) play('toggle');
  });
  paint();

  // ---------- จับการคลิกทั้งหน้า ----------
  var TARGET = 'button, a[href], [role="button"], .chip, .cat-tile, .fcard, .pick-item, .fpill, .seg-btn, summary, select';
  var PRIMARY = '.btn-primary, .cta-main, .nav-btn.solid';
  document.addEventListener('click', function (e) {
    var t = e.target.closest && e.target.closest(TARGET);
    if (!t || t.disabled || t.getAttribute('aria-disabled') === 'true') return;
    play(t.matches(PRIMARY) ? 'primary' : 'tap');
  }, true);

  // ---------- เสียงแจ้งเตือน: ครอบ toast() ของ script.js ----------
  var origToast = window.toast;
  if (typeof origToast === 'function') {
    var BAD = /ไม่|ผิด|ล้มเหลว|กรุณา|ต้อง|ซ้ำ|ปิดแล้ว|ขอตำแหน่ง/;
    window.toast = function (msg) {
      origToast.apply(this, arguments);
      setTimeout(function () { play(BAD.test(String(msg)) ? 'error' : 'success'); }, 60);
    };
  }
})();
