/* ========== KinRaiDee — ระบบนำทางหน้าแรก (โหลดหลัง script.js) ==========
 * ไม่แตะต้องโค้ดเดิมใน script.js — แค่เรียกใช้ฟังก์ชัน/ตัวแปรที่มีอยู่แล้ว:
 *   currentCat, CATEGORIES, renderBrowseChips(), loadRestaurants(), toast()
 */
(function () {
  // สลับแท็บ (ทำงานแบบเดียวกับปุ่ม .tab-btn ใน script.js) — btn = ปุ่มเมนูที่จะไฮไลต์ (ไม่ใส่ = หาจาก data-tab)
  function showTab(name, btn) {
    document.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
    var target = btn || document.querySelector('.tab-btn[data-tab="' + name + '"]');
    if (target) target.classList.add('active');
    document.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.add('hidden'); });
    var el = document.getElementById('tab-' + name);
    if (el) el.classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  window.KRD_showTab = showTab; // ให้ auth.js เรียกสลับหน้าได้

  // "ร้านใกล้ฉัน" = หน้าเลือกเองแบบทุกหมวด (หน้านั้นเรียงตามระยะทางใกล้สุดอยู่แล้ว)
  function resetBrowseToAll() {
    if (typeof currentCat !== 'undefined' && currentCat !== '') {
      currentCat = '';
      renderBrowseChips();
      loadRestaurants();
    }
  }

  // เปิดหน้าเลือกเองพร้อมกรองหมวดหมู่ที่กด
  function openCategory(name) {
    var known = (typeof CATEGORIES !== 'undefined') ? CATEGORIES : [];
    var exists = known.length === 0 || known.some(function (c) { return c.category_name === name; });
    currentCat = exists ? name : '';
    renderBrowseChips();
    loadRestaurants();
    showTab('browse');
    if (!exists) toast('ยังไม่มีหมวด "' + name + '" ในระบบ แสดงร้านทั้งหมดแทน');
  }

  // ปุ่ม/การ์ด/โลโก้ที่มี data-goto="ชื่อแท็บ"
  document.querySelectorAll('[data-goto]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      var near = !!el.dataset.near;
      if (near) resetBrowseToAll();
      showTab(el.dataset.goto, near ? document.querySelector('.tab-btn[data-near]') : null);
    });
  });

  // หมวดหมู่อาหารยอดนิยมที่มี data-cat="ชื่อหมวดในฐานข้อมูล"
  document.querySelectorAll('[data-cat]').forEach(function (el) {
    el.addEventListener('click', function () { openCategory(el.dataset.cat); });
  });

  // เมนูบนสุด (.tab-btn) — script.js สลับแท็บให้อยู่แล้ว ที่นี่เพิ่มแค่เลื่อนขึ้นบนสุด และกรณี "ร้านใกล้ฉัน"
  document.querySelectorAll('.nav-links .tab-btn').forEach(function (b) {
    b.addEventListener('click', function () {
      if (b.dataset.near) resetBrowseToAll();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });
})();
