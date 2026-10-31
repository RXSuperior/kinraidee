<html style="margin:0;padding:0;">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
<div style="background:#FFFCF8;min-height:100vh;font-family:'Nunito','Kanit',sans-serif;color:#51454C;">
<div style="max-width:560px;margin:0 auto;padding:16px;">

  <div style="text-align:center;margin-bottom:14px;">
    <h1 style="font-size:22px;font-weight:800;margin:0;letter-spacing:-.02em;">กินไรดี <span style="color:#E89AAE;">KinRaiDee</span></h1>
    <p style="font-size:11px;color:#887A80;margin:4px 0 0;font-weight:400;">สุ่มร้าน · เลือกเอง · โหวตกับทีม</p>
  </div>

  <div style="display:flex;flex-wrap:wrap;gap:4px;background:#FFFFFF;border:1px solid #F0EAE8;border-radius:16px;padding:5px;margin-bottom:14px;box-shadow:0 2px 8px rgba(81,69,76,.04);">
    <button data-tab="random" class="tabBtn" style="flex:1;min-width:60px;padding:8px 2px;border-radius:12px;font-weight:700;font-size:11px;border:none;cursor:pointer;background:#FCEBF0;color:#E89AAE;">สุ่มร้าน</button>
    <button data-tab="browse" class="tabBtn" style="flex:1;min-width:60px;padding:8px 2px;border-radius:12px;font-weight:600;font-size:11px;border:none;cursor:pointer;background:transparent;color:#887A80;">เลือกเอง</button>
    <button data-tab="vote" class="tabBtn" style="flex:1;min-width:60px;padding:8px 2px;border-radius:12px;font-weight:600;font-size:11px;border:none;cursor:pointer;background:transparent;color:#887A80;">โหวตทีม</button>
    <button data-tab="add" class="tabBtn" style="flex:1;min-width:60px;padding:8px 2px;border-radius:12px;font-weight:600;font-size:11px;border:none;cursor:pointer;background:transparent;color:#887A80;">เพิ่มร้าน</button>
    <button data-tab="admin" class="tabBtn" style="flex:1;min-width:60px;padding:8px 2px;border-radius:12px;font-weight:600;font-size:11px;border:none;cursor:pointer;background:transparent;color:#887A80;">จัดการ</button>
  </div>

  <!-- TAB: RANDOM -->
  <div id="vRandom" style="background:#FFFFFF;border:1px solid #F0EAE8;border-radius:18px;padding:18px;box-shadow:0 2px 12px rgba(81,69,76,.04);">
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px;">
      <div style="flex:1;min-width:120px;"><label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">หมวดหมู่</label>
        <select id="rCat" style="width:100%;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;font-weight:600;color:#51454C;font-family:inherit;"><option value="all">ทุกหมวด</option><option>อาหารตามสั่ง</option><option>ก๋วยเตี๋ยว</option><option>ส้มตำ</option><option>ข้าวแกง</option><option>ปิ้งย่าง</option><option>ฮาลาล</option></select></div>
      <div style="flex:1;min-width:120px;"><label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">งบประมาณ</label>
        <select id="rBudget" style="width:100%;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;font-weight:600;color:#51454C;font-family:inherit;"><option value="9999">ไม่จำกัด</option><option value="50">≤ 50 บาท</option><option value="100">≤ 100 บาท</option><option value="200">≤ 200 บาท</option><option value="500">≤ 500 บาท</option></select></div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px;">
      <div style="flex:1;min-width:120px;"><label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">ประเทศอาหาร</label>
        <select id="rCuisine" style="width:100%;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;font-weight:600;color:#51454C;font-family:inherit;"><option value="all">ทุกประเทศ</option><option>ไทย</option><option>ญี่ปุ่น</option><option>จีน</option><option>เกาหลี</option><option>อิตาลี</option><option>ฝรั่งเศส</option><option>อาหรับ (ฮาลาล)</option><option>อินเดีย</option><option>เวียดนาม</option><option>อเมริกัน</option></select></div>
      <div style="flex:1;min-width:120px;"><label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">บรรยากาศ</label>
        <select id="rAtm" style="width:100%;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;font-weight:600;color:#51454C;font-family:inherit;"><option value="all">ทั้งหมด</option><option>แอร์</option><option>พัดลม</option><option>ทั้งคู่</option></select></div>
    </div>
    <div style="margin-bottom:14px;"><label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">ระยะทางไม่เกิน</label>
      <select id="rDist" style="width:100%;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;font-weight:600;color:#51454C;font-family:inherit;"><option value="99999">ทุกระยะ</option><option value="500">500 ม.</option><option value="1000">1 กม.</option><option value="2000">2 กม.</option><option value="5000">5 กม.</option><option value="10000">10 กม.</option></select></div>
    <button id="randomBtn" style="width:100%;padding:14px;border-radius:12px;background:#E89AAE;color:#fff;font-size:15px;font-weight:800;border:none;cursor:pointer;box-shadow:0 4px 14px rgba(232,154,174,.35);letter-spacing:.02em;">สุ่มร้านเลย!</button>
    <div id="rPh" style="margin-top:14px;text-align:center;padding:24px;border:2px dashed #F0EAE8;border-radius:14px;color:#887A80;font-size:12px;font-weight:600;">กดปุ่มเพื่อสุ่มร้านตามเงื่อนไขทั้งหมด</div>
    <div id="rCard" style="display:none;margin-top:14px;border:2px solid #E89AAE;border-radius:16px;padding:18px;background:#FCEBF0;">
      <p style="font-size:10px;color:#E89AAE;font-weight:800;letter-spacing:.15em;margin:0;text-transform:uppercase;">วันนี้กินที่</p>
      <p id="rName" style="font-size:20px;font-weight:800;margin:4px 0 8px;color:#51454C;"></p>
      <div style="font-size:10px;flex-wrap:wrap;display:flex;gap:5px;"><span id="rCatName" style="padding:3px 10px;border-radius:20px;background:#EBE4F7;color:#7C6BA8;font-weight:700;"></span><span id="rBudgetText" style="padding:3px 10px;border-radius:20px;background:#F9F0D8;color:#A88B3A;font-weight:700;"></span><span id="rCuisineText" style="padding:3px 10px;border-radius:20px;background:#E5EEF9;color:#5B7FB5;font-weight:700;"></span><span id="rAtmText" style="padding:3px 10px;border-radius:20px;background:#FCEBF0;color:#E89AAE;font-weight:700;border:1px solid #E89AAE;"></span></div>
      <p id="rTime" style="font-size:11px;color:#887A80;margin-top:8px;font-weight:600;"></p>
    </div>
  </div>

  <!-- TAB: BROWSE -->
  <div id="vBrowse" style="display:none;background:#FFFFFF;border:1px solid #F0EAE8;border-radius:18px;padding:18px;box-shadow:0 2px 12px rgba(81,69,76,.04);">
    <div id="bSel" style="display:none;margin-bottom:10px;padding:12px;border-radius:12px;background:#E5EEF9;border:1px solid #B8CCE8;"><p style="font-size:9px;color:#5B7FB5;font-weight:800;letter-spacing:.1em;margin:0;text-transform:uppercase;">คุณเลือกร้าน</p><p id="bSelName" style="font-weight:700;font-size:13px;margin:3px 0 0;color:#51454C;"></p></div>
    <div id="chipBar" style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:12px;"></div>
    <div id="bGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:8px;"></div>
  </div>

  <!-- TAB: VOTE -->
  <div id="vVote" style="display:none;">
    <div id="vJoin">
      <div style="background:#FFFFFF;border:1px solid #F0EAE8;border-radius:18px;padding:18px;margin-bottom:10px;box-shadow:0 2px 12px rgba(81,69,76,.04);">
        <p style="font-size:13px;font-weight:800;margin:0 0 8px;color:#51454C;">1) เข้าร่วมกลุ่ม (ทดลอง: KR7X)</p>
        <input id="codeInput" type="text" maxlength="4" placeholder="KR7X" style="width:100%;padding:12px;border-radius:12px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:20px;font-weight:800;text-align:center;letter-spacing:.35em;box-sizing:border-box;margin-bottom:8px;text-transform:uppercase;color:#E89AAE;font-family:inherit;">
        <button id="joinBtn" style="width:100%;padding:12px;border-radius:12px;background:#E89AAE;color:#fff;font-size:13px;font-weight:800;border:none;cursor:pointer;box-shadow:0 4px 14px rgba(232,154,174,.3);">เข้าร่วมกลุ่ม</button>
        <p id="joinErr" style="display:none;margin:8px 0 0;font-size:11px;color:#D4708A;font-weight:700;text-align:center;">ไม่พบโค้ด (ทดลอง: KR7X)</p>
      </div>
      <div style="text-align:center;font-size:10px;color:#887A80;margin:10px 0;font-weight:600;">—— หรือ ——</div>
      <div style="background:#FFFFFF;border:1px solid #F0EAE8;border-radius:18px;padding:18px;margin-bottom:10px;box-shadow:0 2px 12px rgba(81,69,76,.04);">
        <p style="font-size:13px;font-weight:800;margin:0 0 8px;color:#51454C;">2) สร้างกลุ่มใหม่ แล้วแชร์โค้ดให้เพื่อน</p>
        <input id="groupNameInput" type="text" placeholder="ชื่อกลุ่ม เช่น ทีมกินข้าววันศุกร์" style="width:100%;padding:11px;border-radius:12px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;box-sizing:border-box;margin-bottom:8px;color:#51454C;font-family:inherit;">
        <button id="createBtn" style="width:100%;padding:12px;border-radius:12px;background:#A8C8B0;color:#fff;font-size:13px;font-weight:800;border:none;cursor:pointer;box-shadow:0 4px 14px rgba(168,200,176,.35);">สร้างกลุ่มใหม่</button>
      </div>
      <div style="background:#FFFFFF;border:1px solid #F0EAE8;border-radius:18px;padding:18px;box-shadow:0 2px 12px rgba(81,69,76,.04);">
        <p style="font-size:12px;font-weight:800;margin:0 0 8px;color:#51454C;">ประวัติกลุ่มที่คุณสร้างไว้ (<span id="groupCount">0</span> กลุ่ม)</p>
        <div id="groupHistory" style="font-size:11px;color:#887A80;font-weight:600;">ยังไม่เคยสร้างกลุ่ม ลองสร้างกลุ่มแรกเลย!</div>
      </div>
    </div>
    <div id="vVoteInner" style="display:none;">
      <div style="background:#FFFFFF;border:1px solid #F0EAE8;border-radius:18px;padding:18px;margin-bottom:10px;box-shadow:0 2px 12px rgba(81,69,76,.04);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
          <div><p style="font-size:9px;color:#887A80;font-weight:700;margin:0;text-transform:uppercase;letter-spacing:.08em;">กลุ่ม</p><p id="vgName" style="font-weight:800;font-size:15px;margin:2px 0 0;color:#51454C;"></p></div>
          <div style="text-align:right;"><p style="font-size:9px;color:#887A80;font-weight:700;margin:0;text-transform:uppercase;letter-spacing:.08em;">โค้ด</p><button id="copyCode" style="background:#FCEBF0;border:1px solid #E89AAE;color:#E89AAE;font-family:monospace;font-size:16px;font-weight:800;padding:4px 14px;border-radius:10px;cursor:pointer;letter-spacing:.12em;margin-top:2px;"></button></div>
        </div>
        <div id="voteArea">
          <select id="vtRest" style="width:100%;padding:11px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;box-sizing:border-box;margin-bottom:9px;color:#51454C;font-family:inherit;font-weight:600;"><option value="1">ร้านเจ๊ไฝ อาหารตามสั่ง</option><option value="2">ครัวคุณต๋อง</option><option value="3">ข้าวมันไก่ฮาลาล</option><option value="4">ราเมนซางโตะ (ญี่ปุ่น)</option><option value="5">ส้มตำเจ๊แดง</option><option value="6">ปิ้งย่างบ้านๆ</option></select>
          <button id="vtSubmit" style="width:100%;padding:12px;border-radius:12px;background:#E89AAE;color:#fff;font-size:14px;font-weight:800;border:none;cursor:pointer;box-shadow:0 4px 14px rgba(232,154,174,.3);">โหวตเลย! (ไม่ระบุตัวตน)</button>
        </div>
        <div id="sealedBox" style="margin-top:14px;padding:16px;border-radius:14px;border:2px dashed #E89AAE;background:#FCEBF0;">
          <div style="text-align:center;margin-bottom:10px;">
            <p style="font-size:10px;color:#E89AAE;font-weight:800;letter-spacing:.08em;margin:0 0 4px;text-transform:uppercase;">ผลโหวตถูกปิดอยู่</p>
            <p id="vtCount" style="font-size:28px;font-weight:800;color:#51454C;margin:0;">0</p>
            <p style="font-size:10px;color:#887A80;margin:0;font-weight:600;">คนได้โหวตแล้ว</p>
          </div>
          <div style="border-top:1px dashed rgba(232,154,174,.4);padding-top:10px;">
            <p style="font-size:10px;font-weight:800;color:#51454C;margin:0 0 6px;text-transform:uppercase;letter-spacing:.05em;">ร้านที่มีคนโหวต:</p>
            <div id="votedList" style="display:flex;flex-wrap:wrap;gap:5px;"></div>
          </div>
        </div>
        <div id="resultBox" style="display:none;margin-top:14px;">
          <div style="padding:16px;border-radius:14px;background:linear-gradient(135deg,#E89AAE,#C9A9E0);color:#fff;text-align:center;margin-bottom:10px;box-shadow:0 4px 16px rgba(232,154,174,.3);">
            <p style="font-size:10px;opacity:.9;font-weight:800;margin:0;letter-spacing:.12em;text-transform:uppercase;">ร้านที่ได้โหวตมากที่สุด</p>
            <p id="winnerName" style="font-size:20px;font-weight:800;margin:5px 0 3px;"></p>
            <p id="winnerDetail" style="font-size:11px;opacity:.95;margin:0;font-weight:600;"></p>
          </div>
          <p style="font-size:11px;font-weight:800;margin:0 0 8px;color:#51454C;text-transform:uppercase;letter-spacing:.05em;">ผลคะแนนเต็ม:</p>
          <div id="vtResults"></div>
        </div>
        <div style="display:flex;gap:8px;margin-top:14px;"><button id="vtAnnounce" style="flex:1;padding:11px;border-radius:10px;background:#A8C8B0;color:#fff;border:none;font-size:11px;font-weight:800;cursor:pointer;box-shadow:0 3px 10px rgba(168,200,176,.3);">ประกาศผล</button><button id="leaveBtn" style="flex:1;padding:11px;border-radius:10px;background:#fff;color:#887A80;border:1px solid #F0EAE8;font-size:11px;font-weight:700;cursor:pointer;">ออกจากกลุ่ม</button></div>
      </div>
    </div>
  </div>

  <!-- TAB: ADD -->
  <div id="vAdd" style="display:none;background:#FFFFFF;border:1px solid #F0EAE8;border-radius:18px;padding:18px;box-shadow:0 2px 12px rgba(81,69,76,.04);">
    <p style="font-size:11px;color:#A8C8B0;font-weight:800;margin:0 0 12px;text-align:center;text-transform:uppercase;letter-spacing:.05em;">ทุกคนเพิ่มร้านได้ ไม่ต้องล็อกอิน</p>
    <label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">ชื่อร้าน *</label>
    <input id="fName" type="text" placeholder="ชื่อร้าน" style="width:100%;padding:11px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:13px;box-sizing:border-box;margin-bottom:10px;color:#51454C;font-family:inherit;">
    <div style="display:flex;gap:8px;margin-bottom:10px;">
      <div style="flex:1;"><label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">หมวดหมู่</label><select id="fCat" style="width:100%;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;color:#51454C;font-family:inherit;font-weight:600;"><option>อาหารตามสั่ง</option><option>ก๋วยเตี๋ยว</option><option>ส้มตำ</option><option>ข้าวแกง</option><option>ปิ้งย่าง</option><option>ฮาลาล</option></select></div>
      <div style="flex:1;"><label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">ประเทศอาหาร</label><select id="fCuisine" style="width:100%;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;color:#51454C;font-family:inherit;font-weight:600;"><option>ไทย</option><option>ญี่ปุ่น</option><option>จีน</option><option>เกาหลี</option><option>อิตาลี</option><option>อาหรับ (ฮาลาล)</option></select></div>
    </div>
    <label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:5px;">บรรยากาศ</label>
    <div style="display:flex;gap:6px;margin-bottom:10px;"><label style="flex:1;display:flex;align-items:center;justify-content:center;gap:4px;padding:10px;border:1px solid #F0EAE8;border-radius:10px;font-size:11px;font-weight:700;cursor:pointer;background:#FFFCF8;color:#51454C;"><input type="radio" name="atm" value="แอร์" checked> แอร์</label><label style="flex:1;display:flex;align-items:center;justify-content:center;gap:4px;padding:10px;border:1px solid #F0EAE8;border-radius:10px;font-size:11px;font-weight:700;cursor:pointer;background:#FFFCF8;color:#51454C;"><input type="radio" name="atm" value="พัดลม"> พัดลม</label><label style="flex:1;display:flex;align-items:center;justify-content:center;gap:4px;padding:10px;border:1px solid #F0EAE8;border-radius:10px;font-size:11px;font-weight:700;cursor:pointer;background:#FFFCF8;color:#51454C;"><input type="radio" name="atm" value="ทั้งคู่"> ทั้งคู่</label></div>
    <label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">งบประมาณต่อหัว (บาท)</label>
    <div style="display:flex;gap:8px;align-items:center;margin-bottom:10px;"><input id="fBMin" type="number" value="40" style="flex:1;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:13px;box-sizing:border-box;color:#51454C;font-family:inherit;"><span style="font-size:12px;color:#887A80;font-weight:700;">–</span><input id="fBMax" type="number" value="100" style="flex:1;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:13px;box-sizing:border-box;color:#51454C;font-family:inherit;"><span style="font-size:12px;color:#887A80;font-weight:700;">บาท</span></div>
    <label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">ที่อยู่</label>
    <input id="fAddr" type="text" placeholder="เช่น ซอยสุขุมวิท 11 แยก 2" style="width:100%;padding:11px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:13px;box-sizing:border-box;margin-bottom:10px;color:#51454C;font-family:inherit;">
    <label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">พิกัด (ละติจูด , ลองจิจูด) * — copy จาก Google Maps</label>
    <div style="display:flex;gap:8px;margin-bottom:4px;">
      <input id="fLat" type="text" placeholder="13.7563" style="flex:1;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;box-sizing:border-box;font-family:monospace;color:#51454C;">
      <input id="fLng" type="text" placeholder="100.5018" style="flex:1;padding:10px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;box-sizing:border-box;font-family:monospace;color:#51454C;">
    </div>
    <p style="font-size:10px;color:#887A80;margin:0 0 10px;font-weight:600;">วิธีหา: Google Maps → คลิกขวาที่ร้าน → "What's here?" → copy ตัวเลข 2 จุดมาใส่</p>
    <div style="display:flex;gap:8px;margin-bottom:10px;"><div style="flex:1;"><label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">เวลาเปิด</label><input id="fOpen" type="time" value="08:00" style="width:100%;padding:9px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;box-sizing:border-box;color:#51454C;font-family:inherit;"></div><div style="flex:1;"><label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">เวลาปิด</label><input id="fClose" type="time" value="20:00" style="width:100%;padding:9px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:12px;box-sizing:border-box;color:#51454C;font-family:inherit;"></div></div>
    <label style="font-size:11px;font-weight:700;color:#887A80;display:block;margin-bottom:4px;">ชื่อคุณ (ไม่บังคับ)</label>
    <input id="fBy" type="text" placeholder="ปล่อยว่างได้" style="width:100%;padding:11px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:13px;box-sizing:border-box;margin-bottom:14px;color:#51454C;font-family:inherit;">
    <button id="addBtn" style="width:100%;padding:13px;border-radius:12px;background:#E89AAE;color:#fff;font-size:14px;font-weight:800;border:none;cursor:pointer;box-shadow:0 4px 14px rgba(232,154,174,.3);">เพิ่มร้านนี้เลย!</button>
    <div id="addSuccess" style="display:none;margin-top:12px;padding:14px;border-radius:12px;border:2px solid #A8C8B0;background:#EAF3EC;text-align:center;"><p id="addSuccessText" style="font-size:11px;font-weight:700;color:#5B8A68;margin:0;line-height:1.7;"></p></div>
  </div>

  <!-- TAB: ADMIN -->
  <div id="vAdmin" style="display:none;">
    <div id="adminLogin" style="background:#FFFFFF;border:1px solid #F0EAE8;border-radius:18px;padding:22px;text-align:center;box-shadow:0 2px 12px rgba(81,69,76,.04);">
      <p style="font-size:15px;font-weight:800;margin:0 0 4px;color:#51454C;">จัดการข้อมูลร้าน</p>
      <p style="font-size:11px;color:#D4708A;font-weight:700;margin:0 0 16px;">เฉพาะแอดมินเท่านั้น — กรุณาล็อกอิน (admin/1234)</p>
      <input id="adminUser" type="text" placeholder="ชื่อผู้ใช้ (admin)" style="width:100%;padding:11px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:13px;box-sizing:border-box;margin-bottom:8px;color:#51454C;font-family:inherit;">
      <input id="adminPass" type="password" placeholder="รหัสผ่าน (1234)" style="width:100%;padding:11px;border-radius:10px;border:1px solid #F0EAE8;background:#FFFCF8;font-size:13px;box-sizing:border-box;margin-bottom:10px;color:#51454C;font-family:inherit;">
      <button id="adminLoginBtn" style="width:100%;padding:12px;border-radius:12px;background:#51454C;color:#fff;font-size:14px;font-weight:800;border:none;cursor:pointer;">เข้าสู่ระบบ</button>
      <p id="adminErr" style="display:none;margin:10px 0 0;font-size:11px;color:#D4708A;font-weight:700;">ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง</p>
    </div>
    <div id="adminPanel" style="display:none;background:#FFFFFF;border:1px solid #F0EAE8;border-radius:18px;padding:18px;box-shadow:0 2px 12px rgba(81,69,76,.04);">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;"><p style="font-size:13px;font-weight:800;margin:0;color:#51454C;">จัดการร้านทั้งหมด</p><button id="adminLogout" style="padding:6px 12px;border-radius:8px;background:transparent;border:1px solid #F0EAE8;color:#887A80;font-size:10px;font-weight:700;cursor:pointer;">ออกจากระบบ</button></div>
      <div style="overflow-x:auto;"><table style="width:100%;font-size:11px;border-collapse:collapse;min-width:380px;"><thead><tr style="color:#887A80;border-bottom:2px solid #F0EAE8;font-size:9px;text-transform:uppercase;letter-spacing:.05em;"><th style="text-align:left;padding:7px 4px;font-weight:800;">ร้าน</th><th style="text-align:left;padding:7px 4px;font-weight:800;">งบ</th><th style="text-align:left;padding:7px 4px;font-weight:800;">สถานะ</th><th style="text-align:right;padding:7px 4px;font-weight:800;">จัดการ</th></tr></thead><tbody id="adminBody"></tbody></table></div>
    </div>
  </div>

  <p id="toast" style="display:none;position:fixed;bottom:16px;left:50%;transform:translateX(-50%);background:#51454C;color:#fff;padding:10px 20px;border-radius:12px;font-size:12px;font-weight:700;z-index:99;box-shadow:0 4px 16px rgba(81,69,76,.25);"></p>
