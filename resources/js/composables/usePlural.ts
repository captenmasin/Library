export function usePlural (word: string = '', number: number = 0): string {
    return word + (number !== 1 ? 's' : '')
}
