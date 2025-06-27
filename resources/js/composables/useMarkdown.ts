import { marked } from 'marked'
import DOMPurify from 'isomorphic-dompurify'

export function useMarkdown (content: string): string {
    const markdown = marked.parse(content) as string
    return DOMPurify.sanitize(markdown)
}