</div>
</div>
</html>

<script>
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
    sessions:{'KR7X':{name:'ทีมกินข้าววันพฤหัส',votes:[{rest:1},{rest:4},{rest:1},{rest:5},{rest:1}],announced:false}},
    myGroups:[],cur:null,adminLoggedIn:false
  };
  window.fmt=function(m){return m<1000?m+' ม.':(m/1000).toFixed(1)+' กม.';};
  window.toast=function(m){var t=document.getElementById('toast');t.textContent=m;t.style.display='block';setTimeout(function(){t.style.display='none';},2200);};
})();
</script>

<script>
(function(){
  var views={random:document.getElementById('vRandom'),browse:document.getElementById('vBrowse'),vote:document.getElementById('vVote'),add:document.getElementById('vAdd'),admin:document.getElementById('vAdmin')};
  var btns=document.querySelectorAll('.tabBtn');
  for(var i=0;i<btns.length;i++){btns[i].addEventListener('click',function(){for(var j=0;j<btns.length;j++){btns[j].style.background='transparent';btns[j].style.color='#887A80';btns[j].style.fontWeight='600';}this.style.background='#FCEBF0';this.style.color='#E89AAE';this.style.fontWeight='800';var t=this.getAttribute('data-tab');for(var k in views){views[k].style.display=(k===t)?'block':'none';}});}
})();
</script>

