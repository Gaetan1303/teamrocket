// public/js/game_first_theft.js
(() => {
  const gameArea   = document.getElementById('game-area');
  const timerEl    = document.getElementById('timer');
  const attemptsEl = document.getElementById('attempts-count');
  const startBtn   = document.getElementById('start-btn');
  const resultEl   = document.getElementById('result');
  const missionEl  = document.getElementById('mission-block');

  let totalTime   = 30;
  let spawnEvery  = 2000;
  let attempts    = 0;
  let timerId     = null;
  let spawnId     = null;
  let current     = null;

  /* 1. Récupérer la mission UNIQUE de la Team */
  fetch('/game/first-theft/mission')
    .then(r => r.json())
    .then(mission => {
      missionEl.innerHTML = `
        <h4>${mission.titre}</h4>
        <p>${mission.description}</p>
        <small><strong>Action :</strong> ${mission.action_verbe}  |  <strong>Cible :</strong> ${mission.cible_type}</small>
      `;
      missionEl.style.display = 'block';
    })
    .catch(() => {
      missionEl.innerHTML = '<p class="text-warning">Impossible de charger la mission…</p>';
      missionEl.style.display = 'block';
    });

  /* 2. Mini-jeu (inchangé) */
  const fmt = s => `${String(Math.floor(s/60)).padStart(2,'0')}:${String(s%60).padStart(2,'0')}`;

  async function fetchPoke(){
    const id = Math.floor(Math.random() * 898) + 1;
    const r  = await fetch(`https://pokeapi.co/api/v2/pokemon/${id}`);
    if(!r.ok) throw new Error('pokeapi');
    return r.json();
  }

  function spawn(){
    document.querySelectorAll('.spawned-poke').forEach(el=>el.remove());
    fetchPoke()
      .then(p=>{
        current = {id:p.id, name:p.name, sprite:p.sprites.front_default};
        const img = document.createElement('img');
        img.src = current.sprite;
        img.className = 'spawned-poke';
        img.style.position='absolute';
        img.style.width='120px';
        img.style.cursor='pointer';
        const rect = gameArea.getBoundingClientRect();
        img.style.left = `${Math.random() * (rect.width-120)}px`;
        img.style.top  = `${Math.random() * (rect.height-120)}px`;
        img.addEventListener('click', catchPoke);
        gameArea.appendChild(img);
        setTimeout(()=>img.remove(),1600);
      })
      .catch(()=>{});
  }

  function catchPoke(){
    attempts++;
    attemptsEl.textContent = attempts;
    end(true);
  }

  function start(){
    attempts = 0; attemptsEl.textContent = attempts;
    resultEl.innerHTML = '';
    startBtn.disabled = true;

    let left = totalTime;
    timerEl.textContent = fmt(left);
    timerId = setInterval(()=>{
      left--;
      timerEl.textContent = fmt(left);
      if(left<=0) end(false);
    },1000);

    spawnId = setInterval(spawn, spawnEvery);
    spawn();
  }

  function end(success){
    clearInterval(timerId); clearInterval(spawnId);
    document.querySelectorAll('.spawned-poke').forEach(el=>el.remove());
    startBtn.disabled = false;

    if(!success){
      resultEl.innerHTML = `<strong>Échec</strong> — temps écoulé. <button id="retry">Réessayer</button>`;
      document.getElementById('retry').addEventListener('click',start);
      return;
    }

    resultEl.innerHTML = `Enregistrement de <strong>${current.name}</strong>…`;
    fetch('/game/first-theft/result',{
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({success:true, starter:current.name, starterId:current.id})
    })
      .then(r=>r.json())
      .then(d=>{
        if(d.success){
          resultEl.innerHTML = `<strong>Succès !</strong> Tu es sbire avec ${current.name}. <a class="btn btn-sm btn-outline-light ms-2" href="/profile">Profil</a>`;
        }else{
          resultEl.innerHTML = `Erreur serveur : ${d.message||'inconnue'}`;
        }
      })
      .catch(()=>{ resultEl.innerHTML = 'Erreur réseau.'; });
  }

  startBtn.addEventListener('click',start);
})();