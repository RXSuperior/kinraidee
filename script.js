/* ========== KinRaiDee — เวอร์ชันเรียก API จริง (PHP + MySQL) + คำนวณระยะทางจริงตามถนน (OSRM) ========== */
const API_BASE = "api/";

// ตำแหน่งผู้ใช้ (ระบบจริงใช้ navigator.geolocation)
let USER = { lat: 13.2875, lng: 100.9244 };
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(
    function(pos) { USER.lat = pos.coords.latitude; USER.lng = pos.coords.longitude; loadRestaurants(); },
    function() { loadRestaurants(); }
  );
}

// voter_token สำหรับป้องกันโหวตซ้ำ
let voterToken = (function() {
  try {
    let t = localStorage.getItem("krd_token");
    if (!t) { t = "v" + Math.random().toString(36).slice(2, 10); localStorage.setItem("krd_token", t); }
    return t;
  } catch(e) { return "v_demo"; }
})();

let CATEGORIES = [];
let currentCat = "";
let currentVoteGroup = null;

// ========== ฟังก์ชันช่วยเหลือ ==========
function fmtDist(m) { return m < 1000 ? Math.round(m) + " ม." : (m/1000).toFixed(1) + " กม."; }
function distClass(m) { return m <= 500 ? "dist-near" : (m <= 1000 ? "dist-mid" : "dist-far"); }
function toast(msg) {
  let t = document.getElementById("toast");
  if (!t) return;
  t.textContent = msg; t.classList.add("show");
  setTimeout(function() { t.classList.remove("show"); }, 2200);
}
function contactHtml(r) {
  let html = '<div class="contact-row">';
  if (r.phone) html += '<a class="contact-link" href="tel:' + r.phone.replace(/-/g,'') + '">โทร ' + r.phone + '</a>';
  if (r.social) {
    let href = r.social;
    if (href.indexOf("http") !== 0) href = "https://" + href;
    html += '<a class="contact-link" href="' + href + '" target="_blank" rel="noopener">Social</a>';
  }
  html += '</div>';
  return (r.phone || r.social) ? html : "";
}
async function apiGet(url) {
  let res = await fetch(API_BASE + url);
  return res.json();
}
async function apiPost(url, data) {
  let res = await fetch(API_BASE + url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  });
  return res.json();
}

// ========== เริ่มต้น: โหลดหมวดหมู่และร้าน ==========
async function loadCategories() {
  CATEGORIES = await apiGet("categories.php");
  let fCat = document.getElementById("fCat"), aCat = document.getElementById("aCat");
  if (!fCat || !aCat) return;
  fCat.innerHTML = '<option value="">ทุกหมวด</option>';
  aCat.innerHTML = "";
  CATEGORIES.forEach(function(c) {
    let o = document.createElement("option"); o.value = c.category_name; o.textContent = c.category_name;
    fCat.appendChild(o); aCat.appendChild(o.cloneNode(true));
  });
  renderBrowseChips();
}

// ========== โหลดร้านอาหารพร้อมคำนวณระยะทางจริงตามถนน (OSRM) ==========
// ขอระยะทางขับรถจริงจาก OSRM (หน่วยเมตร) — ต้องใส่ annotations=distance
// ไม่งั้น OSRM จะคืนแค่ "เวลา" (durations) และ distances จะเป็น undefined
// เซิร์ฟเวอร์สาธารณะจำกัด 100 พิกัด/คำขอ จึงแบ่งเป็นชุดละ 90 ร้าน
async function fetchRoadDistances(origin, shops) {
  const CHUNK = 90;
  const out = new Array(shops.length).fill(null);
  for (let i = 0; i < shops.length; i += CHUNK) {
    const part = shops.slice(i, i + CHUNK);
    const coords = [origin.lng + "," + origin.lat]
      .concat(part.map(function(s) { return s.lng + "," + s.lat; }))
      .join(";");
    const url = "https://router.project-osrm.org/table/v1/driving/" + coords +
                "?sources=0&annotations=distance";
    const res = await fetch(url);
    if (!res.ok) throw new Error("OSRM HTTP " + res.status);
    const j = await res.json();
    if (j.code !== "Ok" || !j.distances || !j.distances[0]) throw new Error("OSRM: " + (j.code || "no distances"));
    // index 0 = ตำแหน่งผู้ใช้ (ระยะ 0) จึงเริ่มอ่านจาก index 1
    j.distances[0].slice(1).forEach(function(d, k) { out[i + k] = d; });
  }
  return out;
}

