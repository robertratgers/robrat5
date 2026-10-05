Tailwind/CSS build

This project now includes a Tailwind config and build script.

Prerequisites:
- Node.js and npm installed.

Commands:
- npm install
- npm run build:css    # builds ./assets/gemini-built.css
- npm run watch:css    # watch mode for development

What changed:
- `tailwind.config.js` added at repo root (converted from the JS in assets).
- `package.json` with build scripts.
- `index.php` now links to `assets/gemini-built.css`.

Note: Run `npm install` and then `npm run build:css` on your development machine or in CI to generate `assets/gemini-built.css` before deploying.
