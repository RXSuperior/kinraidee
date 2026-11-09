/* สุ่มร้าน: ระยะที่เลือก = "ไม่เกิน" จริง ๆ (วัดตามถนนที่แสดงบนการ์ด)
 * เดิม random.php กรองด้วยระยะเส้นตรง แต่การ์ดแสดงระยะขับรถ จึงเจอร้าน 1.6 กม. ทั้งที่เลือก 1 กม.
 * ตอนนี้ถ้าร้านที่สุ่มได้ไกลเกินกำหนดตามถนน จะสุ่มใหม่ (ข้ามร้านเดิม) จนได้ร้านที่อยู่ในระยะ */
(function () {
  var btn = document.getElementById('randomBtn');
  if (!btn || typeof apiGet !== 'function') return;
  var fresh = btn.cloneNode(true);                 // ตัด handler เดิมของ script.js ออก
  btn.parentNode.replaceChild(fresh, btn);
  var MAX_TRY = 15;

  function say(box, msg) {
    box.style.borderStyle = 'solid'; box.style.borderColor = '#F0C8CE'; box.style.background = '#FFF6F8';
    box.innerHTML = '<p style="color:#B07078;font-size:.9rem;text-align:center;">' + msg + '</p>';
  }
  function limitText(m) { return m >= 1000 ? (m / 1000) + ' กม.' : m + ' ม.'; }

  fresh.addEventListener('click', async function () {
    var box = document.getElementById('randomResult'); if (!box) return;
    var maxD = parseFloat(document.getElementById('fDist').value) || 99999;
    var tried = [], r = null, d = 0, last = null;
    fresh.disabled = true;
    try {
      for (var i = 0; i < MAX_TRY; i++) {
        var params = new URLSearchParams({
          lat: USER.lat, lng: USER.lng,
          cat: document.getElementById('fCat').value,
          budget_max: document.getElementById('fBudget').value,
          country: document.getElementById('fCountry').value,
          atmosphere: document.getElementById('fAtmos').value,
          max_dist: maxD, exclude: tried.join(',')
        });
        var data = await apiGet('random.php?' + params.toString());
        if (!data.success) { last = data; break; }
        var c = data.restaurant;
        if (tried.indexOf(+c.restaurant_id) >= 0) break;   // เซิร์ฟเวอร์เก่าไม่รองรับ exclude
        tried.push(+c.restaurant_id);
        try {
          var rd = await fetchRoadDistances(USER, [c]);
          if (rd[0] !== null && rd[0] !== undefined) c.distance_m = rd[0];
        } catch (e) { console.error('OSRM Error (random):', e); }
        if (maxD >= 99999 || parseFloat(c.distance_m) <= maxD) { r = c; break; }
      }
    } finally { fresh.disabled = false; }

    if (!r) {
      say(box, tried.length ? 'ไม่พบร้านที่อยู่ไม่เกิน ' + limitText(maxD) + ' (วัดตามถนนจริง) ลองเพิ่มระยะดูนะ'
                            : ((last && last.message) || 'ไม่พบร้านที่ตรงเงื่อนไข'));
      return;
    }
    d = parseFloat(r.distance_m);
    box.style.borderStyle = 'solid'; box.style.borderColor = '#F8C8D2'; box.style.background = '#FFF6F8';
    box.innerHTML = '<div style="text-align:center;width:100%;">' +
      '<div style="font-size:1.25rem;font-weight:700;color:var(--accent-deep);margin-bottom:.5rem;">' + r.restaurant_name + '</div>' +
      '<div style="display:flex;flex-wrap:wrap;justify-content:center;gap:.4rem;margin-bottom:.6rem;">' +
        '<span class="meta-tag">' + r.category_name + '</span><span class="meta-tag">' + r.country + '</span><span class="meta-tag">' + r.atmosphere + '</span><span class="meta-tag">งบ ≤ ' + r.budget_max + ' บาท</span></div>' +
      '<p style="font-size:.85rem;color:#86686E;line-height:1.7;margin:0;">ระยะทาง: <strong>' + fmtDist(d) + '</strong> · เวลาทำการ: ' + r.open_time + ' - ' + r.close_time + '</p>' +
      contactHtml(r) +
      '<div><span class="status-open">เปิดให้บริการ</span></div>' +
      (r.map_url ? '<a class="map-link" href="' + r.map_url + '" target="_blank" rel="noopener">เปิดใน Google Maps</a>' : '') +
      '</div>';
  });
})();
