import { marked } from 'marked'
import DOMPurify from 'isomorphic-dompurify'

export function useMarkdown (content: string | null | undefined): string {
    if (!content) {
        return ''
    }

    const markdown = marked.parse(content) as string
    return DOMPurify.sanitize(markdown)
}
