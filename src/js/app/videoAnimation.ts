import { resolve } from "path";

class VideoAnimation {

    el: Element;
    video: HTMLVideoElement;
    position: 'up' | 'down';
    videoDuration: number;
    tuneForwardSpeed: number;
    tuneDownwardSpeed: number;
    maxWidth: number;
    startDelta: number;

    constructor(el: Element) {
        this.el = el;
        this.video = el.querySelector('#scroll-video');

        this.tuneForwardSpeed = 3.5;
        this.tuneDownwardSpeed = 1500;

        this.maxWidth = 1610;

        setTimeout(() => this.init(), 2000);
    }

    init() {
        this.videoDuration = this.video.duration;
        this.startDelta = 2 * window.innerHeight / 3;

        const rect = this.el.getBoundingClientRect();
        this.position = rect.top > 0 ? 'down' : 'up';

        if (this.position === 'up') {
            this.video.currentTime = this.videoDuration;
        }

        let isScrollLocked = false;

        const lockScrollAndSyncVideo = async (event:WheelEvent) => {
            const deltaY = event.deltaY;

            if (!isScrollLocked) return;

            event.preventDefault();

            if (deltaY < 0) {
                //const scrollDelta = Math.sign(deltaY) * 0.001;
                const scrollDelta = deltaY / this.tuneDownwardSpeed;

                window.removeEventListener("wheel", lockScrollAndSyncVideo);

                this.video.currentTime = Math.min(
                    Math.max(this.video.currentTime + scrollDelta * this.videoDuration, 0),
                    this.videoDuration
                );

                await new Promise(resolve => {
                    setTimeout(resolve, 50);
                });

                window.addEventListener("wheel", lockScrollAndSyncVideo, { passive: false });
            }
            else if (this.video.currentTime !== this.videoDuration) {
                this.video.play();
                let playbackRate = Math.abs(deltaY) / this.tuneForwardSpeed;
                playbackRate = playbackRate > 16 ? 16 : playbackRate;

                this.video.playbackRate = playbackRate;

                window.removeEventListener("wheel", lockScrollAndSyncVideo);

                await new Promise(resolve => {
                    setTimeout(resolve, 50);
                });

                window.addEventListener("wheel", lockScrollAndSyncVideo, { passive: false });

                this.video.pause();
            }

            if (this.video.currentTime === 0) {
                this.position = 'down'
                unlockScroll();
            }
            else if (this.video.currentTime === this.videoDuration) {
                this.position = 'up';
                unlockScroll();
            }
        };

        const lockScroll = () => {
            isScrollLocked = true;
            document.body.style.overflow = "hidden";
        };

        const unlockScroll = () => {
            isScrollLocked = false;
            document.body.style.overflow = "";
        };

        window.addEventListener("scroll", () => {
            const rect = this.el.getBoundingClientRect();

            if (!isScrollLocked) {
                if (
                    (this.position === 'up' && window.innerHeight - rect.bottom <= 0) ||
                    (this.position === 'down' && rect.top <= 0)
                ) {
                    this.el.scrollIntoView(true);
                    lockScroll();
                }
            }

            if (this.position === 'down' && rect.top <= this.startDelta) {
                this.transformContainer(rect.top);
            }

            if (this.position === 'up' && (window.innerHeight - rect.bottom) <= this.startDelta) {
                this.transformContainer(window.innerHeight - rect.bottom);
            }
        });

        window.addEventListener("wheel", lockScrollAndSyncVideo, { passive: false });
    }

    transformContainer = (delta: number) => {
        console.log(delta);
        const width = this.maxWidth + ((this.startDelta - (delta)) / this.startDelta) * (window.innerWidth - this.maxWidth);
        const height = 40 + ((this.startDelta - delta) / this.startDelta) * 60;
        const padding = 20 - ((this.startDelta - delta) / this.startDelta) * 20;

        (this.el as HTMLElement).style.maxWidth = width + 'px';
        (this.el as HTMLElement).style.height = height + 'vh';
        (this.el as HTMLElement).style.padding = '0 ' + padding + 'px';
        (this.video as HTMLElement).style.borderRadius = padding + 'px';
    }
}

export default VideoAnimation;
