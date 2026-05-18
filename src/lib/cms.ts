/**
 * CMS Content Reader — reads Keystatic JSON at build time
 * Only used in pre-rendered (static) pages, never in SSR routes.
 */
import fs from 'node:fs';
import path from 'node:path';

const CONTENT_DIR = path.join(process.cwd(), 'src/content/keystatic');

export function readSingleton<T>(name: string): T {
  const filePath = path.join(CONTENT_DIR, `${name}.json`);
  return JSON.parse(fs.readFileSync(filePath, 'utf-8'));
}

export function readCollection<T>(name: string): T[] {
  const dirPath = path.join(CONTENT_DIR, name);
  const entries = fs.readdirSync(dirPath, { withFileTypes: true });
  return entries
    .filter(entry => entry.isDirectory())
    .map(entry => {
      const jsonPath = path.join(dirPath, entry.name, 'index.json');
      return JSON.parse(fs.readFileSync(jsonPath, 'utf-8'));
    });
}