<script>
(function(){
  function pick(){
    var cat=document.getElementById('rCat').value,budget=parseInt(document.getElementById('rBudget').value),cuisine=document.getElementById('rCuisine').value,atm=document.getElementById('rAtm').value,maxD=parseInt(document.getElementById('rDist').value);
    var pool=[];var rest=window.DATA.rest;
    for(var i=0;i<rest.length;i++){var r=rest[i];if(r.isOpen!==1)continue;if(cat!=='ทุกหมวด'&&cat!=='all'&&r.cat!==cat)continue;if(r.bmax>budget)continue;if(cuisine!=='all'&&cuisine!=='ทุกประเทศ'&&r.cuisine!==cuisine)continue;if(atm!=='all'&&atm!=='ทั้งหมด'&&r.atm!==atm&&r.atm!=='ทั้งคู่')continue;if(r.dist>maxD)continue;pool.push(r);}
    var ph=document.getElementById('rPh'),card=document.getElementById('rCard'),nameEl=document.getElementById('rName');
    if(pool.length===0){ph.style.display='block';ph.innerHTML='<strong style="color:#D4708A;">ไม่พบร้าน</strong> ลองผ่อนเงื่อนไข (ขยายงบ/ระยะทาง)';card.style.display='none';return;}
    ph.style.display='none';card.style.display='block';nameEl.textContent='กำลังสุ่ม...';
    var f=0;var iv=setInterval(function(){nameEl.textContent=pool[Math.floor(Math.random()*pool.length)].name;f++;if(f>=8){clearInterval(iv);var x=pool[Math.floor(Math.random()*pool.length)];nameEl.textContent=x.name;document.getElementById('rCatName').textContent=x.cat;document.getElementById('rBudgetText').textContent='งบ '+x.bmin+'-'+x.bmax;document.getElementById('rCuisineText').textContent=x.cuisine;document.getElementById('rAtmText').textContent=x.atm;document.getElementById('rTime').textContent='เวลา '+x.open+' – '+x.close+' น. · ห่าง '+window.fmt(x.dist);}},70);
  }
  document.getElementById('randomBtn').addEventListener('click',pick);
})();
</script>