let loadSeq = 0; // กันผลลัพธ์เก่าทับของใหม่ (เช่น โหลดซ้ำตอนได้ตำแหน่ง GPS)
async function loadRestaurants() {
  const seq = ++loadSeq;
  const statusDiv = document.getElementById("status") || { innerHTML: "" };
  statusDiv.innerHTML = "⏳ กำลังคำนวณระยะทางตามถนนจริง...";

  let data;
  try {
    data = await apiGet("restaurants.php?lat=" + USER.lat + "&lng=" + USER.lng + "&cat=" + encodeURIComponent(currentCat));
  } catch (e) {
    console.error("API Error:", e);
    if (seq === loadSeq) statusDiv.innerHTML = "โหลดข้อมูลร้านไม่สำเร็จ";
    return;
  }
  if (seq !== loadSeq) return;

  if (!data || data.length === 0) {
    renderBrowseList([]);
    statusDiv.innerHTML = "ไม่พบร้านในหมวดนี้";
    return;
  }

  // แสดงรายการก่อน (ระยะเส้นตรงจากฐานข้อมูล) แล้วค่อยอัปเดตเป็นระยะจริงตามถนน
  renderBrowseList(data);

  try {
    const roadDist = await fetchRoadDistances(USER, data);
    if (seq !== loadSeq) return;

    data.forEach(function(shop, i) {
      if (roadDist[i] !== null && roadDist[i] !== undefined) {
        shop.distance_m = roadDist[i];
        shop.road = true;
      }
    });
    data.sort(function(a, b) { return parseFloat(a.distance_m) - parseFloat(b.distance_m); });

    renderBrowseList(data);
    statusDiv.innerHTML = "✅ พบร้านอาหาร " + data.length + " แห่ง (เรียงตามระยะทางจริงตามถนน)";
  } catch (error) {
    console.error("OSRM Error:", error);
    if (seq === loadSeq) statusDiv.innerHTML = "พบร้านอาหาร " + data.length + " แห่ง (แสดงระยะเส้นตรง — คำนวณระยะตามถนนไม่ได้ชั่วคราว)";
  }
}

// ========== เปลี่ยนแท็บ ==========
document.querySelectorAll(".tab-btn").forEach(function(b) {
  b.addEventListener("click", function() {
    document.querySelectorAll(".tab-btn").forEach(function(x) { x.classList.remove("active"); });
    b.classList.add("active");
    document.querySelectorAll(".tab-panel").forEach(function(p) { p.classList.add("hidden"); });
    let el = document.getElementById("tab-" + b.dataset.tab);
    if (el) el.classList.remove("hidden");
  });
});

