import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import './bootstrap';

// Glob memetakan nama page Laravel ke file React pada folder Pages.
const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });
createInertiaApp({
    resolve: (name) => pages[`./Pages/${name}.jsx`],
    setup({ el, App, props }) { createRoot(el).render(<App {...props} />); },
    progress: { color: '#2962ff' },
});
