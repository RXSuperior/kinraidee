<html style="margin:0;padding:0;">
<script src="https://cdn.tailwindcss.com"></script>
<div style="background:#fefcf8;min-height:100vh;font-family:Tahoma,sans-serif;color:#0f172a;">
<div style="max-width:560px;margin:0 auto;padding:14px;">

  <div style="text-align:center;margin-bottom:10px;">
    <h1 style="font-size:18px;font-weight:bold;margin:0 0 2px;">กินไรดี <span style="color:#ea580c;">KinRaiDee</span></h1>
    <p id="jsStatus" style="font-size:10px;color:#64748b;margin:0;">กำลังโหลด...</p>
  </div>

  <div style="display:flex;flex-wrap:wrap;gap:2px;background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:3px;margin-bottom:10px;">
    <button data-tab="random" class="tabBtn" style="flex:1;min-width:60px;padding:7px 2px;border-radius:7px;font-weight:bold;font-size:10px;border:none;cursor:pointer;background:rgba(234,88,12,.14);color:#0f172a;border-bottom:3px solid #ea580c;">สุ่มร้าน</button>
    <button data-tab="browse" class="tabBtn" style="flex:1;min-width:60px;padding:7px 2px;border-radius:7px;font-weight:bold;font-size:10px;border:none;cursor:pointer;background:transparent;color:#64748b;">เลือกเอง</button>
    <button data-tab="vote" class="tabBtn" style="flex:1;min-width:60px;padding:7px 2px;border-radius:7px;font-weight:bold;font-size:10px;border:none;cursor:pointer;background:transparent;color:#64748b;">โหวตทีม</button>
    <button data-tab="add" class="tabBtn" style="flex:1;min-width:60px;padding:7px 2px;border-radius:7px;font-weight:bold;font-size:10px;border:none;cursor:pointer;background:transparent;color:#64748b;">เพิ่มร้าน</button>
    <button data-tab="admin" class="tabBtn" style="flex:1;min-width:60px;padding:7px 2px;border-radius:7px;font-weight:bold;font-size:10px;border:none;cursor:pointer;background:transparent;color:#64748b;">จัดการ (AD)</button>
  </div>

  <!-- TAB: RANDOM -->
  <div id="vRandom" style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;">
    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
      <div style="flex:1;min-width:110px;"><label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">หมวดหมู่</label>
        <select id="rCat" style="width:100%;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;font-weight:bold;"><option value="all">ทุกหมวด</option><option>อาหารตามสั่ง</option><option>ก๋วยเตี๋ยว</option><option>ส้มตำ</option><option>ข้าวแกง</option><option>ปิ้งย่าง</option><option>ฮาลาล</option></select></div>
      <div style="flex:1;min-width:110px;"><label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">งบประมาณ</label>
        <select id="rBudget" style="width:100%;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;font-weight:bold;"><option value="9999">ไม่จำกัด</option><option value="50">≤ 50 บาท</option><option value="100">≤ 100 บาท</option><option value="200">≤ 200 บาท</option><option value="500">≤ 500 บาท</option></select></div>
    </div>
    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
      <div style="flex:1;min-width:110px;"><label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">ประเทศอาหาร</label>
        <select id="rCuisine" style="width:100%;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;font-weight:bold;"><option value="all">ทุกประเทศ</option><option>ไทย</option><option>ญี่ปุ่น</option><option>จีน</option><option>อาหรับ (ฮาลาล)</option><option>อิตาลี</option></select></div>
      <div style="flex:1;min-width:110px;"><label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">บรรยากาศ</label>
        <select id="rAtm" style="width:100%;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;font-weight:bold;"><option value="all">ทั้งหมด</option><option>แอร์</option><option>พัดลม</option><option>ทั้งคู่</option></select></div>
    </div>
    <div style="margin-bottom:10px;"><label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">ระยะทางไม่เกิน</label>
      <select id="rDist" style="width:100%;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;font-weight:bold;"><option value="99999">ทุกระยะ</option><option value="500">500 ม.</option><option value="1000">1 กม.</option><option value="2000">2 กม.</option></select></div>
    <button id="randomBtn" style="width:100%;padding:12px;border-radius:9px;background:#ea580c;color:#fff;font-size:14px;font-weight:bold;border:none;cursor:pointer;">สุ่มร้านเลย!</button>
    <div id="rPh" style="margin-top:10px;text-align:center;padding:16px;border:2px dashed #e2e8f0;border-radius:9px;color:#64748b;font-size:11px;">กดปุ่มเพื่อสุ่มร้านตามเงื่อนไขทั้งหมด</div>
    <div id="rCard" style="display:none;margin-top:10px;border:2px solid #ea580c;border-radius:10px;padding:12px;background:rgba(234,88,12,.07);">
      <p style="font-size:9px;color:#ea580c;font-weight:bold;letter-spacing:.1em;margin:0;">วันนี้กินที่</p>
      <p id="rName" style="font-size:16px;font-weight:bold;margin:2px 0 5px;"></p>
      <div style="font-size:10px;flex-wrap:wrap;display:flex;gap:3px;"><span id="rCatName" style="padding:2px 7px;border-radius:20px;background:rgba(22,163,74,.14);color:#16a34a;font-weight:bold;border:1px solid #16a34a;"></span><span id="rBudgetText" style="padding:2px 7px;border-radius:20px;background:rgba(234,88,12,.14);color:#ea580c;font-weight:bold;border:1px solid #ea580c;"></span><span id="rCuisineText" style="padding:2px 7px;border-radius:20px;background:rgba(234,88,12,.14);color:#ea580c;font-weight:bold;border:1px solid #ea580c;"></span><span id="rAtmText" style="padding:2px 7px;border-radius:20px;background:rgba(234,88,12,.14);color:#ea580c;font-weight:bold;border:1px solid #ea580c;"></span></div>
      <p id="rTime" style="font-size:10px;color:#64748b;margin-top:5px;font-weight:bold;"></p>
    </div>
  </div>

  <!-- TAB: BROWSE -->
  <div id="vBrowse" style="display:none;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;">
    <div id="bSel" style="display:none;margin-bottom:8px;padding:9px;border-radius:9px;border:2px solid #16a34a;background:rgba(22,163,74,.1);"><p style="font-size:9px;color:#16a34a;font-weight:bold;letter-spacing:.1em;margin:0;">คุณเลือกร้าน</p><p id="bSelName" style="font-weight:bold;font-size:12px;margin:2px 0 0;"></p></div>
    <div id="chipBar" style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:8px;"></div>
    <div id="bGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:6px;"></div>
  </div>

  <!-- TAB: VOTE -->
  <div id="vVote" style="display:none;">
    <div id="vJoin">
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;margin-bottom:8px;">
        <p style="font-size:11px;font-weight:bold;margin:0 0 6px;">1) เข้าร่วมกลุ่ม (ใส่โค้ดที่เพื่อนแชร์)</p>
        <input id="codeInput" type="text" maxlength="4" placeholder="KR7X" style="width:100%;padding:10px;border-radius:9px;border:1px solid #e2e8f0;font-size:16px;font-weight:bold;text-align:center;letter-spacing:.3em;box-sizing:border-box;margin-bottom:6px;text-transform:uppercase;">
        <button id="joinBtn" style="width:100%;padding:10px;border-radius:9px;background:#ea580c;color:#fff;font-size:12px;font-weight:bold;border:none;cursor:pointer;">เข้าร่วมกลุ่ม</button>
        <p id="joinErr" style="display:none;margin:6px 0 0;font-size:10px;color:#dc2626;font-weight:bold;text-align:center;">ไม่พบโค้ด (ทดลอง: KR7X)</p>
      </div>
      <div style="text-align:center;font-size:9px;color:#64748b;margin:6px 0;">—— หรือ ——</div>
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;">
        <p style="font-size:11px;font-weight:bold;margin:0 0 6px;">2) สร้างกลุ่มใหม่ แล้วแชร์โค้ดให้เพื่อน</p>
        <input id="groupNameInput" type="text" placeholder="ชื่อกลุ่ม เช่น ทีมกินข้าววันศุกร์" style="width:100%;padding:9px;border-radius:9px;border:1px solid #e2e8f0;font-size:11px;box-sizing:border-box;margin-bottom:6px;">
        <button id="createBtn" style="width:100%;padding:10px;border-radius:9px;background:#16a34a;color:#fff;font-size:12px;font-weight:bold;border:none;cursor:pointer;">สร้างกลุ่มใหม่</button>
      </div>
      <p style="text-align:center;font-size:9px;color:#64748b;margin-top:8px;">โหวตแบบไม่ระบุตัวตน — ระบบไม่แสดงชื่อคนโหวต</p>
    </div>
    <div id="vVoteInner" style="display:none;">
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;margin-bottom:8px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
          <div><p style="font-size:9px;color:#64748b;font-weight:bold;margin:0;">กลุ่ม</p><p id="vgName" style="font-weight:bold;font-size:13px;margin:1px 0 0;"></p></div>
          <div style="text-align:right;"><p style="font-size:9px;color:#64748b;font-weight:bold;margin:0;">โค้ด</p><button id="copyCode" style="background:rgba(234,88,12,.14);border:1px solid #ea580c;color:#ea580c;font-family:monospace;font-size:14px;font-weight:bold;padding:3px 10px;border-radius:7px;cursor:pointer;letter-spacing:.1em;margin-top:1px;"></button></div>
        </div>
        <select id="vtRest" style="width:100%;padding:9px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;box-sizing:border-box;margin-bottom:7px;"><option value="1">ร้านเจ๊ไฝ อาหารตามสั่ง</option><option value="2">ครัวคุณต๋อง</option><option value="3">ข้าวมันไก่ฮาลาล</option><option value="4">ราเมนซางโตะ (ญี่ปุ่น)</option><option value="5">ส้มตำเจ๊แดง</option><option value="6">ปิ้งย่างบ้านๆ</option></select>
        <button id="vtSubmit" style="width:100%;padding:10px;border-radius:9px;background:#ea580c;color:#fff;font-size:13px;font-weight:bold;border:none;cursor:pointer;">โหวตเลย! (ไม่ระบุตัวตน)</button>
      </div>
      <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:7px;"><p style="font-size:12px;font-weight:bold;margin:0;">ผลคะแนนสด (ไม่แสดงชื่อคนโหวต)</p><span id="vtTotal" style="font-size:10px;font-weight:bold;color:#ea580c;background:rgba(234,88,12,.14);padding:2px 8px;border-radius:20px;">0 คะแนน</span></div>
        <div id="vtResults"></div>
        <div style="display:flex;gap:6px;margin-top:10px;"><button id="vtAnnounce" style="flex:1;padding:8px;border-radius:7px;background:#16a34a;color:#fff;border:none;font-size:10px;font-weight:bold;cursor:pointer;">ประกาศผล</button><button id="leaveBtn" style="flex:1;padding:8px;border-radius:7px;background:#fff;color:#64748b;border:1px solid #e2e8f0;font-size:10px;font-weight:bold;cursor:pointer;">ออกจากกลุ่ม</button></div>
      </div>
    </div>
  </div>

  <!-- TAB: ADD (มีที่อยู่ + พิกัดครบ) -->
  <div id="vAdd" style="display:none;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;">
    <p style="font-size:10px;color:#16a34a;font-weight:bold;margin:0 0 8px;text-align:center;">ทุกคนเพิ่มร้านได้ ไม่ต้องล็อกอิน · มีช่องพิกัดครบ</p>
    <label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">ชื่อร้าน *</label>
    <input id="fName" type="text" placeholder="ชื่อร้าน" style="width:100%;padding:9px;border-radius:7px;border:1px solid #e2e8f0;font-size:12px;box-sizing:border-box;margin-bottom:7px;">
    <div style="display:flex;gap:6px;margin-bottom:7px;">
      <div style="flex:1;"><label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">หมวดหมู่</label><select id="fCat" style="width:100%;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;"><option>อาหารตามสั่ง</option><option>ก๋วยเตี๋ยว</option><option>ส้มตำ</option><option>ข้าวแกง</option><option>ปิ้งย่าง</option><option>ฮาลาล</option></select></div>
      <div style="flex:1;"><label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">ประเทศอาหาร</label><select id="fCuisine" style="width:100%;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;"><option>ไทย</option><option>ญี่ปุ่น</option><option>จีน</option><option>อาหรับ (ฮาลาล)</option><option>อิตาลี</option></select></div>
    </div>
    <label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:3px;">บรรยากาศ</label>
    <div style="display:flex;gap:5px;margin-bottom:7px;"><label style="flex:1;display:flex;align-items:center;gap:3px;padding:7px;border:1px solid #e2e8f0;border-radius:7px;font-size:10px;font-weight:bold;cursor:pointer;"><input type="radio" name="atm" value="แอร์" checked> แอร์</label><label style="flex:1;display:flex;align-items:center;gap:3px;padding:7px;border:1px solid #e2e8f0;border-radius:7px;font-size:10px;font-weight:bold;cursor:pointer;"><input type="radio" name="atm" value="พัดลม"> พัดลม</label><label style="flex:1;display:flex;align-items:center;gap:3px;padding:7px;border:1px solid #e2e8f0;border-radius:7px;font-size:10px;font-weight:bold;cursor:pointer;"><input type="radio" name="atm" value="ทั้งคู่"> ทั้งคู่</label></div>
    <label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">งบประมาณต่อหัว (บาท)</label>
    <div style="display:flex;gap:6px;align-items:center;margin-bottom:7px;"><input id="fBMin" type="number" value="40" style="flex:1;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:12px;box-sizing:border-box;"><span style="font-size:11px;color:#64748b;font-weight:bold;">–</span><input id="fBMax" type="number" value="100" style="flex:1;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:12px;box-sizing:border-box;"><span style="font-size:11px;color:#64748b;font-weight:bold;">บาท</span></div>
    <label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">ที่อยู่</label>
    <input id="fAddr" type="text" placeholder="เช่น ซอยสุขุมวิท 11 แยก 2" style="width:100%;padding:9px;border-radius:7px;border:1px solid #e2e8f0;font-size:12px;box-sizing:border-box;margin-bottom:7px;">
    <label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">พิกัด (ละติจูด , ลองจิจูด) * — copy จาก Google Maps</label>
    <div style="display:flex;gap:6px;margin-bottom:3px;">
      <input id="fLat" type="text" placeholder="13.7563" style="flex:1;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;box-sizing:border-box;font-family:monospace;">
      <input id="fLng" type="text" placeholder="100.5018" style="flex:1;padding:8px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;box-sizing:border-box;font-family:monospace;">
    </div>
    <p style="font-size:9px;color:#64748b;margin:0 0 7px;">วิธีหา: Google Maps → คลิกขวาที่ร้าน → "What's here?" → copy ตัวเลข 2 จุดมาใส่</p>
    <div style="display:flex;gap:6px;margin-bottom:7px;"><div style="flex:1;"><label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">เวลาเปิด</label><input id="fOpen" type="time" value="08:00" style="width:100%;padding:7px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;box-sizing:border-box;"></div><div style="flex:1;"><label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">เวลาปิด</label><input id="fClose" type="time" value="20:00" style="width:100%;padding:7px;border-radius:7px;border:1px solid #e2e8f0;font-size:11px;box-sizing:border-box;"></div></div>
    <label style="font-size:10px;font-weight:bold;color:#64748b;display:block;margin-bottom:2px;">ชื่อคุณ (ไม่บังคับ)</label>
    <input id="fBy" type="text" placeholder="ปล่อยว่างได้" style="width:100%;padding:9px;border-radius:7px;border:1px solid #e2e8f0;font-size:12px;box-sizing:border-box;margin-bottom:10px;">
    <button id="addBtn" style="width:100%;padding:11px;border-radius:9px;background:#ea580c;color:#fff;font-size:13px;font-weight:bold;border:none;cursor:pointer;">เพิ่มร้านนี้เลย!</button>
    <div id="addSuccess" style="display:none;margin-top:8px;padding:10px;border-radius:9px;border:2px solid #16a34a;background:rgba(22,163,74,.1);text-align:center;"><p id="addSuccessText" style="font-size:10px;font-weight:bold;color:#16a34a;margin:0;line-height:1.6;"></p></div>
  </div>

  <!-- TAB: ADMIN -->
  <div id="vAdmin" style="display:none;">
    <div id="adminLogin" style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:18px;text-align:center;">
      <p style="font-size:13px;font-weight:bold;margin:0 0 2px;">จัดการข้อมูลร้าน</p>
      <p style="font-size:10px;color:#dc2626;font-weight:bold;margin:0 0 12px;">เฉพาะแอดมินเท่านั้น — กรุณาล็อกอิน</p>
      <input id="adminUser" type="text" placeholder="ชื่อผู้ใช้ (admin)" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;box-sizing:border-box;margin-bottom:7px;">
      <input id="adminPass" type="password" placeholder="รหัสผ่าน (1234)" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e2e8f0;font-size:12px;box-sizing:border-box;margin-bottom:8px;">
      <button id="adminLoginBtn" style="width:100%;padding:11px;border-radius:9px;background:#1c1917;color:#fff;font-size:13px;font-weight:bold;border:none;cursor:pointer;">เข้าสู่ระบบ</button>
      <p id="adminErr" style="display:none;margin:8px 0 0;font-size:10px;color:#dc2626;font-weight:bold;">ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (ทดลอง: admin / 1234)</p>
      <p style="font-size:9px;color:#64748b;margin-top:10px;">แอดมินสามารถ: แก้ไขร้าน, ลบร้าน, อนุมัติร้านที่ผู้ใช้เพิ่ม</p>
    </div>
    <div id="adminPanel" style="display:none;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;"><p style="font-size:12px;font-weight:bold;margin:0;">จัดการร้านทั้งหมด (แอดมิน)</p><button id="adminLogout" style="padding:5px 10px;border-radius:6px;background:transparent;border:1px solid #e2e8f0;color:#64748b;font-size:10px;font-weight:bold;cursor:pointer;">ออกจากระบบ</button></div>
      <div style="overflow-x:auto;"><table style="width:100%;font-size:10px;border-collapse:collapse;min-width:380px;"><thead><tr style="color:#64748b;border-bottom:2px solid #e2e8f0;font-size:9px;text-transform:uppercase;"><th style="text-align:left;padding:5px 3px;">ร้าน</th><th style="text-align:left;padding:5px 3px;">งบ</th><th style="text-align:left;padding:5px 3px;">สถานะ</th><th style="text-align:right;padding:5px 3px;">จัดการ</th></tr></thead><tbody id="adminBody"></tbody></table></div>
    </div>
  </div>

  <p id="toast" style="display:none;position:fixed;bottom:14px;left:50%;transform:translateX(-50%);background:#1c1917;color:#fff;padding:8px 16px;border-radius:9px;font-size:11px;font-weight:bold;z-index:99;"></p>