<script>
(function(){
  var cur='ทั้งหมด',selId=null;var bar=document.getElementById('chipBar'),grid=document.getElementById('bGrid');
  var cats=['ทั้งหมด','อาหารตามสั่ง','ก๋วยเตี๋ยว','ส้มตำ','ข้าวแกง','ปิ้งย่าง','ฮาลาล'];var chips=[];
  for(var i=0;i<cats.length;i++){(function(c){var b=document.createElement('button');b.textContent=c;b.style.cssText='padding:5px 12px;border-radius:20px;border:1px solid #F0EAE8;background:#fff;color:#51454C;font-size:10px;font-weight:700;cursor:pointer;font-family:inherit;';if(c==='ทั้งหมด'){b.style.background='#E89AAE';b.style.color='#fff';b.style.borderColor='#E89AAE';}b.addEventListener('click',function(){cur=c;for(var j=0;j<chips.length;j++){chips[j].style.background='#fff';chips[j].style.color='#51454C';chips[j].style.borderColor='#F0EAE8';}this.style.background='#E89AAE';this.style.color='#fff';this.style.borderColor='#E89AAE';render();});chips.push(b);bar.appendChild(b);})(cats[i]);}
  function render(){
    var list=[];var rest=window.DATA.rest;for(var i=0;i<rest.length;i++){var r=rest[i];if(cur==='ทั้งหมด'||r.cat===cur)list.push(r);}
    list.sort(function(a,b){return a.dist-b.dist;});grid.innerHTML='';
    for(var i=0;i<list.length;i++){(function(r){var closed=r.isOpen===0;var d=document.createElement('div');d.style.cssText='background:#FFFCF8;border:1px solid #F0EAE8;border-radius:14px;padding:12px;'+(closed?'opacity:.5;':'cursor:pointer;');if(selId===r.id){d.style.border='2px solid #E89AAE';d.style.background='#FCEBF0';}var badge=closed?'<span style="padding:2px 8px;border-radius:20px;background:#F0EAE8;color:#887A80;font-size:9px;font-weight:700;">ปิด</span>':'<span style="padding:2px 8px;border-radius:20px;background:#EAF3EC;color:#5B8A68;font-size:9px;font-weight:700;">เปิด</span>';var dc=r.dist<=500?'#5B8A68':(r.dist<=1000?'#D4708A':'#887A80');d.innerHTML='<div style="display:flex;justify-content:space-between;gap:4px;margin-bottom:5px;"><strong style="font-size:11px;line-height:1.35;color:#51454C;">'+r.name+'</strong>'+badge+'</div><div style="display:flex;gap:4px;align-items:center;flex-wrap:wrap;"><span style="padding:2px 8px;border-radius:8px;background:#EBE4F7;color:#7C6BA8;font-size:9px;font-weight:700;">'+r.cat+'</span><span style="font-size:10px;font-weight:800;color:'+dc+';">'+window.fmt(r.dist)+'</span></div><p style="font-size:9px;color:#887A80;font-family:monospace;margin:5px 0 0;font-weight:600;">'+r.open+'–'+r.close+' · งบ'+r.bmin+'-'+r.bmax+'</p>';if(!closed){d.addEventListener('click',function(){selId=r.id;render();document.getElementById('bSel').style.display='block';document.getElementById('bSelName').textContent=r.name+' · ห่าง '+window.fmt(r.dist)+' · งบ'+r.bmin+'-'+r.bmax+' บาท';});}grid.appendChild(d);})(list[i]);}
  }
  render();
})();
</script>

