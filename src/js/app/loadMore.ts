class LoadMore {
    loadMoreBtn:HTMLElement;
    items:HTMLElement[];

    constructor(el:HTMLElement) {
        this.items = Array.from(el.children) as HTMLElement[];
        this.loadMoreBtn = el.parentElement.querySelector('.load-more-btn');

        this.init();
    }

    init() {
        this.loadMoreBtn.addEventListener('click', e => {
            this.showAll();
            this.loadMoreBtn.style.display = 'none';
        });
    }

    showAll() {
        this.items.forEach(item => {
            item.style.display = 'flex';
        });
    }
}

export default LoadMore;