</div>
</div>
</html>

<script>
// ===== DATA =====
(function(){
  window.DATA={
    rest:[
      {id:1,cat:'อาหารตามสั่ง',name:'ร้านเจ๊ไฝ',dist:350,open:'10:00',close:'20:00',isOpen:1,bmin:40,bmax:80,cuisine:'ไทย',atm:'พัดลม'},
      {id:2,cat:'อาหารตามสั่ง',name:'ครัวคุณต๋อง',dist:380,open:'09:00',close:'21:00',isOpen:1,bmin:50,bmax:120,cuisine:'ไทย',atm:'แอร์'},
      {id:3,cat:'ฮาลาล',name:'ข้าวมันไก่ฮาลาล',dist:500,open:'09:00',close:'20:00',isOpen:1,bmin:40,bmax:70,cuisine:'อาหรับ (ฮาลาล)',atm:'แอร์'},
      {id:4,cat:'ก๋วยเตี๋ยว',name:'ราเมนซางโตะ',dist:700,open:'10:00',close:'21:00',isOpen:1,bmin:150,bmax:300,cuisine:'ญี่ปุ่น',atm:'แอร์'},
      {id:5,cat:'ส้มตำ',name:'ส้มตำเจ๊แดง',dist:650,open:'10:00',close:'23:00',isOpen:1,bmin:40,bmax:90,cuisine:'ไทย',atm:'ทั้งคู่'},
      {id:6,cat:'ปิ้งย่าง',name:'ปิ้งย่างบ้านๆ',dist:1100,open:'17:00',close:'01:00',isOpen:1,bmin:80,bmax:200,cuisine:'ไทย',atm:'พัดลม'},
      {id:7,cat:'ก๋วยเตี๋ยว',name:'บะหมี่เกี๊ยวปู',dist:600,open:'11:00',close:'22:00',isOpen:0,bmin:50,bmax:100,cuisine:'ไทย',atm:'แอร์'}
    ],
    sessions:{'KR7X':{name:'ทีมกินข้าววันพฤหัส',votes:[{rest:1},{rest:4},{rest:1},{rest:5},{rest:1}],closed:false}},
    cur:null,adminLoggedIn:false
  };
  window.fmt=function(m){return m<1000?m+' ม.':(m/1000).toFixed(1)+' กม.';};
  window.toast=function(m){var t=document.getElementById('toast');t.textContent=m;t.style.display='block';setTimeout(function(){t.style.display='none';},2200);};
  document.getElementById('jsStatus').textContent='พร้อมใช้งาน — 5 แท็บ · แท็บเพิ่มร้านมีช่องพิกัดครบแล้ว · จัดการต้องล็อกอิน (admin/1234)';
})();
</script>

