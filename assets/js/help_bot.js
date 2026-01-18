// Lightweight FAQs bot and guided tour
(function(){
  let FAQS = [
    { q: 'Start a guided tour', a: 'Type “tour” or click the Tour suggestion to start an in-app walkthrough tailored to this page.', tags:['tour','guide'] }
  ];
  const DEFAULT_SUGGESTIONS = [
    'How do I log in?','How to register a new account?','Where do I upload archives?'
  ];
  const CATEGORIES = [
    {label:'Login', tag:'login'},
    {label:'Register', tag:'register'},
    {label:'Archives', tag:'archives'},
    {label:'Projects', tag:'projects'},
    {label:'Admin', tag:'admin'},
    {label:'Theme', tag:'theme'},
    {label:'Profile', tag:'profile'},
    {label:'Support', tag:'support'}
  ];
  async function loadFaqs(){
    // First try server-side database via FAQs endpoint
    const base = (typeof _base_url_ !== 'undefined' && _base_url_) ? _base_url_ : '';
    try{
      const api = await fetch(base + 'classes/FAQs.php?f=list_public&v='+(Date.now()), {cache:'no-store'});
      if(api.ok){
        const j = await api.json();
        if(j && j.status === 'success' && Array.isArray(j.data)){
          FAQS = j.data.map(x=>({ q:x.question, a:x.answer, tags: (x.tags||'').split(/[,\s]+/).filter(Boolean) })).concat(FAQS);
          return;
        }
      }
      throw new Error('Endpoint not available');
    }catch(_){
      // Fall back to bundled JSON file if endpoint fails
      try{
        const res = await fetch(base + 'assets/faqs.json?v='+(Date.now()));
        if(!res.ok) throw new Error('Failed to load FAQs');
        const data = await res.json();
        if(Array.isArray(data)) FAQS = data.concat(FAQS);
      }catch(e){
        // final fallback: embed a few basics
        FAQS = [
          { q: 'How do I log in?', a: 'Click the “USER” button, enter your email/ID and password, then submit.', tags:['login'] },
          { q: 'How to register a new account?', a: 'Click “Register” on the selection page. Fill details, capture photo, submit.', tags:['register'] },
          { q: 'Where do I upload archives?', a: 'Open “Archives” in the nav and click Submit Thesis/Capstone.', tags:['archives','upload'] },
        ].concat(FAQS);
      }
    }
  }

  function fuzzyScore(s, t){
    s = s.toLowerCase(); t = t.toLowerCase();
    if (s === t) return 100;
    let score = 0;
    const sw = s.split(/\s+/);
    const tw = t.split(/\s+/);
    sw.forEach(w => { if(t.includes(w)) score += 20; });
    tw.forEach(w => { if(s.includes(w)) score += 10; });
    return Math.min(score, 95);
  }

  function findAnswer(text){
    if(!text) return null;
    if(/^(tour|guide|walkthrough)$/i.test(text.trim())) return {q:'Start a guided tour', a:null, startTour:true};
    let best = {score:-1, item:null};
    FAQS.forEach(item => {
      const sc = fuzzyScore(text, item.q) + ((item.tags||[]).some(t=> text.toLowerCase().includes(t)) ? 10 : 0);
      if(sc > best.score) best = {score: sc, item};
    });
    return best.item;
  }

  function createEl(tag, cls){ const el = document.createElement(tag); if(cls) el.className = cls; return el; }

  async function renderWidget(){
    await loadFaqs();
    if (document.getElementById('help-bot-launcher')) return; // singleton
    const launcher = createEl('button','help-bot-launcher');
    launcher.id = 'help-bot-launcher';
    launcher.title = 'Help & FAQs';
    launcher.textContent = '?';
    const panel = createEl('div','help-bot-panel');
    panel.id = 'help-bot-panel';

    const header = createEl('div','help-bot-header');
    header.innerHTML = '<span>Help & FAQs</span>';
    const close = createEl('button','hb-close'); close.innerHTML = '&times;';
    header.appendChild(close);

    const body = createEl('div','help-bot-body');
    const footer = createEl('div','help-bot-footer');
    const input = createEl('input'); input.placeholder = 'Ask a question or type “tour”...';
    const send = createEl('button'); send.textContent = 'Send';
    footer.appendChild(input); footer.appendChild(send);

    panel.appendChild(header); panel.appendChild(body); panel.appendChild(footer);
    document.body.appendChild(launcher);
    document.body.appendChild(panel);

    // Seed suggestions
    const suggWrap = createEl('div');
    (function seed(){
      const firstThree = FAQS.filter(f=> DEFAULT_SUGGESTIONS.includes(f.q)).concat(FAQS.slice(0,3)).slice(0,3);
      firstThree.forEach(item => {
        const s = createEl('button','hb-suggestion'); s.type='button'; s.textContent = item.q;
        s.addEventListener('click',()=> addQA(item.q, item.a));
        suggWrap.appendChild(s);
      });
    })();
    // Category chips
    const cats = createEl('div');
    CATEGORIES.forEach(c=>{
      const chip = createEl('button','hb-suggestion'); chip.textContent=c.label;
      chip.addEventListener('click',()=> showCategory(c.tag));
      cats.appendChild(chip);
    });
    body.appendChild(cats);
    const tourBtn = createEl('button','hb-suggestion'); tourBtn.textContent = 'Start Tour';
    tourBtn.addEventListener('click',()=> startTour());
    suggWrap.appendChild(tourBtn);
    body.appendChild(suggWrap);

    function addMsg(text, who){
      const msg = createEl('div','hb-msg '+who);
      const bubble = createEl('div','hb-bubble'); bubble.textContent = text;
      msg.appendChild(bubble); body.appendChild(msg); body.scrollTop = body.scrollHeight;
    }
    function addQA(q, a){ addMsg(q,'user'); if(a) addMsg(a,'bot'); else startTour(); }
    function showCategory(tag){
      const list = FAQS.filter(f=> (f.tags||[]).includes(tag)).slice(0,6);
      const wrap = createEl('div');
      if(!list.length){ addMsg('No FAQs for '+tag+'. Try asking your question directly.', 'bot'); return; }
      list.forEach(item=>{
        const s = createEl('button','hb-suggestion'); s.type='button'; s.textContent = item.q;
        s.addEventListener('click',()=> addQA(item.q, item.a));
        wrap.appendChild(s);
      });
      body.appendChild(wrap); body.scrollTop = body.scrollHeight;
    }
    function onSend(){
      const txt = input.value.trim(); if(!txt) return;
      input.value = '';
      addMsg(txt,'user');
      const ans = findAnswer(txt);
      if(!ans) addMsg('Sorry, I could not find an answer. Try rephrasing, choose a category above, or open the Help page.', 'bot');
      else if(ans.startTour) startTour();
      else addMsg(ans.a,'bot');
    }

    launcher.addEventListener('click',()=>{ panel.style.display = panel.style.display==='block' ? 'none':'block'; });
    close.addEventListener('click',()=>{ panel.style.display = 'none'; });
    send.addEventListener('click', onSend);
    input.addEventListener('keydown', (e)=>{ if(e.key==='Enter') onSend(); });
  }

  // Guided tour implementation (no external deps)
  function startTour(){
    const steps = collectSteps();
    if(!steps.length){ alert('No tour targets found on this page.'); return; }
    const overlay = document.querySelector('.hb-tour-overlay') || (function(){ const o = createEl('div','hb-tour-overlay'); document.body.appendChild(o); return o; })();
    overlay.style.display = 'block';
    let idx = 0; let tip = document.querySelector('.hb-tour-tooltip') || createEl('div','hb-tour-tooltip');
    document.body.appendChild(tip);

    function positionTip(target){
      const rect = target.getBoundingClientRect();
      target.scrollIntoView({behavior:'smooth', block:'center'});
      const viewportW = window.innerWidth;
      const viewportH = window.innerHeight;
      const padding = 10;
      let top, left, arrowClass = '';
      if(rect.right + padding + 280 < viewportW){
        top = window.scrollY + rect.top;
        left = window.scrollX + rect.right + padding;
        arrowClass = 'hb-arrow-left';
      } else if(rect.left - padding - 280 > 0){
        top = window.scrollY + rect.top;
        left = window.scrollX + rect.left - padding - 280;
        arrowClass = 'hb-arrow-right';
      } else if(rect.bottom + padding + 140 < viewportH){
        top = window.scrollY + rect.bottom + padding;
        left = window.scrollX + Math.max(10, rect.left);
        arrowClass = 'hb-arrow-top';
      } else {
        top = window.scrollY + rect.top - padding - 160;
        left = window.scrollX + Math.max(10, rect.left);
        arrowClass = 'hb-arrow-bottom';
      }
      tip.style.top = top + 'px';
      tip.style.left = left + 'px';
      tip.classList.remove('hb-arrow-left','hb-arrow-right','hb-arrow-top','hb-arrow-bottom');
      tip.classList.add(arrowClass);
      target.classList.add('hb-focus-ring');
    }

    function clearOutline(){ steps.forEach(s => { if(s.el) s.el.classList && s.el.classList.remove('hb-focus-ring'); }); }

    function render(){
      clearOutline();
      const s = steps[idx];
      positionTip(s.el);
      tip.innerHTML = '<div style="font-weight:700;margin-bottom:6px;">'+s.title+'</div><div>'+s.text+'</div>';
      const ctrls = createEl('div','hb-tour-controls');
      const prev = createEl('button','hb-prev'); prev.textContent='Prev'; prev.disabled = idx===0;
      const next = createEl('button','hb-next'); next.textContent = idx===steps.length-1 ? 'Finish' : 'Next';
      const end = createEl('button','hb-end'); end.textContent='End';
      ctrls.appendChild(prev); ctrls.appendChild(next); ctrls.appendChild(end);
      tip.appendChild(ctrls);
      prev.onclick = ()=>{ if(idx>0){ idx--; render(); } };
      next.onclick = ()=>{ if(idx<steps.length-1){ idx++; render(); } else finish(); };
      end.onclick = finish;
      document.addEventListener('keydown', escHandler);
    }
    function finish(){
      clearOutline(); overlay.style.display='none'; tip.remove();
      document.removeEventListener('keydown', escHandler);
    }
    function escHandler(e){ if(e.key === 'Escape') finish(); }
    render();
  }

  function collectSteps(){
    const steps = [];
    const tryAdd = (sel, title, text)=>{
      const el = document.querySelector(sel);
      if(el) steps.push({el, title, text});
    };
    // Generic site targets
    tryAdd('#top-Nav, nav.navbar, nav.main-header', 'Navigation', 'Use the top navigation to reach pages like Home, Archives, Projects, and Admin.');
    tryAdd('.pup-login-title, h1.page-title, .content-header h1', 'Page Title', 'Each page shows a clear title and optional subtitle for quick context.');
    tryAdd('.pup-role-btn, .btn-primary, .btn', 'Primary Actions', 'Buttons trigger primary actions like Log In, Register, or Save.');
    tryAdd('aside.main-sidebar, .sidebar', 'Sidebar', 'Admins use the sidebar to access modules like Users, Departments, and System Info.');
    tryAdd('section.content, .pup-login-container', 'Content Area', 'This is where main content is displayed and forms are filled.');
    // Context-aware admin hints
    if(document.querySelector('aside.main-sidebar')){
      tryAdd('a.nav-link.nav-home', 'Dashboard', 'Find key stats and quick links to common admin tasks.');
      tryAdd('a.nav-link.nav-registration_list', 'Applications', 'View and filter all applications. Click a row to review details.');
      tryAdd('a.nav-link.nav-manage_registration', 'Create Registration', 'Start a new registration from here and save when complete.');
      tryAdd('a.nav-link.nav-faqs', 'Help & FAQs', 'Open the admin FAQs anytime for guidance on tasks and features.');
    }
    return steps;
  }

  // Public API
  window.HelpBot = {
    init: function(){ renderWidget(); },
    startTour
  };

  // Auto-init when DOM ready
  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', ()=> window.HelpBot.init());
  } else {
    window.HelpBot.init();
  }
})();
