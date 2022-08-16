import { onBeforeUnmount } from "vue"

const useKeystrokes = (keyCombos) => {
    
    const onKeyDown = (event) => {
        const kc = keyCombos.find(kc => kc.key == event.key)
        if(kc) kc.fn()
    }
    
    window.addEventListener('keydown', onKeyDown)
    onBeforeUnmount(() => {
        window.removeEventListener('keydown', onKeyDown)
    })
}

export default useKeystrokes