/* ========== KinRaiDee — ห้องโหวตแบบมีหัวห้อง (โหลดหลัง script.js และ vote_picker.js) ==========
 * - หัวห้อง = คนสร้างห้อง: เลือกร้าน, เฉลยผล, เชิญคนที่ไม่โหวตออก
 * - สมาชิก = คนที่ใส่โค้ดเข้าห้อง: โหวตได้คนละ 1 ร้าน
 * - ก่อนเฉลย: เห็นแค่ว่าใครโหวตแล้ว/ยัง ไม่เห็นว่าใครโหวตร้านไหนและไม่เห็นคะแนน
 * - หัวห้องกดเฉลยได้เมื่อทุกคนโหวตครบ
 * แทนที่ฟังก์ชัน enterVoteRoom / renderVoteOptions / renderVoteChart / renderVoteHistory ของ script.js
 * ใช้ API: vote.php (state / join / cast / kick / announce / my_rooms)
 */
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var timer = null, busy = false, lastSig = '', prev = null, changing = false;

  function el(tag, cls, text) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    if (text !== undefined) e.textContent = text;
    return e;
  }
  function toggle(node, on) { node.classList.toggle('is-hidden', !on); }

  // ---------- ดึงสถานะห้องเป็นระยะ ----------
  function stop() { if (timer) clearInterval(timer); timer = null; }
  function start() {
    stop();
    timer = setInterval(function () { if (!document.hidden && currentVoteGroup) load(); }, 3000);
  }
  async function load() {
    if (!currentVoteGroup || busy) return;
    busy = true;
    try {
      var s = await apiPost('vote.php', { action: 'state', group_id: currentVoteGroup.group_id });
      if (!currentVoteGroup) return;
      if (!s.success) { if (s.removed) { toast(s.message); leave(); } return; }
      render(s);
    } catch (e) { /* เครือข่ายสะดุด รอรอบถัดไป */ }
    finally { busy = false; }
  }

  // ---------- วาดห้อง ----------
  function render(s) {
    var sig = JSON.stringify(s);
    if (sig === lastSig) return;
    lastSig = sig;

    // แจ้งเตือนเมื่อสถานะเปลี่ยน
    if (prev && !prev.is_closed && s.is_closed && !s.is_owner) toast('หัวห้องเฉลยผลแล้ว!');
    if (prev && s.is_owner && !s.is_closed && !prev.can_announce && s.can_announce) toast('ทุกคนโหวตครบแล้ว เฉลยได้เลย!');
    prev = s;

    var host = s.members.filter(function (m) { return m.is_owner; })[0];
    // บทบาทของฉัน
    var role = $('roomRole'); role.innerHTML = '';
    if (s.is_owner) role.appendChild(el('span', 'role-chip host', '👑 คุณคือหัวห้อง'));
    else {
      role.appendChild(el('span', 'role-chip member', 'สมาชิก'));
      if (host) role.appendChild(el('span', '', 'หัวห้อง: ' + host.name));
    }
    if (s.is_closed) role.appendChild(el('span', 'role-chip member', 'เฉลยผลแล้ว'));

    // ความคืบหน้า
    $('rpText').textContent = s.is_closed ? 'เฉลยผลแล้ว · ' + s.total + ' คนร่วมโหวต'
      : 'โหวตแล้ว ' + s.voted_count + ' / ' + s.total + ' คน';
    $('rpBar').style.width = (s.total ? Math.round(s.voted_count / s.total * 100) : 0) + '%';

    // รายชื่อสมาชิก
    var optName = {}; s.options.forEach(function (o) { optName[o.option_id] = o.restaurant_name; });
    var box = $('roomMembers'); box.innerHTML = '';
    s.members.forEach(function (m) {
      var row = el('div', 'member');
      row.appendChild(el('span', 'av', (m.name || '?').trim().charAt(0).toUpperCase()));
      var nm = el('span', 'nm', m.name);
      if (m.is_owner) nm.appendChild(el('span', 'tag', '👑 หัวห้อง'));
      if (m.is_me) nm.appendChild(el('span', 'tag', '(คุณ)'));
      row.appendChild(nm);
      if (s.is_owner && !m.is_me && !s.is_closed) {
        var k = el('button', 'kick', 'เชิญออก'); k.type = 'button';
        k.addEventListener('click', async function () {
          if (!confirm('เชิญ ' + m.name + ' ออกจากห้อง?')) return;
          var r = await apiPost('vote.php', { action: 'kick', group_id: s.group_id, user_id: m.user_id });
          toast(r.message); lastSig = ''; load();
        });
        row.appendChild(k);
      }
      var label = m.voted ? (s.is_closed && optName[m.option_id] ? '→ ' + optName[m.option_id] : 'โหวตแล้ว ✓') : 'รอโหวต';
      row.appendChild(el('span', 'st ' + (m.voted ? 'done' : 'wait'), label));
      box.appendChild(row);
    });

    // ตัวเลือกร้าน / ผลโหวตของฉัน / ผลรวม
    var optsBox = $('voteOptions'), my = $('myVote'), chart = $('voteChart'), ann = $('announceResult');
    optsBox.innerHTML = '';
    if (s.is_closed || !s.already_voted) changing = false;
    var showOpts = !s.is_closed && (!s.already_voted || changing);
    toggle(optsBox, showOpts); toggle($('optLabel'), showOpts);
    optsBox.classList.remove('hidden');
    if (showOpts) {
      s.options.forEach(function (o) {
        var mine = s.already_voted && o.option_id === s.my_option_id;
        var b = el('button', 'btn-ghost', (mine ? '✓ ' : '') + o.restaurant_name + ' · ' + o.category_name + ' · งบ ≤ ' + o.budget_max + ' บาท');
        b.type = 'button'; b.style.cssText = 'width:100%;text-align:left;padding:.7rem 1rem;' + (mine ? 'border-color:var(--accent-deep);background:#FBE4EA;font-weight:600;' : '');
        b.addEventListener('click', async function () {
          if (mine) { changing = false; lastSig = ''; load(); return; }
          var r = await apiPost('vote.php', { action: 'cast', group_id: s.group_id, option_id: o.option_id });
          if (r && r.success) changing = false;
          toast(r.message); lastSig = ''; load();
        });
        optsBox.appendChild(b);
      });
    }
    toggle($('optLabel'), showOpts);
    if ($('optLabel') && showOpts) $('optLabel').textContent = s.already_voted ? 'เลือกร้านใหม่เพื่อเปลี่ยนโหวต (กดร้านที่ติ๊กไว้เพื่อยกเลิกการเปลี่ยน)' : 'เลือกร้านที่อยากกิน';
    toggle(my, !s.is_closed && s.already_voted && !changing);
    if (!s.is_closed && s.already_voted && !changing) {
      my.innerHTML = ''; my.appendChild(document.createTextNode('คุณโหวตแล้ว: '));
      my.appendChild(el('b', '', optName[s.my_option_id] || ''));
      my.appendChild(el('div', '', s.can_announce ? 'ทุกคนโหวตครบแล้ว' : 'รอเพื่อนโหวตให้ครบ แล้วหัวห้องจะเฉลยผล'));
      my.lastChild.style.cssText = 'font-size:.78rem;color:var(--muted);margin-top:.2rem;';
      var cb = el('button', 'btn-ghost', 'เปลี่ยนโหวต'); cb.type = 'button';
      cb.style.cssText = 'margin-top:.6rem;padding:.45rem 1rem;font-size:.85rem;';
      cb.addEventListener('click', function () { changing = true; lastSig = ''; load(); });
      my.appendChild(cb);
    }

    chart.innerHTML = '';
    if (s.is_closed) {
      chart.classList.remove('hidden', 'is-hidden');
      var max = 1; s.options.forEach(function (o) { if (o.vote_count > max) max = o.vote_count; });
      s.options.forEach(function (o) {
        var row = el('div', 'res-row' + (o.option_id === s.winner_option_id ? ' win' : ''));
        var top = el('div', 'top'); top.appendChild(el('span', '', (o.option_id === s.winner_option_id ? '🏆 ' : '') + o.restaurant_name));
        top.appendChild(el('span', '', o.vote_count + ' คะแนน')); row.appendChild(top);
        var bar = el('div', 'res-bar'), fill = document.createElement('i'); fill.style.width = Math.round(o.vote_count / max * 100) + '%';
        bar.appendChild(fill); row.appendChild(bar);
        var who = s.members.filter(function (m) { return m.option_id === o.option_id; }).map(function (m) { return m.name; });
        if (who.length) row.appendChild(el('div', 'who', 'โหวตโดย ' + who.join(', ')));
        chart.appendChild(row);
      });
      var w = s.options.filter(function (o) { return o.option_id === s.winner_option_id; })[0];
      if (w) {
        ann.classList.remove('hidden');
        ann.innerHTML = '';
        ann.appendChild(el('div', '', 'ร้านที่ไปกินกัน')).style.cssText = 'font-size:.8rem;color:var(--muted);';
        var t = el('div', '', w.restaurant_name); t.style.cssText = 'font-size:1.3rem;font-weight:700;color:var(--accent-deep);margin:.3rem 0;'; ann.appendChild(t);
        var sc = el('div', '', 'ได้ ' + w.vote_count + ' คะแนน'); sc.style.cssText = 'font-size:.85rem;color:#86686E;'; ann.appendChild(sc);
        if (s.note) { var n = el('div', '', s.note); n.style.cssText = 'font-size:.78rem;color:var(--muted);margin-top:.3rem;'; ann.appendChild(n); }
        if (w.map_url) { var a = el('a', 'map-link', 'เปิดใน Google Maps'); a.href = w.map_url; a.target = '_blank'; a.rel = 'noopener'; ann.appendChild(a); }
      }
    } else { chart.classList.add('hidden'); ann.classList.add('hidden'); }

    // ปุ่มเฉลย: เฉพาะหัวห้อง และต้องครบทุกคน
    var ab = $('announceBtn'), hint = $('announceHint');
    toggle(ab, s.is_owner && !s.is_closed);
    ab.disabled = !s.can_announce;
    ab.textContent = s.can_announce ? 'เฉลยผล' : 'เฉลยผล (' + s.voted_count + '/' + s.total + ')';
    hint.textContent = s.is_closed ? '' :
      s.is_owner ? (s.can_announce ? 'ทุกคนโหวตครบแล้ว กดเฉลยผลได้เลย' : 'รอสมาชิกโหวตให้ครบก่อน จึงจะเฉลยได้ (เหลืออีก ' + (s.total - s.voted_count) + ' คน)')
                 : 'รอหัวห้องเฉลยผลเมื่อทุกคนโหวตครบ';
  }

  // ---------- แทนที่ฟังก์ชันของ script.js ----------
  window.enterVoteRoom = function (code) {
    $('voteLobby').classList.add('hidden');
    $('votePicker').classList.add('hidden');
    $('voteRoom').classList.remove('hidden');
    $('roomCode').textContent = code;
    $('announceResult').classList.add('hidden');
    lastSig = ''; prev = null;
    ['roomMembers', 'voteOptions', 'voteChart'].forEach(function (i) { $(i).innerHTML = ''; });
    load(); start();
  };
  window.renderVoteOptions = function () { lastSig = ''; load(); };
  window.renderVoteChart = function () {};

  window.renderVoteHistory = async function () {
    var wrap = $('voteHistoryWrap'), box = $('voteHistory');
    if (!wrap || !box) return;
    var r; try { r = await apiPost('vote.php', { action: 'my_rooms' }); } catch (e) { return; }
    if (!r.success || !r.rooms.length) { wrap.classList.add('hidden'); return; }
    wrap.classList.remove('hidden'); box.innerHTML = '';
    r.rooms.forEach(function (h) {
      var row = el('div', 'hist-item');
      var left = el('div'); left.style.cssText = 'display:flex;align-items:center;gap:.6rem;min-width:0;';
      var code = el('span', '', h.share_code); code.style.cssText = 'background:var(--accent);color:#fff;padding:.2rem .6rem;border-radius:6px;font-weight:700;letter-spacing:2px;font-size:.85rem;flex-shrink:0;';
      var info = el('div'); info.style.cssText = 'font-size:.78rem;color:var(--muted);';
      info.appendChild(document.createTextNode(h.members + ' คน'));
      if (+h.is_owner) info.appendChild(el('span', 'hist-badge host', 'หัวห้อง'));
      if (+h.is_closed) info.appendChild(el('span', 'hist-badge done', 'เฉลยแล้ว'));
      left.appendChild(code); left.appendChild(info); row.appendChild(left);
      var b = el('button', 'btn-ghost', 'เข้าห้อง'); b.type = 'button'; b.style.cssText = 'font-size:.75rem;padding:.3rem .7rem;flex-shrink:0;';
      b.addEventListener('click', async function () {
        var d = await apiPost('vote.php', { action: 'join', code: h.share_code });
        if (d.success) { currentVoteGroup = { group_id: d.group_id, code: d.code }; enterVoteRoom(d.code); } else toast(d.message);
      });
      row.appendChild(b); box.appendChild(row);
    });
  };

  // ---------- ออกจากห้อง / เฉลย ----------
  function leave() {
    stop(); currentVoteGroup = null; lastSig = ''; prev = null; changing = false;
    $('voteRoom').classList.add('hidden'); $('voteLobby').classList.remove('hidden');
    renderVoteHistory();
  }
  window.KRD_leaveRoom = leave;
  $('leaveRoomBtn').addEventListener('click', function () { stop(); lastSig = ''; prev = null; setTimeout(renderVoteHistory, 100); });

  // ตัด handler เฉลยเดิมของ script.js ออก แล้วใช้ตัวใหม่ (ฝั่งเซิร์ฟเวอร์ตรวจสิทธิ์ซ้ำอยู่แล้ว)
  var old = $('announceBtn'), nb = old.cloneNode(true);
  old.parentNode.replaceChild(nb, old);
  nb.addEventListener('click', async function () {
    if (!currentVoteGroup) return;
    nb.disabled = true;
    var r = await apiPost('vote.php', { action: 'announce', group_id: currentVoteGroup.group_id });
    if (!r.success) { toast(r.message); lastSig = ''; load(); return; }
    lastSig = ''; render(r);
  });

  // โหลด "ห้องของฉัน" เมื่อล็อกอิน
  window.addEventListener('krd-auth', function () {
    if (window.KRD_me && window.KRD_me.logged_in) renderVoteHistory(); else if (currentVoteGroup) leave();
  });
})();
