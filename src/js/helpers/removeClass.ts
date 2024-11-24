export const removeClass = (els: NodeListOf<Element>) => {
    els.forEach(el => el.classList.remove('active'))
}