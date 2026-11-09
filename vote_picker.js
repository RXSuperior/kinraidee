/* ========== KinRaiDee — คนสร้างห้องโหวตเลือกร้านเอง (โหลดหลัง script.js) ==========
 * ใช้ตัวแปร/ฟังก์ชันเดิมจาก script.js: apiPost, toast, currentVoteGroup, createdHistory,
 * renderVoteHistory, enterVoteRoom  และ API: vote.php (action list / create)
 */
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var all = [];                 // ร้านทั้งหมดที่เลือกได้
  var picked = new Set();       // restaurant_id ที่เลือก
  var loaded = false;

  function show(which) {        // lobby | picker
    $('voteLobby').classList.toggle('hidden', which !== 'lobby');
    $('votePicker').classList.toggle('hidden', which !== 'picker');
  }

  function visible() {
    var q = $('pickSearch').value.trim().toLowerCase(), cat = $('pickCat').value;
    return all.filter(function (r) {
      return (!cat || r.category_name === cat) && (!q || r.restaurant_name.toLowerCase().indexOf(q) !== -1);
    });
  }

  function updateCount() {
    $('pickCount').textContent = picked.size;
    $('pickCreate').disabled = picked.size < 2;
  }

  function render() {
    var box = $('pickList'), rows = visible();
    box.innerHTML = '';
    if (!rows.length) { box.innerHTML = '<div class="pick-empty">ไม่พบร้านที่ตรงกับที่ค้นหา</div>'; updateCount(); return; }
    var frag = document.createDocumentFragment();
    rows.forEach(function (r) {
      var id = Number(r.restaurant_id);
      var lb = document.createElement('label'); lb.className = 'pick-item';
      var cb = document.createElement('input'); cb.type = 'checkbox'; cb.checked = picked.has(id);
      cb.addEventListener('change', function () { cb.checked ? picked.add(id) : picked.delete(id); updateCount(); });
      var tx = document.createElement('span');
      var b = document.createElement('b'); b.textContent = r.restaurant_name;
      var sm = document.createElement('small'); sm.textContent = r.category_name + ' · งบ ≤ ' + r.budget_max + ' บาท';
      tx.appendChild(b); tx.appendChild(sm);
      lb.appendChild(cb); lb.appendChild(tx); frag.appendChild(lb);
    });
    box.appendChild(frag); updateCount();
  }

  async function open() {
    picked.clear(); $('pickSearch').value = '';
    show('picker');
    if (!loaded) {
      $('pickList').innerHTML = '<div class="pick-empty">กำลังโหลดรายชื่อร้าน…</div>';
      try {
        var data = await apiPost('vote.php', { action: 'list' });
        if (!data.success) throw new Error(data.message || 'โหลดไม่สำเร็จ');
        all = data.restaurants; loaded = true;
        var cats = []; all.forEach(function (r) { if (cats.indexOf(r.category_name) === -1) cats.push(r.category_name); });
        var sel = $('pickCat'); sel.innerHTML = '<option value="">ทุกหมวด</option>';
        cats.forEach(function (c) { var o = document.createElement('option'); o.value = c; o.textContent = c; sel.appendChild(o); });
      } catch (e) {
        $('pickList').innerHTML = '<div class="pick-empty">โหลดรายชื่อร้านไม่สำเร็จ ลองใหม่อีกครั้ง</div>';
        return;
      }
    }
    render();
  }

  async function create() {
    if (picked.size < 2) { toast('เลือกอย่างน้อย 2 ร้าน'); return; }
    var btn = $('pickCreate'); btn.disabled = true;
    try {
      var data = await apiPost('vote.php', { action: 'create', restaurant_ids: Array.from(picked) });
      if (!data.success) { toast(data.message); btn.disabled = false; return; }
      currentVoteGroup = { group_id: data.group_id, code: data.code };
      createdHistory.push({ code: data.code, group_id: data.group_id, time: new Date().toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' }) });
      renderVoteHistory();
      $('votePicker').classList.add('hidden');
      enterVoteRoom(data.code);
      toast('สร้างห้องแล้ว โค้ด: ' + data.code);
    } catch (e) { toast('สร้างห้องไม่สำเร็จ ลองใหม่อีกครั้ง'); }
    updateCount();
  }

  $('openPickerBtn').addEventListener('click', open);
  $('pickCancel').addEventListener('click', function () { show('lobby'); });
  $('pickCreate').addEventListener('click', create);
  $('pickSearch').addEventListener('input', render);
  $('pickCat').addEventListener('change', render);
  $('pickAll').addEventListener('click', function () { visible().forEach(function (r) { picked.add(Number(r.restaurant_id)); }); render(); });
  $('pickClear').addEventListener('click', function () { picked.clear(); render(); });
})();
