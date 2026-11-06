{{-- Sisipkan sekali saja pada menu pasien, setelah sesi pasien tervalidasi. --}}
<style>
/* Widget Bantuan NADI - independent dari .nadi-phone dan bottom navigation */
#nadi-help-launch, #nadi-help-shell, #nadi-help-shell * { box-sizing:border-box; }
#nadi-help-launch {
 position:fixed !important;
 right:max(18px,env(safe-area-inset-right,0px)) !important;
 bottom:calc(108px + env(safe-area-inset-bottom,0px)) !important;
 z-index:2147483000 !important;
 border:0;border-radius:50px;background:#0b629c;color:white;
 box-shadow:0 8px 28px #123b5e33;padding:14px 19px;
 cursor:pointer;font:600 14px system-ui,sans-serif;
 display:flex;gap:9px;align-items:center;justify-content:center;
 max-width:calc(100vw - 32px);
}
#nadi-help-shell {
 position:fixed !important;
 right:max(18px,env(safe-area-inset-right,0px)) !important;
 bottom:calc(177px + env(safe-area-inset-bottom,0px)) !important;
 width:min(410px,calc(100vw - 32px)) !important;
 height:min(610px,calc(100dvh - 205px - env(safe-area-inset-bottom,0px))) !important;
 min-height:180px;
 background:#fff;border:1px solid #dde5ee;border-radius:18px;
 box-shadow:0 18px 54px #0f274f34;
 z-index:2147483001 !important;
 display:flex;flex-direction:column;overflow:hidden;
 font:14px system-ui,sans-serif;color:#172b45;
}
#nadi-help-shell[hidden],#nadi-help-launch[hidden],#nadi-help-shell [hidden] { display:none !important; }
.nh-head{background:#0b629c;color:white;padding:14px 17px;display:flex;justify-content:space-between;align-items:center;gap:10px}
.nh-head small{display:block;opacity:.8}.nh-head button{background:transparent;border:0;color:white;font-size:23px;cursor:pointer}
.nh-top{display:flex;gap:8px;padding:10px 13px;border-bottom:1px solid #e6ebf1}
.nh-top button,.nh-button{background:#eaf1f8;color:#17466b;border:0;border-radius:9px;padding:9px 11px;cursor:pointer;font:inherit}
.nh-top button[aria-selected=true],.nh-button.primary{background:#0b629c;color:white}
.nh-area{flex:1;min-height:0;display:flex;flex-direction:column;padding:12px;overflow:hidden}
.nh-scroll{flex:1;overflow-y:auto;min-height:0}
.nh-form label{display:block;margin:8px 0 4px;font-size:12px;color:#536478}
.nh-form select,.nh-form input,.nh-form textarea,#nh-text{box-sizing:border-box;width:100%;padding:10px;border:1px solid #d6e0ea;border-radius:9px;font:inherit;resize:vertical}
.nh-bubble{max-width:84%;width:fit-content;padding:10px 12px;background:#edf2f7;border-radius:13px;margin:8px 0;white-space:pre-wrap;overflow-wrap:anywhere}
.nh-bubble.patient{background:#dbeffd;margin-left:auto}.nh-bubble.system{background:#fff4d9;color:#70550d;margin-left:auto;margin-right:auto;font-size:12px}
.nh-notice{padding:11px;background:#f0f3f6;border:1px solid #d9e0e7;border-radius:10px;color:#576477;margin-top:12px;text-align:center}
.nh-list{width:100%;padding:12px;margin:4px 0;border:1px solid #dce5ed;background:#fff;border-radius:10px;text-align:left;cursor:pointer;color:#1a3550}
.nh-list small{display:block;color:#62758b;margin-top:3px}
.nh-footer{padding:10px 12px;border-top:1px solid #e6ebf1}
.nh-compose{display:flex;gap:8px;align-items:end}.nh-compose textarea{min-height:42px;max-height:96px}
.nh-muted{font-size:12px;color:#62758b}.nh-error{font-size:12px;color:#b42332;padding:0 12px;min-height:0}
.nh-actions{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:8px}
@media(max-width:600px){
 #nadi-help-launch{right:14px !important;bottom:calc(98px + env(safe-area-inset-bottom,0px)) !important}
 #nadi-help-shell{
  right:10px !important;left:10px !important;
  width:auto !important;
  bottom:calc(165px + env(safe-area-inset-bottom,0px)) !important;
  height:min(540px,calc(100dvh - 180px - env(safe-area-inset-bottom,0px))) !important;
  border-radius:16px;
 }
}
@media(max-height:550px){
 #nadi-help-shell{bottom:calc(90px + env(safe-area-inset-bottom,0px)) !important;height:calc(100dvh - 115px - env(safe-area-inset-bottom,0px)) !important;min-height:120px}
}
</style>
<button id="nadi-help-launch" type="button" aria-controls="nadi-help-shell" aria-expanded="false">💬 Bantuan CS</button>
<section id="nadi-help-shell" hidden aria-label="Bantuan Customer Service NADI">
  <header class="nh-head"><div><strong>Bantuan NADI</strong><small>Customer Service RSUD Bali Mandara</small></div><button type="button" id="nh-hide" aria-label="Tutup widget">×</button></header>
  <nav class="nh-top" aria-label="Menu chat"><button id="nh-tab-chat" type="button" aria-selected="true">Percakapan</button><button id="nh-tab-history" type="button" aria-selected="false">Riwayat sesi</button></nav>
  <p class="nh-error" id="nh-error" role="alert" aria-live="polite"></p>
  <div class="nh-area" id="nh-chat-view">
    <div id="nh-start-view" class="nh-scroll nh-form" hidden><p>Silakan sampaikan pertanyaan Anda kepada petugas CS.</p><label for="nh-category">Kategori</label><select id="nh-category"><option>Pendaftaran</option><option>Reservasi</option><option>Jadwal Dokter</option><option>Hasil Pemeriksaan</option><option>Penggunaan NADI</option><option>Lainnya</option></select><label for="nh-subject">Topik (opsional)</label><input id="nh-subject" maxlength="180"><label for="nh-first">Pesan</label><textarea id="nh-first" rows="4" maxlength="3000" placeholder="Tulis pertanyaan..." required></textarea><button id="nh-start" type="button" class="nh-button primary" style="margin-top:12px">Mulai chat baru</button></div>
    <div id="nh-session-view" style="display:flex;flex-direction:column;flex:1;min-height:0" hidden>
      <div class="nh-actions"><div><strong id="nh-ticket"></strong><div class="nh-muted" id="nh-status"></div></div><button id="nh-close-session" type="button" class="nh-button">Akhiri sesi</button></div>
      <div id="nh-messages" class="nh-scroll" aria-live="polite"></div>
      <div id="nh-closed" class="nh-notice" hidden>🔒 Sesi sudah berakhir. Riwayat percakapan tetap dapat dibaca, tetapi tidak dapat dibalas.<div style="margin-top:8px"><button id="nh-new" class="nh-button primary" type="button">Mulai sesi baru</button></div></div>
    </div>
  </div>
  <div class="nh-area" id="nh-history-view" hidden><div class="nh-muted">Pilih tiket untuk melihat isi percakapan sebelumnya.</div><div id="nh-history" class="nh-scroll"></div></div>
  <footer id="nh-footer" class="nh-footer" hidden><form id="nh-send-form" class="nh-compose"><textarea id="nh-text" maxlength="3000" rows="2" placeholder="Ketik pesan..." required></textarea><button class="nh-button primary" type="submit">Kirim</button></form><p class="nh-muted">Jangan kirim OTP, password, atau informasi medis sensitif.</p></footer>
</section>
<script>
(function(){'use strict';
  if(window.__nadiHelpWidgetLoaded)return;window.__nadiHelpWidgetLoaded=true;
  // Pindahkan widget ke body supaya tidak terpotong oleh .nadi-phone / container transform.
  ['nadi-help-launch','nadi-help-shell'].forEach(function(id){var node=document.getElementById(id);if(node && node.parentElement!==document.body)document.body.appendChild(node);});
  const base={{ Illuminate\Support\Js::from(url('/nadi/bantuan')) }};
  const token={{ Illuminate\Support\Js::from(csrf_token()) }};
  const el=id=>document.getElementById(id);
  let selected=null,active=null,after=0,busy=false,tab='chat';
  const error=message=>{el('nh-error').textContent=message||''};
  async function api(path,options={}){
    const r=await fetch(base+path,{credentials:'same-origin',headers:{Accept:'application/json','Content-Type':'application/json','X-CSRF-TOKEN':token},...options});
    const d=await r.json().catch(()=>({}));if(!r.ok)throw new Error(d.message||'Permintaan gagal ('+r.status+')');return d;
  }
  const live=s=>s&&['waiting','active'].includes(s.status);
  function renderState(){
    el('nh-tab-chat').setAttribute('aria-selected',String(tab==='chat'));
    el('nh-tab-history').setAttribute('aria-selected',String(tab==='history'));
    el('nh-chat-view').hidden=tab!=='chat';el('nh-history-view').hidden=tab!=='history';
    el('nh-start-view').hidden=!!selected;el('nh-session-view').hidden=!selected;
    el('nh-footer').hidden=!(tab==='chat'&&live(selected));
    if(selected){el('nh-ticket').textContent=selected.ticket||'';el('nh-status').textContent=({waiting:'Menunggu CS',active:'Sedang ditangani',closed:'Sesi sudah berakhir'})[selected.status]||selected.status;el('nh-close-session').hidden=!live(selected);el('nh-closed').hidden=live(selected)}
  }
  function setSelected(s){selected=s;after=0;el('nh-messages').replaceChildren();renderState()}
  function addMessage(m){const d=document.createElement('div');d.className='nh-bubble '+(m.sender_type==='patient'?'patient':m.sender_type==='system'?'system':'');d.textContent=m.message;el('nh-messages').appendChild(d)}
  async function loadMessages(){if(!selected||busy||tab!=='chat')return;busy=true;const id=selected.id;try{const d=await api('/sessions/'+id+'/messages?after='+after);if(!selected||selected.id!==id)return;selected=d.session;for(const m of d.messages||[]){after=Math.max(after,Number(m.id));addMessage(m)}renderState();if((d.messages||[]).length)el('nh-messages').scrollTop=el('nh-messages').scrollHeight}catch(e){error(e.message)}finally{busy=false}}
  async function loadHistory(){try{const d=await api('/history');const box=el('nh-history');box.replaceChildren();if(!d.sessions.length){box.textContent='Belum ada riwayat sesi.';return}for(const s of d.sessions){const b=document.createElement('button');b.type='button';b.className='nh-list';const title=document.createElement('strong');title.textContent=s.ticket;const detail=document.createElement('small');detail.textContent=[s.category,s.status==='closed'?'Sesi sudah berakhir':s.status==='active'?'Sedang ditangani':'Menunggu CS',s.created_at?new Date(s.created_at).toLocaleString('id-ID'):null].filter(Boolean).join(' · ');b.append(title,detail);b.onclick=()=>{tab='chat';setSelected(s);loadMessages()};box.appendChild(b)}}catch(e){error(e.message)}}
  async function init(){try{const d=await api('/current');active=d.session;setSelected(active);if(active)await loadMessages()}catch(e){error(e.message)}await loadHistory()}
  function show(){el('nadi-help-shell').hidden=false;el('nadi-help-launch').hidden=true;el('nadi-help-launch').setAttribute('aria-expanded','true');renderState();if(selected)loadMessages()}
  function hide(){el('nadi-help-shell').hidden=true;el('nadi-help-launch').hidden=false;el('nadi-help-launch').setAttribute('aria-expanded','false')}
  window.NadiHelpWidget={open:show,close:hide};
  el('nadi-help-launch').onclick=show;el('nh-hide').onclick=hide;
  el('nh-tab-chat').onclick=()=>{tab='chat';if(active&&(!selected||selected.status==='closed'))setSelected(active);renderState();loadMessages()};
  el('nh-tab-history').onclick=()=>{tab='history';renderState();loadHistory()};
  el('nh-start').onclick=async()=>{error('');const message=el('nh-first').value.trim();if(!message)return error('Tuliskan pesan terlebih dahulu.');try{const d=await api('/start',{method:'POST',body:JSON.stringify({category:el('nh-category').value,subject:el('nh-subject').value,message})});active=d.session;tab='chat';setSelected(active);el('nh-first').value='';await loadMessages();await loadHistory()}catch(e){error(e.message)}};
  el('nh-send-form').onsubmit=async e=>{e.preventDefault();if(!live(selected))return;const m=el('nh-text').value.trim();if(!m)return;try{await api('/sessions/'+selected.id+'/messages',{method:'POST',body:JSON.stringify({message:m})});el('nh-text').value='';await loadMessages()}catch(err){error(err.message)}};
  el('nh-close-session').onclick=async()=>{if(!selected||!live(selected)||!confirm('Akhiri sesi ini? Riwayat tetap tersimpan.'))return;try{await api('/sessions/'+selected.id+'/close',{method:'POST',body:'{}'});selected.status='closed';active=null;renderState();loadHistory()}catch(e){error(e.message)}};
  el('nh-new').onclick=()=>{if(active&&live(active)){setSelected(active)}else{setSelected(null)}renderState()};
  setInterval(()=>{if(el('nadi-help-shell').hidden)return;if(tab==='chat'&&selected)loadMessages()},5000);
  init();
})();
</script>
