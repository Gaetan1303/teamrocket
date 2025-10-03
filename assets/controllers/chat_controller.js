
import { Controller } from '@hotwired/stimulus'

/**
 * Contrôleur Stimulus pour la gestion de l'interface utilisateur du chat.
 * Gère l'envoi des messages via fetch et la réception en temps réel via Mercure/EventSource.
 * Connects to data-controller="chat"
 */
export default class extends Controller {
  // --- Propriétés Values (Données transmises par le HTML, utilisées comme variables) ---
  static values = {
    // Le jeton ou l'identifiant de l'utilisateur actuel. Utilisé dans l'en-tête X-CHAT-TOKEN lors de l'envoi.
    user: { type: String, default: null },
    // L'URL de base du Hub Mercure. Nécessaire pour établir la connexion EventSource.
    mercureUrl: { type: String, default: '' },
    // Tableau des équipes disponibles (contenant id et name) pour le sélecteur.
    teams: { type: Array, default: [] }
  }

  // --- Propriétés Targets (Éléments du DOM auxquels le contrôleur doit se lier) ---
  static targets = ["list", "form", "input", "teamSelect"]

  /**
   * Méthode appelée lors de la connexion du contrôleur à l'élément DOM.
   */
  connect() {
    // wire targets (Assigne les éléments DOM aux propriétés de l'instance)
    this.list = this.hasListTarget ? this.listTarget : null        // Conteneur de la liste des messages
    this.form = this.hasFormTarget ? this.formTarget : null        // Le formulaire d'envoi du message
    this.input = this.hasInputTarget ? this.inputTarget : null      // Le champ de saisie du message
    this.teamSelect = this.hasTeamSelectTarget ? this.teamSelectTarget : null // Le sélecteur d'équipe

    this.populateTeams() // Appelle la fonction pour remplir le sélecteur d'équipe avec les options.

  // La variable stockant le topic Mercure actuellement écouté (par défaut: global).
  // Use an internal URN namespace so it won't be interpreted as an external URL by browsers.
  this.currentTopic = 'urn:teamrocket:chat:global'
    
    // Si l'URL de Mercure est fournie, on s'abonne immédiatement au topic global.
    if (this.mercureUrlValue) {
      this.subscribeToTopic(this.currentTopic)
    }

    // Ajoute un écouteur d'événement sur le changement de valeur du sélecteur d'équipe.
    if (this.teamSelect) {
      this.teamSelect.addEventListener('change', () => this.onTeamChange())
    }
  }

  /**
   * Remplit le sélecteur d'équipe (teamSelect) avec les options "Global" et les équipes fournies.
   */
  populateTeams() {
    const teams = this.teamsValue || [] // Récupère le tableau d'équipes.
    if (!this.teamSelect) return // S'arrête si l'élément teamSelect n'est pas présent.

    // 1. Option par défaut = global
    const empty = document.createElement('option')
    empty.value = ''
    empty.text = 'Global'
    this.teamSelect.appendChild(empty)

    // 2. Options pour chaque équipe
    teams.forEach(t => {
      const opt = document.createElement('option')
      opt.value = t.id // Utilise l'ID de l'équipe comme valeur.
      opt.text = t.name || ('Team ' + t.id) // Affiche le nom ou un nom par défaut.
      this.teamSelect.appendChild(opt)
    })
  }

