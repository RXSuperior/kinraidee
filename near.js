/* ========== KinRaiDee — หน้าร้านใกล้ฉัน: แยกตามหมวดอาหาร + ค้นหา/กรอง/เรียง ==========
 * ครอบฟังก์ชัน renderBrowseList() และ renderBrowseChips() ของ script.js (โหลดหลัง script.js)
 *  - "ทั้งหมด"   : แยกเป็นหมวด ๆ หัวข้อชัดเจน แต่ละหมวดโชว์ร้านใกล้สุด 5 ร้าน + ปุ่มดูทั้งหมดของหมวดนั้น
 *  - เลือกหมวด   : โชว์เฉพาะหมวดนั้น เรียงใกล้ไปไกล โหลดเพิ่มทีละ 15 ร้าน
 * การดึงข้อมูล/คำนวณระยะทางยังเป็นของ script.js เหมือนเดิม
 */
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var PAGE = 15, PREVIEW = 5;
  var all = [];                                  // ร้านทั้งหมดล่าสุดจาก script.js (ไม่แยกหมวด)
  var st = { q: '', dist: 0, open: false, sort: 'dist', cat: '', shown: PAGE };
  var origRender = window.renderBrowseList;
  if (typeof origRender !== 'function') return;

  var DAYS = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
  function isOpenNow(r) {
    if (parseInt(r.is_open) !== 1) return false;
    var now = new Date(), today = DAYS[now.getDay()];
    if ((r.closed_day || '').split(',').indexOf(today) !== -1) return false;
    function mins(t) { var p = String(t || '0:0').split(':'); return (+p[0]) * 60 + (+p[1] || 0); }
    var cur = now.getHours() * 60 + now.getMinutes(), o = mins(r.open_time), c = mins(r.close_time);
    return c < o ? (cur >= o || cur <= c) : (cur >= o && cur <= c);
  }
  function el(tag, cls, text) { var e = document.createElement(tag); if (cls) e.className = cls; if (text !== undefined) e.textContent = text; return e; }

  // ร้านที่ผ่านตัวกรอง (ยังไม่แยกหมวด)
  function base() {
    var q = st.q.trim().toLowerCase();
    return all.filter(function (r) {
      if (q && String(r.restaurant_name).toLowerCase().indexOf(q) === -1) return false;
      if (st.dist && parseFloat(r.distance_m) > st.dist) return false;
      if (st.open && !isOpenNow(r)) return false;
      return true;
    });
  }
  function sorter() {
    return {
      dist: function (a, b) { return parseFloat(a.distance_m) - parseFloat(b.distance_m); },
      price: function (a, b) { return parseFloat(a.budget_max) - parseFloat(b.budget_max); },
      name: function (a, b) { return String(a.restaurant_name).localeCompare(b.restaurant_name, 'th'); }
    }[st.sort];
  }
  // เรียงลำดับหมวดตามที่ตั้งไว้ในระบบ (CATEGORIES) แล้วตามด้วยหมวดที่ไม่อยู่ในลิสต์
  function catOrder(names) {
    var known = (typeof CATEGORIES !== 'undefined' ? CATEGORIES : []).map(function (c) { return c.category_name; });
    return names.sort(function (a, b) {
      var ia = known.indexOf(a), ib = known.indexOf(b);
      return (ia < 0 ? 999 : ia) - (ib < 0 ? 999 : ib) || a.localeCompare(b, 'th');
    });
  }
  function groups(rows) {
    var g = {};
    rows.forEach(function (r) { (g[r.category_name] = g[r.category_name] || []).push(r); });
    return g;
  }

  // ใช้การ์ดร้านเดิมของ script.js: ให้มันวาดลง #browseList แล้วย้ายการ์ดมาใส่กล่องของเรา
  function cards(rows) {
    var box = $('browseList'), frag = document.createDocumentFragment();
    origRender(rows);
    while (box.firstChild) frag.appendChild(box.firstChild);
    return frag;
  }

  // ---------- เลื่อนแถบหมวด: ปุ่มลูกศร + ล้อเมาส์ + ลากด้วยเมาส์/นิ้ว ----------
  var chipsBox = $('browseChips'), arrL = $('chipsLeft'), arrR = $('chipsRight');
  function updateArrows() {
    if (!chipsBox) return;
    var max = chipsBox.scrollWidth - chipsBox.clientWidth;
    var l = chipsBox.scrollLeft > 4, r = chipsBox.scrollLeft < max - 4;
    arrL.classList.toggle('is-hidden', !l); arrL.style.display = l ? '' : 'none';
    arrR.classList.toggle('is-hidden', !r); arrR.style.display = r ? '' : 'none';
  }
  function slide(dir) { chipsBox.scrollBy({ left: dir * Math.max(160, chipsBox.clientWidth * 0.7), behavior: 'smooth' }); }
  arrL.addEventListener('click', function () { slide(-1); });
  arrR.addEventListener('click', function () { slide(1); });
  chipsBox.addEventListener('scroll', updateArrows, { passive: true });
  window.addEventListener('resize', updateArrows);
  if (window.ResizeObserver) { var ro = new ResizeObserver(updateArrows); ro.observe(chipsBox); }
  document.addEventListener('click', function () { setTimeout(updateArrows, 120); });
  // ล้อเมาส์เลื่อนขึ้นลง → เลื่อนแถบไปทางข้างแทน (เฉพาะเมื่อแถบยังเลื่อนได้)
  chipsBox.addEventListener('wheel', function (e) {
    if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) return;
    var max = chipsBox.scrollWidth - chipsBox.clientWidth;
    if (max <= 0) return;
    var next = chipsBox.scrollLeft + e.deltaY;
    if ((e.deltaY < 0 && chipsBox.scrollLeft <= 0) || (e.deltaY > 0 && chipsBox.scrollLeft >= max)) return;  // สุดทางแล้ว ปล่อยให้หน้าเลื่อนต่อ
    e.preventDefault(); chipsBox.scrollLeft = next;
  }, { passive: false });
  // ลากด้วยเมาส์
  var drag = null, moved = false;
  chipsBox.addEventListener('pointerdown', function (e) {
    if (e.pointerType !== 'mouse') return;
    drag = { x: e.clientX, left: chipsBox.scrollLeft }; moved = false;
  });
  window.addEventListener('pointermove', function (e) {
    if (!drag) return;
    var dx = e.clientX - drag.x;
    if (Math.abs(dx) > 5) { moved = true; chipsBox.classList.add('dragging'); }
    if (moved) chipsBox.scrollLeft = drag.left - dx;
  });
  window.addEventListener('pointerup', function () { drag = null; setTimeout(function () { chipsBox.classList.remove('dragging'); }, 0); });
  chipsBox.addEventListener('click', function (e) { if (moved) { e.stopPropagation(); e.preventDefault(); moved = false; } }, true);
  // เปลี่ยนหมวดแล้ว เลื่อนให้เห็นชิปที่เลือกเฉพาะเมื่อมันอยู่นอกจอ
  function revealActive() {
    var act = chipsBox.querySelector('.chip.active'); if (!act) return;
    var l = act.offsetLeft, r = l + act.offsetWidth;
    if (l < chipsBox.scrollLeft) chipsBox.scrollLeft = Math.max(0, l - 8);
    else if (r > chipsBox.scrollLeft + chipsBox.clientWidth) chipsBox.scrollLeft = r - chipsBox.clientWidth + 8;
  }

  // ---------- แถบหมวด (ชิป) พร้อมจำนวนร้าน ----------
  function drawChips() {
    var box = $('browseChips'); if (!box) return;
    var g = groups(base()), total = base().length;
    box.innerHTML = '';
    function chip(label, count, cat) {
      var b = el('button', 'chip' + (st.cat === cat ? ' active' : ''), label + (count !== null ? ' ' + count : ''));
      b.type = 'button';
      b.addEventListener('click', function () { st.cat = cat; st.shown = PAGE; draw(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
      box.appendChild(b);
    }
    chip('ทั้งหมด', all.length ? total : null, '');
    var cats = (typeof CATEGORIES !== 'undefined' ? CATEGORIES : []).map(function (c) { return c.category_name; });
    Object.keys(g).forEach(function (n) { if (cats.indexOf(n) < 0) cats.push(n); });
    cats.forEach(function (n) {
      var c = g[n] ? g[n].length : 0;
      if (all.length && !c && st.cat !== n) return;            // ซ่อนหมวดที่ไม่มีร้าน
      chip(n, all.length ? c : null, n);
    });
    revealActive(); updateArrows();
  }

  // ---------- วาดรายการ ----------
  function draw() {
    var box = $('browseList'), more = $('nearMore'), count = $('nearCount');
    var rows = base().sort(sorter());
    var g = groups(rows), names = catOrder(Object.keys(g));
    drawChips();
    box.innerHTML = ''; more.classList.add('is-hidden');
    var out = document.createDocumentFragment();   // สร้างให้เสร็จนอกหน้าจอก่อน (cards() ล้าง #browseList ทุกครั้งที่เรียก)
    function flush() { box.innerHTML = ''; box.appendChild(out); }

    if (!all.length) { count.textContent = ''; return; }
    if (!rows.length) {
      var e = el('p', 'near-empty', 'ไม่พบร้านที่ตรงกับตัวกรอง'); var rb = el('button', 'btn-ghost', 'ล้างตัวกรอง'); rb.type = 'button';
      rb.addEventListener('click', reset); e.appendChild(document.createElement('br')); e.appendChild(rb); out.appendChild(e); flush();
      count.textContent = 'ไม่พบร้านที่ตรงกัน'; return;
    }

    if (st.cat) {                                  // ----- มุมมองหมวดเดียว -----
      var list = g[st.cat] || [];
      var head = el('div', 'cat-focus');
      var back = el('button', 'btn-ghost cat-back', '← ทุกหมวด'); back.type = 'button';
      back.addEventListener('click', function () { st.cat = ''; st.shown = PAGE; draw(); });
      head.appendChild(back);
      head.appendChild(el('h3', 'cat-name', st.cat));
      head.appendChild(el('span', 'cat-count', list.length + ' ร้าน'));
      out.appendChild(head);
      if (!list.length) { out.appendChild(el('p', 'near-empty', 'ไม่มีร้านในหมวดนี้ตามตัวกรองที่เลือก')); flush(); count.textContent = ''; return; }
      var shown = list.slice(0, st.shown), wrap = el('div', 'cat-list space-y-3'); wrap.appendChild(cards(shown)); out.appendChild(wrap); flush();
      count.textContent = 'แสดง ' + shown.length + ' จาก ' + list.length + ' ร้าน';
      var left = list.length - shown.length;
      if (left > 0) { more.classList.remove('is-hidden'); more.textContent = 'ดูร้านเพิ่มอีก ' + Math.min(PAGE, left) + ' ร้าน (เหลือ ' + left + ')'; }
      return;
    }

    // ----- มุมมองทุกหมวด: แยกเป็นส่วน ๆ -----
    count.textContent = 'พบ ' + rows.length + ' ร้าน ใน ' + names.length + ' หมวด';
    names.forEach(function (n) {
      var list = g[n], sec = el('section', 'cat-section');
      var head = el('div', 'cat-head-row');
      head.appendChild(el('span', 'cat-bar'));
      head.appendChild(el('h3', 'cat-name', n));
      head.appendChild(el('span', 'cat-count', list.length + ' ร้าน'));
      var near = Math.min.apply(null, list.map(function (r) { return parseFloat(r.distance_m); }));
      if (isFinite(near)) head.appendChild(el('span', 'cat-near', 'ใกล้สุด ' + fmtDist(near)));
      sec.appendChild(head);
      var wrap = el('div', 'cat-list space-y-3'); wrap.appendChild(cards(list.slice(0, PREVIEW))); sec.appendChild(wrap);
      if (list.length > PREVIEW) {
        var mb = el('button', 'btn-ghost cat-more', 'ดูทั้งหมด ' + list.length + ' ร้านใน' + n + ' →'); mb.type = 'button';
        mb.addEventListener('click', function () { st.cat = n; st.shown = PAGE; draw(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
        sec.appendChild(mb);
      }
      out.appendChild(sec);
    });
    flush();
  }

  // ---------- แทนที่ฟังก์ชันของ script.js ----------
  window.renderBrowseList = function (arr) { all = arr || []; draw(); };
  window.renderBrowseChips = function () {
    // หน้าแรก (ช่องหมวด) ตั้ง currentCat มา → รับไปเป็นหมวดที่เลือก แล้วให้ script.js โหลดร้านทุกหมวดเสมอ
    if (typeof currentCat !== 'undefined' && currentCat) { st.cat = currentCat; st.shown = PAGE; currentCat = ''; }
    drawChips();
  };
  // กด "ร้านใกล้ฉัน" จากที่ไหนก็ตาม = กลับมุมมองทุกหมวด
  document.querySelectorAll('[data-near]').forEach(function (b) {
    b.addEventListener('click', function () { st.cat = ''; st.shown = PAGE; if (all.length) draw(); }, true);
  });

  function reset() { st.q = ''; st.dist = 0; st.open = false; $('nearSearch').value = ''; syncUi(); draw(); }
  function syncUi() {
    document.querySelectorAll('.fpill[data-dist]').forEach(function (b) { b.classList.toggle('active', +b.dataset.dist === st.dist); });
    var o = $('nearOpen'); o.classList.toggle('active', st.open); o.setAttribute('aria-pressed', st.open);
  }
  var t;
  $('nearSearch').addEventListener('input', function (e) { clearTimeout(t); t = setTimeout(function () { st.q = e.target.value; st.shown = PAGE; draw(); }, 150); });
  document.querySelectorAll('.fpill[data-dist]').forEach(function (b) { b.addEventListener('click', function () { st.dist = +b.dataset.dist; st.shown = PAGE; syncUi(); draw(); }); });
  $('nearOpen').addEventListener('click', function () { st.open = !st.open; st.shown = PAGE; syncUi(); draw(); });
  $('nearSort').addEventListener('change', function (e) { st.sort = e.target.value; draw(); });
  $('nearMore').addEventListener('click', function () { st.shown += PAGE; draw(); });

  // แถบค้นหาลอยค้างใต้เมนูบนสุด
  function setNavH() { var nav = document.querySelector('.site-nav'); if (nav) document.documentElement.style.setProperty('--nav-h', (nav.getBoundingClientRect().bottom + 6) + 'px'); }
  setNavH(); window.addEventListener('resize', setNavH); setTimeout(setNavH, 600);

  var top = $('toTop');
  window.addEventListener('scroll', function () {
    var show = window.scrollY > 600 && !$('tab-browse').classList.contains('hidden');
    top.classList.toggle('is-hidden', !show); top.style.display = show ? '' : 'none';
  }, { passive: true });
  top.style.display = 'none';
  top.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });
  syncUi();
})();