<script>
(function(){
  var names={1:'ร้านเจ๊ไฝ',2:'ครัวคุณต๋อง',3:'ข้าวมันไก่ฮาลาล',4:'ราเมนซางโตะ',5:'ส้มตำเจ๊แดง',6:'ปิ้งย่างบ้านๆ'};
  function tally(s){var c={};for(var i=0;i<s.votes.length;i++){var v=s.votes[i];c[v.rest]=(c[v.rest]||0)+1;}var arr=[];for(var k in c){arr.push({rest:parseInt(k),count:c[k]});}arr.sort(function(a,b){return b.count-a.count;});return arr;}
  function updateSealed(){
    document.getElementById('vtCount').textContent=window.DATA.cur.votes.length;
    var voted={};for(var i=0;i<window.DATA.cur.votes.length;i++){voted[window.DATA.cur.votes[i].rest]=true;}
    var ids=Object.keys(voted);var list=document.getElementById('votedList');
    if(ids.length===0){list.innerHTML='<span style="font-size:10px;color:#887A80;font-weight:600;">ยังไม่มีร้านใดถูกโหวต</span>';}
    else{var html='';for(var i=0;i<ids.length;i++){html+='<span style="padding:3px 10px;border-radius:20px;background:#fff;border:1px solid #E89AAE;color:#E89AAE;font-size:10px;font-weight:700;">'+names[parseInt(ids[i])]+'</span>';}list.innerHTML=html;}
  }
  function renderHistory(){
    document.getElementById('groupCount').textContent=window.DATA.myGroups.length;
    var box=document.getElementById('groupHistory');
    if(window.DATA.myGroups.length===0){box.innerHTML='<span style="font-size:11px;color:#887A80;font-weight:600;">ยังไม่เคยสร้างกลุ่ม ลองสร้างกลุ่มแรกเลย!</span>';return;}
    var html='<div style="display:flex;flex-wrap:wrap;gap:5px;">';
    for(var i=0;i<window.DATA.myGroups.length;i++){html+='<button data-code="'+window.DATA.myGroups[i].code+'" class="histBtn" style="padding:6px 12px;border-radius:10px;background:#E5EEF9;border:1px solid #B8CCE8;color:#5B7FB5;font-size:10px;font-weight:700;cursor:pointer;font-family:inherit;">'+window.DATA.myGroups[i].name+' ('+window.DATA.myGroups[i].code+')</button>';}
    html+='</div>';box.innerHTML=html;
    var hb=box.querySelectorAll('.histBtn');
    for(var j=0;j<hb.length;j++){hb[j].addEventListener('click',function(){var code=this.getAttribute('data-code');if(window.DATA.sessions[code]){enter(code,window.DATA.sessions[code]);window.toast('เข้ากลุ่ม '+window.DATA.sessions[code].name);}});}
  }
  function enter(code,s){
    window.DATA.cur=s;
    document.getElementById('vgName').textContent=s.name;
    document.getElementById('copyCode').textContent=code;
    document.getElementById('vJoin').style.display='none';
    document.getElementById('vVoteInner').style.display='block';
    document.getElementById('sealedBox').style.display='block';
    document.getElementById('resultBox').style.display='none';
    document.getElementById('voteArea').style.display='block';
    var ab=document.getElementById('vtAnnounce');ab.textContent='ประกาศผล';ab.style.background='#A8C8B0';ab.disabled=false;
    updateSealed();
    if(s.announced){showResults();}
  }
  function showResults(){
    var results=tally(window.DATA.cur);var top=results[0].count;var winners=[];
    for(var i=0;i<results.length;i++){if(results[i].count===top)winners.push(results[i]);}
    var w=winners[Math.floor(Math.random()*winners.length)];
    document.getElementById('winnerName').textContent=names[w.rest];
    document.getElementById('winnerDetail').textContent=winners.length>1?'เสมอกัน '+winners.length+' ร้าน ระบบสุ่มเลือกให้':'ได้ '+w.count+' คะแนน';
    var max=top;if(max===0)max=1;var html='';
    for(var i=0;i<results.length;i++){var r=results[i];var pct=Math.round((r.count/max)*100);var lead=(i===0)?'★ ':'';var color=(i===0)?'#E89AAE':'#51454C';html+='<div style="margin-bottom:8px;"><div style="display:flex;justify-content:space-between;font-size:11px;margin-bottom:4px;"><span style="font-weight:800;color:'+color+';">'+lead+names[r.rest]+'</span><span style="font-weight:800;color:#887A80;">'+r.count+' คะแนน</span></div><div style="height:14px;background:#F0EAE8;border-radius:7px;overflow:hidden;"><div style="width:'+pct+'%;height:100%;background:linear-gradient(90deg,#E89AAE,#C9A9E0);border-radius:7px;min-width:14px;"></div></div></div>';}
    document.getElementById('vtResults').innerHTML=html;
    document.getElementById('sealedBox').style.display='none';document.getElementById('resultBox').style.display='block';document.getElementById('voteArea').style.display='none';
    var ab=document.getElementById('vtAnnounce');ab.textContent='ประกาศผลแล้ว';ab.style.background='#C9C0C4';ab.disabled=true;
  }
  document.getElementById('joinBtn').addEventListener('click',function(){var code=document.getElementById('codeInput').value.trim().toUpperCase();var err=document.getElementById('joinErr');if(!code){err.style.display='block';err.textContent='กรุณาใส่โค้ด';return;}if(window.DATA.sessions[code]){err.style.display='none';enter(code,window.DATA.sessions[code]);window.toast('เข้าร่วมกลุ่มแล้ว!');}else{err.style.display='block';}});
  document.getElementById('codeInput').addEventListener('keydown',function(e){if(e.key==='Enter')document.getElementById('joinBtn').click();});
  document.getElementById('createBtn').addEventListener('click',function(){var nm=document.getElementById('groupNameInput').value.trim()||'กลุ่มใหม่';var chars='ABCDEFGHJKLMNPQRSTUVWXYZ23456789';var code='';for(var i=0;i<4;i++){code+=chars[Math.floor(Math.random()*chars.length)];}window.DATA.sessions[code]={name:nm,votes:[],announced:false};window.DATA.myGroups.push({code:code,name:nm});renderHistory();enter(code,window.DATA.sessions[code]);window.toast('สร้างกลุ่มสำเร็จ! โค้ด: '+code);});
  document.getElementById('copyCode').addEventListener('click',function(){window.toast('คัดลอกโค้ด '+this.textContent+' แล้ว');});
  document.getElementById('vtSubmit').addEventListener('click',function(){var s=window.DATA.cur;if(s.announced){window.toast('ประกาศผลแล้ว โหวตไม่ได้');return;}var rest=parseInt(document.getElementById('vtRest').value);s.votes.push({rest:rest});updateSealed();window.toast('โหวตเรียบร้อย! (ผลยังปิดอยู่)');});
  document.getElementById('vtAnnounce').addEventListener('click',function(){var s=window.DATA.cur;if(s.votes.length===0){window.toast('ยังไม่มีคนโหวต');return;}s.announced=true;showResults();window.toast('เปิดเผยผลโหวตแล้ว!');});
  document.getElementById('leaveBtn').addEventListener('click',function(){window.DATA.cur=null;document.getElementById('vVoteInner').style.display='none';document.getElementById('vJoin').style.display='block';document.getElementById('codeInput').value='';document.getElementById('groupNameInput').value='';document.getElementById('joinErr').style.display='none';renderHistory();window.toast('ออกจากกลุ่มแล้ว');});
  renderHistory();
})();
</script>

