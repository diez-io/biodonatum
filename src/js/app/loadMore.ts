class LoadMore {
    loadMoreBtn:HTMLElement;
    items:NodeListOf<HTMLElement>;
    display:string;

    constructor(el:HTMLElement) {
        this.loadMoreBtn = el;
        this.items = el.parentElement.querySelectorAll('.load-more-items');
        this.display = getComputedStyle(this.items[0].firstElementChild).display;

        this.init();
    }

    init() {
        this.loadMoreBtn.addEventListener('click', e => {
            this.showAll();
            this.loadMoreBtn.style.display = 'none';
        });
    }

    showAll() {
        this.items.forEach(items => {
            (Array.from(items.children) as HTMLElement[]).forEach(item => {
                item.style.display = this.display;
            });
        });
    }
}

export default LoadMore;