<script>
// ===== TABS =====
(function(){
  var views={random:document.getElementById('vRandom'),browse:document.getElementById('vBrowse'),vote:document.getElementById('vVote'),add:document.getElementById('vAdd'),admin:document.getElementById('vAdmin')};
  var btns=document.querySelectorAll('.tabBtn');
  for(var i=0;i<btns.length;i++){
    btns[i].addEventListener('click',function(){
      for(var j=0;j<btns.length;j++){btns[j].style.background='transparent';btns[j].style.color='#64748b';btns[j].style.borderBottom='none';}
      this.style.background='rgba(234,88,12,.14)';this.style.color='#0f172a';this.style.borderBottom='3px solid #ea580c';
      var t=this.getAttribute('data-tab');
      for(var k in views){views[k].style.display=(k===t)?'block':'none';}
    });
  }
})();
</script>

<script>
// ===== RANDOM =====
(function(){
  function pick(){
    var cat=document.getElementById('rCat').value,budget=parseInt(document.getElementById('rBudget').value),cuisine=document.getElementById('rCuisine').value,atm=document.getElementById('rAtm').value,maxD=parseInt(document.getElementById('rDist').value);
    var pool=[];var rest=window.DATA.rest;
    for(var i=0;i<rest.length;i++){var r=rest[i];if(r.isOpen!==1)continue;if(cat!=='ทุกหมวด'&&cat!=='all'&&r.cat!==cat)continue;if(r.bmax>budget)continue;if(cuisine!=='all'&&cuisine!=='ทุกประเทศ'&&r.cuisine!==cuisine)continue;if(atm!=='all'&&atm!=='ทั้งหมด'&&r.atm!==atm&&r.atm!=='ทั้งคู่')continue;if(r.dist>maxD)continue;pool.push(r);}
    var ph=document.getElementById('rPh'),card=document.getElementById('rCard'),nameEl=document.getElementById('rName');
    if(pool.length===0){ph.style.display='block';ph.innerHTML='<strong style="color:#dc2626;">ไม่พบร้าน</strong> ลองผ่อนเงื่อนไข';card.style.display='none';return;}
    ph.style.display='none';card.style.display='block';nameEl.textContent='กำลังสุ่ม...';
    var f=0;var iv=setInterval(function(){nameEl.textContent=pool[Math.floor(Math.random()*pool.length)].name;f++;if(f>=8){clearInterval(iv);var x=pool[Math.floor(Math.random()*pool.length)];nameEl.textContent=x.name;document.getElementById('rCatName').textContent=x.cat;document.getElementById('rBudgetText').textContent='งบ '+x.bmin+'-'+x.bmax;document.getElementById('rCuisineText').textContent=x.cuisine;document.getElementById('rAtmText').textContent=x.atm;document.getElementById('rTime').textContent='เวลา '+x.open+' – '+x.close+' น. · ห่าง '+window.fmt(x.dist);}},70);
  }
  document.getElementById('randomBtn').addEventListener('click',pick);
})();
</script>

