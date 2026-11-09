/* สลับโหมดมืด/สว่าง — จำค่าที่เลือกไว้ (localStorage 'krd_theme') */
(function () {
  var root = document.documentElement, btn = document.getElementById('themeToggle');
  function apply(t) {
    if (t === 'dark') root.setAttribute('data-theme', 'dark'); else root.removeAttribute('data-theme');
    if (btn) { btn.setAttribute('aria-pressed', t === 'dark' ? 'true' : 'false'); btn.title = t === 'dark' ? 'กลับเป็นโหมดสว่าง' : 'โหมดมืด (ถนอมสายตา)'; }
    var m = document.querySelector('meta[name="theme-color"]'); if (m) m.content = t === 'dark' ? '#171214' : '#FFF9F5';
  }
  apply(root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light');
  if (btn) btn.addEventListener('click', function () {
    var t = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    apply(t); try { localStorage.setItem('krd_theme', t); } catch (e) {}
  });
})();
