import React from 'react';
import { createRoot } from 'react-dom/client';
import GameMenu from './react/GameMenu';

const container = document.getElementById('react-root');
if (container) {
  const root = createRoot(container);
  root.render(<GameMenu />);
}