<script>
// ===== BROWSE =====
(function(){
  var cur='ทั้งหมด',selId=null;var bar=document.getElementById('chipBar'),grid=document.getElementById('bGrid');
  var cats=['ทั้งหมด','อาหารตามสั่ง','ก๋วยเตี๋ยว','ส้มตำ','ข้าวแกง','ปิ้งย่าง','ฮาลาล'];var chips=[];
  for(var i=0;i<cats.length;i++){(function(c){var b=document.createElement('button');b.textContent=c;b.style.cssText='padding:4px 10px;border-radius:20px;border:1px solid #e2e8f0;background:#fff;color:#0f172a;font-size:10px;font-weight:bold;cursor:pointer;';if(c==='ทั้งหมด'){b.style.background='#ea580c';b.style.color='#fff';b.style.borderColor='#ea580c';}b.addEventListener('click',function(){cur=c;for(var j=0;j<chips.length;j++){chips[j].style.background='#fff';chips[j].style.color='#0f172a';chips[j].style.borderColor='#e2e8f0';}this.style.background='#ea580c';this.style.color='#fff';this.style.borderColor='#ea580c';render();});chips.push(b);bar.appendChild(b);})(cats[i]);}
  function render(){
    var list=[];var rest=window.DATA.rest;for(var i=0;i<rest.length;i++){var r=rest[i];if(cur==='ทั้งหมด'||r.cat===cur)list.push(r);}
    list.sort(function(a,b){return a.dist-b.dist;});grid.innerHTML='';
    for(var i=0;i<list.length;i++){(function(r){var closed=r.isOpen===0;var d=document.createElement('div');d.style.cssText='background:#fff;border:1px solid #e2e8f0;border-radius:9px;padding:9px;'+(closed?'opacity:.5;':'cursor:pointer;');if(selId===r.id)d.style.border='2px solid #ea580c';var badge=closed?'<span style="padding:1px 5px;border-radius:20px;background:rgba(220,38,38,.1);color:#dc2626;font-size:8px;font-weight:bold;border:1px solid #dc2626;">ปิด</span>':'<span style="padding:1px 5px;border-radius:20px;background:rgba(22,163,74,.14);color:#16a34a;font-size:8px;font-weight:bold;border:1px solid #16a34a;">เปิด</span>';var dc=r.dist<=500?'#16a34a':(r.dist<=1000?'#ea580c':'#64748b');d.innerHTML='<div style="display:flex;justify-content:space-between;gap:3px;margin-bottom:3px;"><strong style="font-size:10px;line-height:1.3;">'+r.name+'</strong>'+badge+'</div><div style="display:flex;gap:3px;align-items:center;"><span style="padding:1px 5px;border-radius:5px;background:rgba(234,88,12,.14);color:#ea580c;font-size:8px;font-weight:bold;">'+r.cat+'</span><span style="font-size:9px;font-weight:bold;color:'+dc+';">'+window.fmt(r.dist)+'</span></div><p style="font-size:8px;color:#64748b;font-family:monospace;margin:3px 0 0;">'+r.open+'–'+r.close+' · งบ'+r.bmin+'-'+r.bmax+'</p>';if(!closed){d.addEventListener('click',function(){selId=r.id;render();document.getElementById('bSel').style.display='block';document.getElementById('bSelName').textContent=r.name+' · ห่าง '+window.fmt(r.dist)+' · งบ'+r.bmin+'-'+r.bmax+' บาท';});}grid.appendChild(d);})(list[i]);}
  }
  render();
})();
</script>