<script>
(function(){
  document.getElementById('addBtn').addEventListener('click',function(){
    var name=document.getElementById('fName').value.trim();if(!name){window.toast('กรุณาใส่ชื่อร้าน');document.getElementById('fName').focus();return;}
    var lat=document.getElementById('fLat').value.trim(),lng=document.getElementById('fLng').value.trim();if(!lat||!lng){window.toast('กรุณาใส่พิกัด lat/lng ด้วย');return;}
    var cat=document.getElementById('fCat').value,cuisine=document.getElementById('fCuisine').value,atm=document.querySelector('input[name="atm"]:checked').value;
    var bmin=document.getElementById('fBMin').value,bmax=document.getElementById('fBMax').value;
    var addr=document.getElementById('fAddr').value.trim()||'ไม่ระบุ';var by=document.getElementById('fBy').value.trim()||'ไม่ระบุชื่อ';
    document.getElementById('addSuccessText').innerHTML='เพิ่ม <strong>'+name+'</strong> เรียบร้อย!<br>'+cat+' · '+cuisine+' · '+atm+' · งบ'+bmin+'-'+bmax+' บาท<br>ที่อยู่: '+addr+' · พิกัด: '+lat+', '+lng+'<br>เพิ่มโดย: '+by;
    document.getElementById('addSuccess').style.display='block';window.toast('เพิ่มร้าน "'+name+'" เรียบร้อย!');
  });
})();
</script>