// ========== ฟีเจอร์สุ่มร้าน ==========
let randomBtn = document.getElementById("randomBtn");
if (randomBtn) {
  randomBtn.addEventListener("click", async function() {
    let box = document.getElementById("randomResult");
    if (!box) return;
    let params = new URLSearchParams({
      lat: USER.lat, lng: USER.lng,
      cat: document.getElementById("fCat").value,
      budget_max: document.getElementById("fBudget").value,
      country: document.getElementById("fCountry").value,
      atmosphere: document.getElementById("fAtmos").value,
      max_dist: document.getElementById("fDist").value
    });
    let data = await apiGet("random.php?" + params.toString());
    if (!data.success) {
      box.style.borderStyle = "solid"; box.style.borderColor = "#F0C8CE"; box.style.background = "#FFF6F8";
      box.innerHTML = '<p style="color:#B07078;font-size:.9rem;text-align:center;">' + data.message + '</p>';
      return;
    }
    let r = data.restaurant;
    // random.php คืนระยะเส้นตรง — แทนด้วยระยะตามถนนจริงจาก OSRM (ถ้าดึงไม่ได้ใช้ค่าเดิม)
    try {
      const rd = await fetchRoadDistances(USER, [r]);
      if (rd[0] !== null && rd[0] !== undefined) r.distance_m = rd[0];
    } catch (e) { console.error("OSRM Error (random):", e); }
    let d = parseFloat(r.distance_m);
    box.style.borderStyle = "solid"; box.style.borderColor = "#F8C8D2"; box.style.background = "#FFF6F8";
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
}

// ========== ฟีเจอร์เลือกเอง ==========
function renderBrowseChips() {
  let chipsBox = document.getElementById("browseChips");
  if (!chipsBox) return;
  chipsBox.innerHTML = "";
  let all = [{category_name: ""}].concat(CATEGORIES);
  all.forEach(function(c) {
    let b = document.createElement("button");
    b.className = "chip" + (c.category_name === currentCat ? " active" : "");
    b.textContent = c.category_name === "" ? "ทั้งหมด" : c.category_name;
    b.addEventListener("click", function() { currentCat = c.category_name; renderBrowseChips(); loadRestaurants(); });
    chipsBox.appendChild(b);
  });
}

function renderBrowseList(arr) {
  let listBox = document.getElementById("browseList");
  if (!listBox) return;
  listBox.innerHTML = "";
  if (!arr || arr.length === 0) { 
    listBox.innerHTML = '<p style="text-align:center;color:var(--muted);font-size:.85rem;padding:2rem 0;">ไม่มีร้านในหมวดนี้</p>'; 
    return; 
  }
  
  arr.forEach(function(r) {
    let d = parseFloat(r.distance_m);
    let openNow = (parseInt(r.is_open) === 1);
    
    let card = document.createElement("div");
    card.className = "rcard" + (openNow ? "" : " closed");
    card.innerHTML = '<div class="flex justify-between items-start gap-2">' +
      '<div style="flex:1;min-width:0;"><div style="font-weight:700;font-size:.98rem;color:var(--text);">' + r.restaurant_name + '</div>' +
      '<div style="font-size:.78rem;color:var(--muted);margin-top:.15rem;">' + r.category_name + ' · ' + r.country + ' · งบ ≤ ' + r.budget_max + ' บาท</div>' +
      '<div style="font-size:.75rem;color:var(--muted);margin-top:.2rem;">เวลา ' + r.open_time + ' - ' + r.close_time + (r.closed_day ? ' · หยุด ' + r.closed_day : '') + '</div>' +
      contactHtml(r) +
      (r.map_url ? '<a class="map-link-sm" href="' + r.map_url + '" target="_blank" rel="noopener">ดูใน Google Maps</a>' : '') +
      '</div>' +
      '<div style="text-align:right;flex-shrink:0;"><span class="dist-pill ' + distClass(d) + '">🚗 ' + fmtDist(d) + '</span>' +
      '<div style="margin-top:.4rem;">' + (openNow ? '<span class="status-open">เปิด</span>' : '<span class="status-closed">ปิด</span>') + '</div></div></div>';
    listBox.appendChild(card);
  });
}

// ========== ฟีเจอร์เพิ่มร้าน ==========
const DAYS = ["จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์","อาทิตย์"];
let closedChipsBox = document.getElementById("aClosedChips");
if (closedChipsBox) {
  DAYS.forEach(function(d) {
    let b = document.createElement("button");
    b.type = "button"; b.className = "day-chip"; b.textContent = d; b.dataset.day = d;
    b.addEventListener("click", function() { b.classList.toggle("active"); });
    closedChipsBox.appendChild(b);
  });
}

let submitAddBtn = document.getElementById("submitAddBtn");
if (submitAddBtn) {
  submitAddBtn.addEventListener("click", async function() {
    let name = document.getElementById("aName").value.trim();
    let budget = parseFloat(document.getElementById("aBudget").value) || 0;
    let mapUrl = document.getElementById("aMap").value.trim();
    let phone = document.getElementById("aPhone").value.trim();
    let social = document.getElementById("aSocial").value.trim();
    if (!name || !budget || !mapUrl) { toast("กรอกชื่อร้าน งบประมาณ และลิงค์ Google Maps ให้ครบ"); return; }
    let closed = [];
    document.querySelectorAll("#aClosedChips .day-chip.active").forEach(function(b) { closed.push(b.dataset.day); });
    let data = await apiPost("add_restaurant.php", {
      name: name, category_name: document.getElementById("aCat").value,
      country: document.getElementById("aCountry").value, atmosphere: document.getElementById("aAtmos").value,
      budget_max: budget, address: document.getElementById("aAddr").value,
      phone: phone, social: social, map_url: mapUrl,
      open_time: document.getElementById("aOpen").value, close_time: document.getElementById("aClose").value,
      closed_day: closed.join(",")
    });
    toast(data.message);
    if (data.success) {
      ["aName","aBudget","aMap","aAddr","aPhone","aSocial"].forEach(function(id) {
        let e = document.getElementById(id); if (e) e.value = "";
      });
      document.querySelectorAll("#aClosedChips .day-chip.active").forEach(function(b) { b.classList.remove("active"); });
    }
  });
}

// ========== ฟีเจอร์แอดมิน ==========
let adminLoginBtn = document.getElementById("adminLoginBtn");
if (adminLoginBtn) {
  adminLoginBtn.addEventListener("click", async function() {
    let data = await apiPost("admin.php", {
      action: "login",
      username: document.getElementById("adminUser").value.trim(),
      password: document.getElementById("adminPass").value
    });
    if (data.success) {
      document.getElementById("adminLogin").classList.add("hidden");
      document.getElementById("adminPanel").classList.remove("hidden");
      loadPending(); loadCatList();
    } else { toast(data.message); }
  });
}

let adminLogoutBtn = document.getElementById("adminLogoutBtn");
if (adminLogoutBtn) {
  adminLogoutBtn.addEventListener("click", async function() {
    await apiPost("admin.php", { action: "logout" });
    document.getElementById("adminPanel").classList.add("hidden");
    document.getElementById("adminLogin").classList.remove("hidden");
  });
}

let addCatBtn = document.getElementById("addCatBtn");
if (addCatBtn) {
  addCatBtn.addEventListener("click", async function() {
    let v = document.getElementById("newCat").value.trim();
    if (!v) { toast("ใส่ชื่อหมวดหมู่"); return; }
    let data = await apiPost("admin.php", { action: "add_cat", category_name: v });
    toast(data.message);
    if (data.success) { document.getElementById("newCat").value = ""; loadCategories(); loadCatList(); }
  });
}

async function loadPending() {
  let data = await apiPost("admin.php", { action: "pending_list" });
  let box = document.getElementById("pendingList");
  if (!box) return;
  if (!data.data || data.data.length === 0) { box.innerHTML = '<p style="font-size:.82rem;color:var(--muted);padding:.5rem 0;">ไม่มีร้านรออนุมัติ</p>'; return; }
  box.innerHTML = "";
  data.data.forEach(function(r) {
    let row = document.createElement("div");
    row.style.cssText = "display:flex;justify-content:space-between;align-items:center;padding:.6rem .8rem;border:1px solid var(--border);border-radius:10px;background:#FFFAF8;";
    row.innerHTML = '<div style="font-size:.85rem;font-weight:600;">' + r.restaurant_name + ' <span style="color:var(--muted);font-weight:400;font-size:.75rem;">(' + r.category_name + ')</span></div>';
    let btns = document.createElement("div"); btns.style.cssText = "display:flex;gap:.4rem;flex-shrink:0;";
    let ap = document.createElement("button"); ap.className = "btn-ghost"; ap.style.cssText = "font-size:.75rem;padding:.3rem .7rem;color:var(--green);border-color:#C8E6C9;"; ap.textContent = "อนุมัติ";
    ap.addEventListener("click", async function() {
      await apiPost("admin.php", { action: "approve", restaurant_id: r.restaurant_id });
      toast("อนุมัติ " + r.restaurant_name); loadPending(); loadRestaurants();
    });
    let dl = document.createElement("button"); dl.className = "btn-ghost"; dl.style.cssText = "font-size:.75rem;padding:.3rem .7rem;color:#B71C1C;border-color:#FFCDD2;"; dl.textContent = "ลบ";
    dl.addEventListener("click", async function() {
      await apiPost("admin.php", { action: "delete", restaurant_id: r.restaurant_id });
      toast("ลบ " + r.restaurant_name); loadPending();
    });
    btns.appendChild(ap); btns.appendChild(dl); row.appendChild(btns);
    box.appendChild(row);
  });
}

async function loadCatList() {
  let box = document.getElementById("catList"); 
  if (!box) return;
  box.innerHTML = "";
  CATEGORIES.forEach(function(c) {
    let tag = document.createElement("span");
    tag.style.cssText = "display:inline-flex;align-items:center;gap:.4rem;background:var(--accent-soft);color:var(--accent-deep);padding:.3rem .7rem;border-radius:999px;font-size:.78rem;font-weight:600;";
    tag.innerHTML = c.category_name + ' <span style="cursor:pointer;font-weight:700;color:#B71C1C;">×</span>';
    tag.querySelector("span").addEventListener("click", async function() {
      await apiPost("admin.php", { action: "del_cat", category_name: c.category_name });
      toast("ลบหมวดหมู่: " + c.category_name); loadCategories(); loadCatList();
    });
    box.appendChild(tag);
  });
}

// ========== ฟีเจอร์โหวตทีม ==========
let createdHistory = [];
let createGroupBtn = document.getElementById("createGroupBtn");
if (createGroupBtn) {
  createGroupBtn.addEventListener("click", async function() {
    let data = await apiPost("vote.php", { action: "create" });
    if (!data.success) { toast(data.message); return; }
    currentVoteGroup = { group_id: data.group_id, code: data.code };
    createdHistory.push({ code: data.code, group_id: data.group_id, time: new Date().toLocaleTimeString('th-TH', {hour:'2-digit',minute:'2-digit'}) });
    renderVoteHistory(); enterVoteRoom(data.code);
    toast("สร้างห้องแล้ว โค้ด: " + data.code);
  });
}

let joinGroupBtn = document.getElementById("joinGroupBtn");
if (joinGroupBtn) {
  joinGroupBtn.addEventListener("click", async function() {
    let codeInput = document.getElementById("joinCode");
    if (!codeInput) return;
    let code = codeInput.value.trim().toUpperCase();
    if (!code || code.length !== 4) { toast("ใส่โค้ด 4 ตัวอักษร"); return; }
    let data = await apiPost("vote.php", { action: "join", code: code, voter_token: voterToken });
    if (!data.success) { toast(data.message); return; }
    currentVoteGroup = { group_id: data.group_id, code: data.code, options: data.options };
    enterVoteRoom(data.code);
  });
}

function enterVoteRoom(code) {
  let voteLobby = document.getElementById("voteLobby");
  let voteRoom = document.getElementById("voteRoom");
  let roomCode = document.getElementById("roomCode");
  let announceResult = document.getElementById("announceResult");
  if (voteLobby) voteLobby.classList.add("hidden");
  if (voteRoom) voteRoom.classList.remove("hidden");
  if (roomCode) roomCode.textContent = code;
  if (announceResult) announceResult.classList.add("hidden");
  renderVoteOptions();
}

async function renderVoteOptions() {
  if (!currentVoteGroup) return;
  let data = await apiPost("vote.php", { action: "join", code: currentVoteGroup.code, voter_token: voterToken });
  let optsBox = document.getElementById("voteOptions"), chartBox = document.getElementById("voteChart");
  if (!optsBox || !chartBox) return;
  optsBox.innerHTML = ""; chartBox.innerHTML = "";
  if (data.already_voted) {
    optsBox.classList.add("hidden"); chartBox.classList.remove("hidden");
    renderVoteChart();
  } else {
    optsBox.classList.remove("hidden"); chartBox.classList.add("hidden");
    data.options.forEach(function(o) {
      let b = document.createElement("button");
      b.className = "btn-ghost"; b.style.width = "100%"; b.style.textAlign = "left"; b.style.padding = ".7rem 1rem";
      b.textContent = o.restaurant_name + " · งบ ≤ " + o.budget_max + " บาท";
      b.addEventListener("click", async function() {
        let res = await apiPost("vote.php", { action: "cast", group_id: currentVoteGroup.group_id, option_id: o.option_id, voter_token: voterToken });
        toast(res.message);
        if (res.success) renderVoteOptions();
      });
      optsBox.appendChild(b);
    });
  }
}

async function renderVoteChart() {
  if (!currentVoteGroup) return;
  let data = await apiPost("vote.php", { action: "result", group_id: currentVoteGroup.group_id });
  let chartBox = document.getElementById("voteChart");
  if (!chartBox) return;
  let max = 1; data.results.forEach(function(r) { if (parseInt(r.vote_count) > max) max = parseInt(r.vote_count); });
  chartBox.innerHTML = '<div style="font-size:.8rem;color:var(--muted);font-weight:600;margin-bottom:.3rem;">ผลโหวตเรียลไทม์ (ไม่แสดงชื่อผู้โหวต)</div>';
  data.results.forEach(function(r) {
    let v = parseInt(r.vote_count); let pct = Math.round(v / max * 100);
    let row = document.createElement("div");
    row.innerHTML = '<div class="flex justify-between" style="font-size:.8rem;margin-bottom:.2rem;"><span style="color:var(--text);font-weight:600;">' + r.restaurant_name + '</span><span style="color:var(--accent-deep);font-weight:700;">' + v + ' คะแนน</span></div>' +
      '<div style="background:#FBE9ED;border-radius:6px;overflow:hidden;"><div class="vote-bar" style="width:' + pct + '%;"></div></div>';
    chartBox.appendChild(row);
  });
}

let leaveRoomBtn = document.getElementById("leaveRoomBtn");
if (leaveRoomBtn) {
  leaveRoomBtn.addEventListener("click", function() {
    currentVoteGroup = null;
    let voteRoom = document.getElementById("voteRoom");
    let voteLobby = document.getElementById("voteLobby");
    let joinCode = document.getElementById("joinCode");
    if (voteRoom) voteRoom.classList.add("hidden");
    if (voteLobby) voteLobby.classList.remove("hidden");
    if (joinCode) joinCode.value = "";
  });
}

let announceBtn = document.getElementById("announceBtn");
if (announceBtn) {
  announceBtn.addEventListener("click", async function() {
    if (!currentVoteGroup) return;
    let data = await apiPost("vote.php", { action: "announce", group_id: currentVoteGroup.group_id });
    let w = data.winner;
    let annBox = document.getElementById("announceResult");
    if (!annBox) return;
    annBox.classList.remove("hidden");
    annBox.innerHTML = '<div style="font-size:.8rem;color:var(--muted);">ร้านที่ได้รับคะแนนมากที่สุด</div>' +
      '<div style="font-size:1.3rem;font-weight:700;color:var(--accent-deep);margin:.3rem 0;">' + w.restaurant_name + '</div>' +
      '<div style="font-size:.85rem;color:#86686E;">ได้ ' + w.vote_count + ' คะแนน</div>' +
      (data.note ? '<div style="font-size:.78rem;color:var(--muted);margin-top:.3rem;">' + data.note + '</div>' : '');
    renderVoteChart();
  });
}

function renderVoteHistory() {
  let wrap = document.getElementById("voteHistoryWrap"), box = document.getElementById("voteHistory");
  if (!wrap || !box) return;
  if (createdHistory.length === 0) { wrap.classList.add("hidden"); return; }
  wrap.classList.remove("hidden"); box.innerHTML = "";
  createdHistory.slice().reverse().forEach(function(h) {
    let row = document.createElement("div"); row.className = "hist-item";
    row.innerHTML = '<div style="display:flex;align-items:center;gap:.6rem;min-width:0;">' +
      '<span style="background:var(--accent);color:#fff;padding:.2rem .6rem;border-radius:6px;font-weight:700;letter-spacing:2px;font-size:.85rem;flex-shrink:0;">' + h.code + '</span>' +
      '<div style="font-size:.78rem;color:var(--muted);">สร้าง ' + h.time + '</div></div>';
    let btn = document.createElement("button"); btn.className = "btn-ghost";
    btn.style.cssText = "font-size:.75rem;padding:.3rem .7rem;flex-shrink:0;"; btn.textContent = "เข้าห้อง";
    btn.addEventListener("click", async function() {
      let data = await apiPost("vote.php", { action: "join", code: h.code, voter_token: voterToken });
      if (data.success) { currentVoteGroup = { group_id: data.group_id, code: data.code }; enterVoteRoom(data.code); }
      else toast(data.message);
    });
    row.appendChild(btn); box.appendChild(row);
  });
}

// ========== เริ่มต้นโหลดข้อมูล ==========
loadCategories();
loadRestaurants();