<script>
// ===== VOTE =====
(function(){
  var names={1:'ร้านเจ๊ไฝ',2:'ครัวคุณต๋อง',3:'ข้าวมันไก่ฮาลาล',4:'ราเมนซางโตะ',5:'ส้มตำเจ๊แดง',6:'ปิ้งย่างบ้านๆ'};
  function tally(s){var c={};for(var i=0;i<s.votes.length;i++){var v=s.votes[i];c[v.rest]=(c[v.rest]||0)+1;}var arr=[];for(var k in c){arr.push({rest:parseInt(k),count:c[k]});}arr.sort(function(a,b){return b.count-a.count;});return arr;}
  function renderVote(){var s=window.DATA.cur;var results=tally(s);var max=0;for(var i=0;i<results.length;i++){if(results[i].count>max)max=results[i].count;}if(max===0)max=1;var box=document.getElementById('vtResults');var html='';if(results.length===0){html='<p style="text-align:center;color:#64748b;font-size:10px;padding:8px;">ยังไม่มีคนโหวต เป็นคนแรกเลย!</p>';}for(var i=0;i<results.length;i++){var r=results[i];var pct=Math.round((r.count/max)*100);var lead=(i===0)?'★ ':'';var color=(i===0)?'#ea580c':'#0f172a';html+='<div style="margin-bottom:7px;"><div style="display:flex;justify-content:space-between;font-size:10px;margin-bottom:2px;"><span style="font-weight:bold;color:'+color+';">'+lead+names[r.rest]+'</span><span style="font-weight:bold;color:#64748b;">'+r.count+' คะแนน</span></div><div style="height:12px;background:#f1f5f9;border-radius:4px;overflow:hidden;"><div style="width:'+pct+'%;height:100%;background:linear-gradient(90deg,#ea580c,#16a34a);border-radius:4px;min-width:12px;"></div></div></div>';}box.innerHTML=html;document.getElementById('vtTotal').textContent=s.votes.length+' คะแนน';}
  function enter(code,s){window.DATA.cur=s;document.getElementById('vgName').textContent=s.name;document.getElementById('copyCode').textContent=code;document.getElementById('vJoin').style.display='none';document.getElementById('vVoteInner').style.display='block';renderVote();}
  document.getElementById('joinBtn').addEventListener('click',function(){var code=document.getElementById('codeInput').value.trim().toUpperCase();var err=document.getElementById('joinErr');if(!code){err.style.display='block';err.textContent='กรุณาใส่โค้ด';return;}if(window.DATA.sessions[code]){err.style.display='none';enter(code,window.DATA.sessions[code]);window.toast('เข้าร่วมกลุ่มแล้ว!');}else{err.style.display='block';}});
  document.getElementById('codeInput').addEventListener('keydown',function(e){if(e.key==='Enter')document.getElementById('joinBtn').click();});
  document.getElementById('createBtn').addEventListener('click',function(){var nm=document.getElementById('groupNameInput').value.trim()||'กลุ่มใหม่';var chars='ABCDEFGHJKLMNPQRSTUVWXYZ23456789';var code='';for(var i=0;i<4;i++){code+=chars[Math.floor(Math.random()*chars.length)];}window.DATA.sessions[code]={name:nm,votes:[],closed:false};enter(code,window.DATA.sessions[code]);window.toast('สร้างกลุ่มสำเร็จ! โค้ด: '+code);});
  document.getElementById('copyCode').addEventListener('click',function(){window.toast('คัดลอกโค้ด '+this.textContent+' แล้ว');});
  document.getElementById('vtSubmit').addEventListener('click',function(){var s=window.DATA.cur;if(s.closed){window.toast('ปิดโหวตแล้ว');return;}var rest=parseInt(document.getElementById('vtRest').value);s.votes.push({rest:rest});renderVote();window.toast('โหวตเรียบร้อย! (ไม่ระบุตัวตน)');});
  document.getElementById('vtAnnounce').addEventListener('click',function(){var s=window.DATA.cur;if(s.votes.length===0){window.toast('ยังไม่มีคนโหวต');return;}s.closed=true;var results=tally(s);var top=results[0].count;var winners=[];for(var i=0;i<results.length;i++){if(results[i].count===top)winners.push(results[i]);}var w=winners[Math.floor(Math.random()*winners.length)];window.toast('ผลออกแล้ว: '+names[w.rest]+' ได้ '+w.count+' คะแนน!');});
  document.getElementById('leaveBtn').addEventListener('click',function(){window.DATA.cur=null;document.getElementById('vVoteInner').style.display='none';document.getElementById('vJoin').style.display='block';document.getElementById('codeInput').value='';document.getElementById('groupNameInput').value='';document.getElementById('joinErr').style.display='none';window.toast('ออกจากกลุ่มแล้ว');});
})();
</script>

