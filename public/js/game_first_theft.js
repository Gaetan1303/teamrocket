document.addEventListener('DOMContentLoaded', function () {
  const resultEl = document.getElementById('result');
  if (!resultEl) {
    console.error('Element #result introuvable');
    return;
  }

  function show(msg, isError = false) {
    resultEl.textContent = '';
    const p = document.createElement('div');
    p.textContent = msg;
    if (isError) p.style.color = 'crimson';
    resultEl.appendChild(p);
  }

  if (typeof targetType === 'undefined' || !targetType) {
    show('Erreur : type de mission manquant.', true);
    return;
  }

  // --- Carte Leaflet ---
  const map = L.map('map').setView([48.8566, 2.3522], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap contributors' }).addTo(map);

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => map.setView([pos.coords.latitude, pos.coords.longitude], 15),
      () => {},
      { enableHighAccuracy: true, timeout: 5000 }
    );
  }

  // --- Récupération Pokémon ---
  const url = `/game/first-theft/mission-pokemon/${encodeURIComponent(targetType)}`;
  show(`Recherche d'un Pokémon de type "${targetType}"...`);

  fetch(url, { method: 'GET', credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
    .then(async res => {
      if (!res.ok) { const text = await res.text().catch(()=>''); throw new Error(`Erreur serveur ${res.status} ${text}`); }
      return res.json();
    })
    .then(data => placePokemonOnMap(data))
    .catch(err => { console.error(err); show('Impossible de récupérer un Pokémon : ' + err.message, true); });

  // --- Placement Pokémon ---
  function placePokemonOnMap(poke) {
    show(`Un ${poke.name} apparaît ! Clique dessus pour tenter la capture.`);

    const center = map.getCenter();
    const randOffset = () => (Math.random() - 0.5) * 0.01;
    const lat = center.lat + randOffset();
    const lng = center.lng + randOffset();

    const icon = L.icon({
      iconUrl: poke.sprite || '/images/default_pokemon.png',
      iconSize: [48,48],
      iconAnchor: [24,48],
      popupAnchor: [0,-48],
      className: poke.shiny ? 'pokemon-icon shiny' : 'pokemon-icon'
    });

    const marker = L.marker([lat, lng], { icon }).addTo(map);
    marker.bindPopup(`<strong>${poke.name}</strong><br>Types: ${poke.types.join(', ')}${poke.shiny ? ' ⭐ Shiny' : ''}<br><em>Clique pour capturer</em>`);
    marker.on('click', () => attemptCapture(poke, marker));
  }

  // --- Capture ---
  function attemptCapture(poke, marker) {
    show(`Tentative de capture de ${poke.name}...`);

    fetch('/game/first-theft/result', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken || '' },
      body: JSON.stringify({ starter: poke.name, starterId: poke.id })
    })
    .then(async res => {
      if (!res.ok) { const text = await res.text().catch(()=>''); throw new Error(`Erreur ${res.status} ${text}`); }
      return res.json();
    })
    .then(body => {
      if (body.success) {
        show(body.message);
        marker.bindPopup(`<strong>${poke.name}</strong><br>Capturé !`).openPopup();
      } else {
        show('Échec : ' + (body.message || 'Erreur inconnue'), true);
      }
    })
    .catch(err => { console.error(err); show('Erreur capture : ' + err.message, true); });
  }
});
