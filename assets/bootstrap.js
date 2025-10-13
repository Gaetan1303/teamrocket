// assets/bootstrap.js

// Import Stimulus depuis le package NPM correct
import { startStimulusApp } from '@symfony/stimulus-bridge';

// Démarre l'application Stimulus
const app = startStimulusApp(require.context(
    './controllers', // dossier où se trouvent tes controllers
    true,            // sous-dossiers inclus
    /\.js$/          // fichiers JS uniquement
));

// Ici tu peux enregistrer des controllers personnalisés si nécessaire
// Exemple :
// import SomeController from './controllers/some_controller.js';
// app.register('some', SomeController);

// Import du point d'entrée React
import './app.jsx';