<script>
// ===== ADD RESTAURANT (มีพิกัด) =====
(function(){
  document.getElementById('addBtn').addEventListener('click',function(){
    var name=document.getElementById('fName').value.trim();
    if(!name){window.toast('กรุณาใส่ชื่อร้าน');document.getElementById('fName').focus();return;}
    var lat=document.getElementById('fLat').value.trim(),lng=document.getElementById('fLng').value.trim();
    if(!lat||!lng){window.toast('กรุณาใส่พิกัด lat/lng ด้วย (copy จาก Google Maps)');return;}
    var cat=document.getElementById('fCat').value,cuisine=document.getElementById('fCuisine').value,atm=document.querySelector('input[name="atm"]:checked').value;
    var bmin=document.getElementById('fBMin').value,bmax=document.getElementById('fBMax').value;
    var addr=document.getElementById('fAddr').value.trim()||'ไม่ระบุ';
    var by=document.getElementById('fBy').value.trim()||'ไม่ระบุชื่อ';
    document.getElementById('addSuccessText').innerHTML='เพิ่ม <strong>'+name+'</strong> เรียบร้อย!<br>'+cat+' · '+cuisine+' · '+atm+' · งบ'+bmin+'-'+bmax+' บาท<br>ที่อยู่: '+addr+'<br>พิกัด: '+lat+', '+lng+'<br>เพิ่มโดย: '+by;
    document.getElementById('addSuccess').style.display='block';
    window.toast('เพิ่มร้าน "'+name+'" เรียบร้อย!');
  });
})();
</script>