  /**
   * (Ré)abonne l'EventSource au topic Mercure spécifié.
   * @param {string} topic L'URL complète du topic Mercure.
   */
  subscribeToTopic(topic) {
    if (!this.mercureUrlValue) return
    if (this.es) this.es.close() // Ferme toute connexion EventSource (this.es) existante avant d'en ouvrir une nouvelle.
    
    try {
      // Construction de l'URL EventSource pour le topic
      // Utilise l'API URL pour gérer proprement les query params (topic, jwt)
      const url = new URL(this.mercureUrlValue, window.location.href)
      url.searchParams.append('topic', topic)
      // En dev, si un JWT a été injecté côté template (window.MERCURE_JWT), l'ajoutons
      if (typeof window !== 'undefined' && window.MERCURE_JWT) {
        url.searchParams.append('jwt', window.MERCURE_JWT)
      }
      const eventSourceUrl = url.toString()

      // Crée une nouvelle connexion EventSource.
      this.es = new EventSource(eventSourceUrl)
      
      // Écoute les messages entrants.
      this.es.onmessage = (e) => {
        try {
          const payload = JSON.parse(e.data) // Les données Mercure sont une chaîne JSON.
          this.pushMessage(payload) // Ajoute le message au DOM.
        } catch (err) {
          console.warn('Invalid mercure message', err)
        }
      }
      // Gère les erreurs de la connexion EventSource.
      this.es.onerror = () => console.warn('Mercure EventSource error')
    } catch (err) {
      console.warn('Cannot subscribe to Mercure', err)
    }
  }

  /**
   * Gère le changement de l'équipe sélectionnée et met à jour le topic Mercure.
   */
  onTeamChange() {
    // Récupère l'ID de l'équipe sélectionnée.
    const val = this.teamSelect ? this.teamSelect.value : ''
    
    // Construit le nouveau topic Mercure en fonction de la sélection.
  if (val) this.currentTopic = 'urn:teamrocket:chat:team/' + val
  else this.currentTopic = 'urn:teamrocket:chat:global'
    
    // Ré-abonne l'EventSource au nouveau topic.
    this.subscribeToTopic(this.currentTopic)
  }

