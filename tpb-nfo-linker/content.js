const urlPattern = /https?:\/\/[^\s<>"']+/g;
const trailingPunctuationPattern = /[.,;!?]+$/;

for (const nfo of document.querySelectorAll('.nfo pre')) {
    const walker = document.createTreeWalker(nfo, NodeFilter.SHOW_TEXT);
    const textNodes = [];

    while (walker.nextNode()) {
        if (!walker.currentNode.parentElement.closest('a')) {
            textNodes.push(walker.currentNode);
        }
    }

    for (const textNode of textNodes) {
        const text = textNode.nodeValue;
        const matches = [...text.matchAll(urlPattern)];

        if (matches.length === 0) {
            continue;
        }

        const fragment = document.createDocumentFragment();
        let offset = 0;

        for (const match of matches) {
            const trailingPunctuation = match[0].match(trailingPunctuationPattern)?.[0] ?? '';
            const url = match[0].slice(0, match[0].length - trailingPunctuation.length);
            const link = document.createElement('a');

            link.href = url;
            link.textContent = url;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';

            fragment.append(text.slice(offset, match.index), link, trailingPunctuation);
            offset = match.index + match[0].length;
        }

        fragment.append(text.slice(offset));
        textNode.replaceWith(fragment);
    }
}
