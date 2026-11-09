/* ========== KinRaiDee — บัญชีผู้ใช้ + แยกสิทธิ์ผู้ใช้ทั่วไป/แอดมิน (โหลดหลัง script.js และ home.js) ==========
 * ใช้ API: api/auth.php (register/login/logout/me) และ api/admin.php (user_list/set_role)
 * ใช้ของเดิมจาก script.js: apiPost, toast, loadPending, loadCatList
 */
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var me = { logged_in: false };
  var mode = 'register';
  var go = function (tab) { if (window.KRD_showTab) window.KRD_showTab(tab); };
  // ซ่อน/แสดงด้วย inline style ด้วย — กันกรณีเบราว์เซอร์ยังจำ home.css เก่าไว้
  var show = function (el, on) { el.classList.toggle('is-hidden', !on); el.style.display = on ? '' : 'none'; };

  // ---------- แสดงผลตามสถานะ ----------
  function paint() {
    var on = !!me.logged_in, admin = on && me.role === 'admin';
    show($('navLogin'), !on);
    show($('navUser'), on);
    show($('navAdmin'), admin);
    show($('navRole'), admin);
    if (on) {
      $('navUserName').textContent = me.display_name || me.username;
      $('navAvatar').textContent = (me.display_name || me.username || '?').trim().charAt(0).toUpperCase();
    }
    // หน้าเพิ่มร้านและห้องโหวต: ต้องล็อกอิน
    $('tab-add').classList.toggle('locked', !on);
    $('tab-vote').classList.toggle('locked', !on);
    window.KRD_me = me; window.dispatchEvent(new Event('krd-auth'));
    // หน้าแอดมิน
    show($('adminLogin'), !admin);
    show($('adminPanel'), admin);
    $('adminPanel').classList.toggle('hidden', !admin);   // แผงนี้เริ่มต้นมี class hidden ของ Tailwind
    if (!admin) {
      $('adminGateMsg').textContent = on ? 'บัญชีของคุณเป็นสมาชิกทั่วไป ใช้หน้านี้ไม่ได้' : 'เข้าสู่ระบบด้วยบัญชีแอดมินเพื่อจัดการร้านและสมาชิก';
      show($('adminGateBtn'), !on);
    } else {
      if (typeof loadPending === 'function') loadPending();
      if (typeof loadCatList === 'function') loadCatList();
      loadUsers();
    }
  }

  async function refresh() {
    try { me = await apiPost('auth.php', { action: 'me' }); } catch (e) { me = { logged_in: false }; }
    paint();
  }

  // ---------- ฟอร์มเข้าสู่ระบบ / สมัคร ----------
  function setMode(m) {
    mode = m;
    $('segLogin').classList.toggle('active', m === 'login');
    $('segRegister').classList.toggle('active', m === 'register');
    $('segLogin').setAttribute('aria-selected', m === 'login');
    $('segRegister').setAttribute('aria-selected', m === 'register');
    document.querySelectorAll('#authForm .reg-only').forEach(function (e) { show(e, m === 'register'); });
    $('authSubmit').textContent = m === 'login' ? 'เข้าสู่ระบบ' : 'สมัครสมาชิก';
    $('authSwitch').innerHTML = '';
    var sw = document.createElement('button'); sw.type = 'button'; sw.className = 'pick-link';
    sw.textContent = m === 'login' ? 'ยังไม่มีบัญชี? สมัครสมาชิก' : 'มีบัญชีอยู่แล้ว? เข้าสู่ระบบ';
    sw.addEventListener('click', function () { setMode(m === 'login' ? 'register' : 'login'); });
    $('authSwitch').appendChild(sw);
    $('auPass').autocomplete = m === 'login' ? 'current-password' : 'new-password';
    msg('');
  }
  function msg(t) { $('authMsg').textContent = t || ''; }
  $('segLogin').addEventListener('click', function () { setMode('login'); });
  $('segRegister').addEventListener('click', function () { setMode('register'); });

  $('authForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    var u = $('auUser').value.trim(), p = $('auPass').value;
    if (!u || !p) { msg('กรอกชื่อผู้ใช้และรหัสผ่าน'); return; }
    var body = { action: mode, username: u, password: p };
    if (mode === 'register') {
      if (p !== $('auPass2').value) { msg('รหัสผ่านสองช่องไม่ตรงกัน'); return; }
      body.display_name = $('auDisplay').value.trim();
    }
    var btn = $('authSubmit'); btn.disabled = true;
    try {
      var r = await apiPost('auth.php', body);
      if (!r.success) { msg(r.message); toast(r.message); return; }
      me = r; $('authForm').reset(); setMode('register'); paint();
      toast((mode === 'register' ? 'ยินดีต้อนรับ ' : 'สวัสดี ') + (me.display_name || me.username));
      go(me.role === 'admin' ? 'admin' : 'home');
    } catch (err) { msg('เชื่อมต่อเซิร์ฟเวอร์ไม่ได้ ลองใหม่อีกครั้ง'); }
    finally { btn.disabled = false; }
  });

  // ---------- ออกจากระบบ ----------
  async function logout() {
    try { await apiPost('auth.php', { action: 'logout' }); } catch (e) {}
    me = { logged_in: false }; paint(); go('home'); toast('ออกจากระบบแล้ว');
  }
  $('navLogout').addEventListener('click', logout);
  // ปุ่มออกจากระบบในแผงแอดมินเดิม: ตัด handler เก่าของ script.js ออก แล้วใช้ตัวเดียวกัน
  var old = $('adminLogoutBtn');
  if (old) { var nb = old.cloneNode(true); old.parentNode.replaceChild(nb, old); nb.addEventListener('click', logout); }

  // ---------- จัดการสมาชิก (เฉพาะแอดมิน) ----------
  async function loadUsers() {
    var box = $('userList'); if (!box) return;
    var r; try { r = await apiPost('admin.php', { action: 'user_list' }); } catch (e) { return; }
    if (!r.success) { box.innerHTML = ''; return; }
    box.innerHTML = '';
    if (!r.data.length) { box.innerHTML = '<p style="font-size:.82rem;color:var(--muted);">ยังไม่มีสมาชิก</p>'; return; }
    r.data.forEach(function (u) {
      var isMe = +u.user_id === r.me, isAdmin = u.role === 'admin';
      var row = document.createElement('div');
      row.style.cssText = 'display:flex;justify-content:space-between;align-items:center;gap:.6rem;padding:.55rem .8rem;border:1px solid var(--border);border-radius:10px;background:#FFFAF8;';
      var info = document.createElement('div'); info.style.minWidth = '0';
      var nm = document.createElement('div'); nm.style.cssText = 'font-size:.85rem;font-weight:600;overflow:hidden;text-overflow:ellipsis;';
      nm.textContent = u.display_name + (isMe ? ' (คุณ)' : '');
      var sub = document.createElement('div'); sub.style.cssText = 'font-size:.74rem;color:var(--muted);';
      sub.textContent = '@' + u.username + ' · ' + (isAdmin ? 'แอดมิน' : 'สมาชิก');
      info.appendChild(nm); info.appendChild(sub); row.appendChild(info);
      if (!isMe) {
        var b = document.createElement('button'); b.type = 'button'; b.className = 'btn-ghost';
        b.style.cssText = 'font-size:.75rem;padding:.3rem .7rem;flex-shrink:0;';
        b.textContent = isAdmin ? 'ถอดสิทธิ์แอดมิน' : 'ตั้งเป็นแอดมิน';
        b.addEventListener('click', async function () {
          if (!isAdmin && !confirm('ตั้ง ' + u.display_name + ' เป็นแอดมิน? แอดมินลบร้านและจัดการสมาชิกได้')) return;
          var rr = await apiPost('admin.php', { action: 'set_role', user_id: u.user_id, role: isAdmin ? 'user' : 'admin' });
          toast(rr.message); loadUsers();
        });
        row.appendChild(b);
      }
      box.appendChild(row);
    });
  }

  // เปิดหน้านี้จากปุ่มไหนก็เริ่มที่ "สมัครสมาชิก" ก่อน
  document.querySelectorAll('[data-goto="login"]').forEach(function (b) { b.addEventListener('click', function () { setMode('register'); }); });
  setMode('register');
  refresh();
})();
