import { JSDOM } from 'jsdom';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';

const contentScript = await readFile(new URL('./content.js', import.meta.url), 'utf8');

test('links NFO URLs without changing existing links or trailing punctuation', () => {
    const dom = new JSDOM(
        `
        <div class="nfo"><pre>Mirror: https://example.com/file.
Existing: <a href="https://already.example">https://already.example</a></pre></div>
    `,
        { runScripts: 'outside-only' },
    );

    dom.window.eval(contentScript);

    const links = [...dom.window.document.querySelectorAll('.nfo a')];

    assert.deepEqual(
        links.map((link) => link.href),
        ['https://example.com/file', 'https://already.example/'],
    );
    assert.match(dom.window.document.querySelector('.nfo pre').textContent, /file\.$/m);
});