<script>
(function(){
  document.getElementById('adminLoginBtn').addEventListener('click',function(){
    var u=document.getElementById('adminUser').value.trim(),p=document.getElementById('adminPass').value;
    if(u==='admin'&&p==='1234'){
      window.DATA.adminLoggedIn=true;document.getElementById('adminLogin').style.display='none';document.getElementById('adminPanel').style.display='block';
      var h='';var rest=window.DATA.rest;
      for(var i=0;i<rest.length;i++){var r=rest[i];var st=r.isOpen?'<span style="padding:2px 8px;border-radius:20px;background:#EAF3EC;color:#5B8A68;font-size:9px;font-weight:700;">เปิดใช้งาน</span>':'<span style="padding:2px 8px;border-radius:20px;background:#F0EAE8;color:#887A80;font-size:9px;font-weight:700;">ปิด</span>';h+='<tr style="border-bottom:1px solid #F0EAE8;"><td style="padding:7px 4px;font-weight:700;color:#51454C;">'+r.name+'</td><td style="padding:7px 4px;color:#887A80;font-weight:600;">'+r.bmin+'-'+r.bmax+'</td><td style="padding:7px 4px;">'+st+'</td><td style="padding:7px 4px;text-align:right;white-space:nowrap;"><button onclick="window.toast(\'แก้ไข '+r.name+' (สาธิต)\')" style="padding:3px 8px;border-radius:6px;background:transparent;border:1px solid #F0EAE8;color:#887A80;font-size:9px;font-weight:700;cursor:pointer;margin-right:3px;font-family:inherit;">แก้ไข</button><button onclick="window.toast(\'ลบ '+r.name+' (สาธิต)\')" style="padding:3px 8px;border-radius:6px;background:transparent;border:1px solid rgba(212,112,138,.3);color:#D4708A;font-size:9px;font-weight:700;cursor:pointer;font-family:inherit;">ลบ</button></td></tr>';}
      document.getElementById('adminBody').innerHTML=h;window.toast('เข้าสู่ระบบแอดมินเรียบร้อย!');
    }else{document.getElementById('adminErr').style.display='block';}
  });
  document.getElementById('adminLogout').addEventListener('click',function(){window.DATA.adminLoggedIn=false;document.getElementById('adminPanel').style.display='none';document.getElementById('adminLogin').style.display='block';document.getElementById('adminUser').value='';document.getElementById('adminPass').value='';document.getElementById('adminErr').style.display='none';window.toast('ออกจากระบบแล้ว');});
})();
</script>