  /**
   * Envoie le message au serveur Symfony via une requête POST.
   * Est déclenchée par l'action `submit->chat#send` du formulaire.
   * @param {Event} e L'événement de soumission du formulaire.
   */
  send(e) {
    if (e && e.preventDefault) e.preventDefault() // Empêche le rechargement de page.
    
    // Récupère et nettoie le texte du champ de saisie.
    const text = this.input ? this.input.value.trim() : ''
    if (!text) return // Annule si le message est vide.

    // Récupère l'ID de l'équipe sélectionnée (ou null pour le chat global).
    const teamId = this.teamSelect ? (this.teamSelect.value || null) : null

    // Requête HTTP POST vers l'endpoint du ChatController.php.
    fetch('/chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        // Utilise le X-CHAT-TOKEN pour l'identification côté serveur.
        'X-CHAT-TOKEN': this.userValue || '' 
      },
      // Corps de la requête JSON.
      body: JSON.stringify({ teamId: teamId, message: text })
    }).then(r => {
      // Gère les erreurs de réponse (codes 4xx/5xx).
      if (!r.ok) return r.json().then(j => alert(j.error || 'Erreur'))
      // Si réussi, vide le champ de saisie.
      if (this.input) this.input.value = ''
    }).catch(err => {
      // Gère les erreurs réseau.
      console.error(err)
      alert("Impossible d'envoyer le message")
    })
  }

  /**
   * Ajoute un message reçu du Hub Mercure à la liste d'affichage.
   * @param {object} data Les données du message (auteur, message, teamId, time, email).
   */
  pushMessage(data) {
    // Crée les éléments du DOM pour le message et applique des styles inline pour la mise en forme.
    const item = document.createElement('div')
    item.className = 'chat-item'
    item.style.display = 'flex'
    // ... (autres styles omis pour la concision) ...

    // Partie gauche (avatar)
    const left = document.createElement('div')
    left.innerHTML = this.avatarHtml(data)

    // Partie droite (contenu)
    const right = document.createElement('div')
    right.style.flex = '1'

    // En-tête (auteur + heure)
    const header = document.createElement('div')
    // ... (styles) ...

    const author = document.createElement('div')
    author.style.fontWeight = 'bold'
    author.textContent = data.author || 'anon'

    const meta = document.createElement('div')
    // ... (styles) ...
    meta.title = data.time || ''
    // NOTE : La fonction formatTime est utilisée mais n'est pas définie dans l'extrait.
    meta.textContent = this.formatTime(data.time)

    header.appendChild(author)
    header.appendChild(meta)

    // Contenu du message
    const msg = document.createElement('div')
    msg.textContent = data.message || ''

    // Badge d'équipe (si applicable)
    if (data.teamId) {
      const teamName = this.lookupTeamName(data.teamId) // Recherche le nom de l'équipe.
      if (teamName) {
        const badge = document.createElement('span')
        badge.textContent = teamName
        // ... (styles pour le badge) ...
        author.appendChild(badge)
      }
    }

    // Assemblage final et ajout à la liste.
    right.appendChild(header)
    right.appendChild(msg)
    item.appendChild(left)
    item.appendChild(right)

    if (this.list) {
      this.list.appendChild(item)
      this.list.scrollTop = this.list.scrollHeight // Fait défiler jusqu'au dernier message.
    }
  }

  /**
   * Recherche le nom d'une équipe à partir de son ID dans la liste des équipes connues.
   * @param {number|string} teamId L'ID de l'équipe.
   * @returns {string|null} Le nom de l'équipe ou null.
   */
  lookupTeamName(teamId){
    const teams = this.teamsValue || []
    // Cherche l'objet équipe correspondant (conversion en String pour comparaison sûre).
    const t = teams.find(x => String(x.id) === String(teamId))
    return t ? t.name : null
  }

  /**
   * Génère le code HTML pour l'avatar. Utilise Gravatar si un email est fourni, sinon l'initiale de l'auteur.
   * @param {object} data Les données du message.
   * @returns {string} Le HTML de l'avatar.
   */
  avatarHtml(data){
    const author = data.author || 'A'
    const email = data.email || null
    
    // Logique Gravatar
    if (email) {
      // Hash de l'email en MD5 pour Gravatar.
      const hash = this.md5(email.trim().toLowerCase())
      return `<img src="https://www.gravatar.com/avatar/${hash}?s=40&d=retro" style="width:32px;height:32px;border-radius:50%;margin-right:8px;"/>`
    }
    
    // Logique de l'initiale par défaut
    const initial = String(author).charAt(0).toUpperCase()
    return `<span style="display:inline-block;width:32px;height:32px;border-radius:50%;background:#222;color:#fff;display:flex;align-items:center;justify-content:center;margin-right:8px;font-weight:bold;">${initial}</span>`
  }

 

  /**
   * Implémentation simplifiée de l'algorithme de hachage MD5.
   * Utilisé spécifiquement ici pour générer le hash Gravatar.
   * @param {string} str La chaîne à hacher (ici l'e-mail).
   * @returns {string} Le hash MD5 en hexadécimal.
   */
  md5(str){
    // ... (Implémentation détaillée de l'algorithme MD5 - Code technique pour le hachage) ...
    function cmn(q, a, b, x, s, t){
      a = add32(add32(a, q), add32(x, t))
      return add32((a << s) | (a >>> (32 - s)), b)
    }
    function ff(a, b, c, d, x, s, t){ return cmn((b & c) | ((~b) & d), a, b, x, s, t) }
    function gg(a, b, c, d, x, s, t){ return cmn((b & d) | (c & (~d)), a, b, x, s, t) }
    function hh(a, b, c, d, x, s, t){ return cmn(b ^ c ^ d, a, b, x, s, t) }
    function ii(a, b, c, d, x, s, t){ return cmn(c ^ (b | (~d)), a, b, x, s, t) }
    function md51(s){
      txt = ''
      var n = s.length
      var state = [1732584193, -271733879, -1732584194, 271733878]
      var i
      for (i = 64; i <= s.length; i += 64) {
        md5cycle(state, md5blk(s.substring(i - 64, i)))
      }
      s = s.substring(i - 64)
      var tail = new Array(16).fill(0)
      for (i = 0; i < s.length; i++) tail[i >> 2] |= s.charCodeAt(i) << ((i % 4) << 3)
      tail[i >> 2] |= 0x80 << ((i % 4) << 3)
      if (i > 55) {
        md5cycle(state, tail)
        tail = new Array(16).fill(0)
      }
      tail[14] = n * 8
      md5cycle(state, tail)
      return state
    }
    function md5blk(s){
      var md5blks = []
      for (var i = 0; i < 64; i += 4) {
        md5blks[i >> 2] = s.charCodeAt(i) + (s.charCodeAt(i + 1) << 8) + (s.charCodeAt(i + 2) << 16) + (s.charCodeAt(i + 3) << 24)
      }
      return md5blks
    }
   function md5cycle(x, k){
  // ----------------------------------------------------
  // 1. Initialisation des registres
  // ----------------------------------------------------
  // Extraction des valeurs initiales des quatre registres A, B, C, D du tableau d'état 'x'.
  var a = x[0], b = x[1], c = x[2], d = x[3]

  // ----------------------------------------------------
  // 2. RONDE 1 (Utilise la fonction 'ff')
  //    (7, 12, 17, 22 sont les nombres de rotations appliqués)
  // ----------------------------------------------------
  // 16 opérations, utilisant la fonction FF (F(b,c,d) = (b & c) | ((~b) & d)).
  // Chaque opération modifie le registre 'a', puis 'd', puis 'c', puis 'b', et ainsi de suite.
  a = ff(a, b, c, d, k[0], 7, -680876936)  // k[0] est le mot 0 du bloc d'entrée
  d = ff(d, a, b, c, k[1], 12, -389564586)
  c = ff(c, d, a, b, k[2], 17, 606105819)
  b = ff(b, c, d, a, k[3], 22, -1044525330)
  a = ff(a, b, c, d, k[4], 7, -176418897)
  d = ff(d, a, b, c, k[5], 12, 1200080426)
  c = ff(c, d, a, b, k[6], 17, -1473231341)
  b = ff(b, c, d, a, k[7], 22, -45705983)
  a = ff(a, b, c, d, k[8], 7, 1770035416)
  d = ff(d, a, b, c, k[9], 12, -1958414417)
  c = ff(c, d, a, b, k[10], 17, -42063)
  b = ff(b, c, d, a, k[11], 22, -1990404162)
  a = ff(a, b, c, d, k[12], 7, 1804603682)
  d = ff(d, a, b, c, k[13], 12, -40341101)
  c = ff(c, d, a, b, k[14], 17, -1502002290)
  b = ff(b, c, d, a, k[15], 22, 1236535329) // k[15] est le mot 15 du bloc d'entrée

  // ----------------------------------------------------
  // 3. RONDE 2 (Utilise la fonction 'gg')
  //    (5, 9, 14, 20 sont les nombres de rotations appliqués)
  // ----------------------------------------------------
  // 16 opérations, utilisant la fonction GG (G(b,c,d) = (b & d) | (c & (~d))).
  // Notez l'ordre différent des mots d'entrée 'k' par rapport à la Ronde 1.
  a = gg(a, b, c, d, k[1], 5, -165796510)
  d = gg(d, a, b, c, k[6], 9, -1069501632)
  c = gg(c, d, a, b, k[11], 14, 643717713)
  b = gg(b, c, d, a, k[0], 20, -373897302)
  a = gg(a, b, c, d, k[5], 5, -701558691)
  d = gg(d, a, b, c, k[10], 9, 38016083)
  c = gg(c, d, a, b, k[15], 14, -660478335)
  b = gg(b, c, d, a, k[4], 20, -405537848)
  a = gg(a, b, c, d, k[9], 5, 568446438)
  d = gg(d, a, b, c, k[14], 9, -1019803690)
  c = gg(c, d, a, b, k[3], 14, -187363961)
  b = gg(b, c, d, a, k[8], 20, 1163531501)
  a = gg(a, b, c, d, k[13], 5, -1444681467)
  d = gg(d, a, b, c, k[2], 9, -51403784)
  c = gg(c, d, a, b, k[7], 14, 1735328473)
  b = gg(b, c, d, a, k[12], 20, -1926607734)

  // ----------------------------------------------------
  // 4. RONDE 3 (Utilise la fonction 'hh')
  //    (4, 11, 16, 23 sont les nombres de rotations appliqués)
  // ----------------------------------------------------
  // 16 opérations, utilisant la fonction HH (H(b,c,d) = b ^ c ^ d).
  a = hh(a, b, c, d, k[5], 4, -378558)
  d = hh(d, a, b, c, k[8], 11, -2022574463)
  c = hh(c, d, a, b, k[11], 16, 1839030562)
  b = hh(b, c, d, a, k[14], 23, -35309556)
  a = hh(a, b, c, d, k[1], 4, -1530992060)
  d = hh(d, a, b, c, k[4], 11, 1272893353)
  c = hh(c, d, a, b, k[7], 16, -155497632)
  b = hh(b, c, d, a, k[10], 23, -1094730640)
  a = hh(a, b, c, d, k[13], 4, 681279174)
  d = hh(d, a, b, c, k[0], 11, -358537222)
  c = hh(c, d, a, b, k[3], 16, -722521979)
  b = hh(b, c, d, a, k[6], 23, 76029189)
  a = hh(a, b, c, d, k[9], 4, -640364487)
  d = hh(d, a, b, c, k[12], 11, -421815835)
  c = hh(c, d, a, b, k[15], 16, 530742520)
  b = hh(b, c, d, a, k[2], 23, -995338651)

  // ----------------------------------------------------
  // 5. RONDE 4 (Utilise la fonction 'ii')
  //    (6, 10, 15, 21 sont les nombres de rotations appliqués)
  // ----------------------------------------------------
  // 16 opérations, utilisant la fonction II (I(b,c,d) = c ^ (b | (~d))).
  a = ii(a, b, c, d, k[0], 6, -198630844)
  d = ii(d, a, b, c, k[7], 10, 1126891415)
  c = ii(c, d, a, b, k[14], 15, -1416354905)
  b = ii(b, c, d, a, k[5], 21, -57434055)
  a = ii(a, b, c, d, k[12], 6, 1700485571)
  d = ii(d, a, b, c, k[3], 10, -1894986606)
  c = ii(c, d, a, b, k[10], 15, -1051523)
  b = ii(b, c, d, a, k[1], 21, -2054922799)
  a = ii(a, b, c, d, k[8], 6, 1873313359)
  d = ii(d, a, b, c, k[15], 10, -30611744)
  c = ii(c, d, a, b, k[6], 15, -1560198380)
  b = ii(b, c, d, a, k[13], 21, 1309151649)
  
  // Notez que les indices de k et les constantes sont fixés par la spécification MD5.

  // ----------------------------------------------------
  // 6. Mise à jour de l'état interne
  // ----------------------------------------------------
  // Ajoute les valeurs finales des registres (a, b, c, d) aux valeurs initiales de l'état 'x'.
  // C'est une opération d'addition modulo 2^32, gérée par la fonction 'add32'.
  x[0] = add32(a, x[0])
  x[1] = add32(b, x[1])
  x[2] = add32(c, x[2])
  x[3] = add32(d, x[3])
}
    function add32(a, b){ return (a + b) & 0xFFFFFFFF } // Aide à gérer le dépassement de capacité (overflow) des entiers 32 bits.
    var i, txt
    var result = md51(str)
    var hex = ''
    // Convertit le résultat de l'algorithme en format hexadécimal (la chaîne MD5 finale).
    for (i = 0; i < result.length; i++) {
      var s = result[i]
      var j = 0
      for (j = 0; j < 4; j++) {
        hex += ((s >> (j * 8 + 4)) & 0xF).toString(16) + ((s >> (j * 8)) & 0xF).toString(16)
      }
    }
    return hex
  }
}