<script>
// ===== ADMIN LOGIN =====
(function(){
  document.getElementById('adminLoginBtn').addEventListener('click',function(){
    var u=document.getElementById('adminUser').value.trim(),p=document.getElementById('adminPass').value;
    if(u==='admin'&&p==='1234'){
      window.DATA.adminLoggedIn=true;document.getElementById('adminLogin').style.display='none';document.getElementById('adminPanel').style.display='block';
      var h='';var rest=window.DATA.rest;
      for(var i=0;i<rest.length;i++){var r=rest[i];var st=r.isOpen?'<span style="padding:1px 6px;border-radius:20px;background:rgba(22,163,74,.14);color:#16a34a;font-size:8px;font-weight:bold;border:1px solid #16a34a;">เปิดใช้งาน</span>':'<span style="padding:1px 6px;border-radius:20px;background:rgba(220,38,38,.1);color:#dc2626;font-size:8px;font-weight:bold;border:1px solid #dc2626;">ปิด</span>';h+='<tr style="border-bottom:1px solid #e2e8f0;"><td style="padding:5px 3px;font-weight:bold;">'+r.name+'</td><td style="padding:5px 3px;color:#64748b;">'+r.bmin+'-'+r.bmax+'</td><td style="padding:5px 3px;">'+st+'</td><td style="padding:5px 3px;text-align:right;white-space:nowrap;"><button onclick="window.toast(\'แก้ไข '+r.name+' (สาธิต)\')" style="padding:2px 6px;border-radius:4px;background:transparent;border:1px solid #e2e8f0;color:#64748b;font-size:8px;cursor:pointer;margin-right:2px;">แก้ไข</button><button onclick="window.toast(\'ลบ '+r.name+' (สาธิต)\')" style="padding:2px 6px;border-radius:4px;background:transparent;border:1px solid rgba(220,38,38,.3);color:#dc2626;font-size:8px;cursor:pointer;">ลบ</button></td></tr>';}
      document.getElementById('adminBody').innerHTML=h;window.toast('เข้าสู่ระบบแอดมินเรียบร้อย!');
    }else{document.getElementById('adminErr').style.display='block';}
  });
  document.getElementById('adminLogout').addEventListener('click',function(){window.DATA.adminLoggedIn=false;document.getElementById('adminPanel').style.display='none';document.getElementById('adminLogin').style.display='block';document.getElementById('adminUser').value='';document.getElementById('adminPass').value='';document.getElementById('adminErr').style.display='none';window.toast('ออกจากระบบแล้ว');});
})();
</script>