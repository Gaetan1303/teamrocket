import React, { useEffect } from 'react';

export default function GameMenu() {
  // Le JS vanilla est chargé via le script <script defer>
  // Il s’auto-exécute donc rien à faire ici.
  useEffect(() => {
    // si je veux lancer quelque chose après le montage
  }, []);

  return (
    <section className="text-center mt-5">
      <h2 className="mb-3">🚀 Team Rocket – Premier vol</h2>
      <p className="mb-4">
        Clique sur <strong>Lancer le vol</strong> pour capturer ton premier Pokémon
        et devenir un sbire officiel !
      </p>
    </section>
  );
}