export function usePlural (word: string = '', number: number = 0): string {
    console.log(number)
    return word + (number !== 1 ? 's' : '')
}
