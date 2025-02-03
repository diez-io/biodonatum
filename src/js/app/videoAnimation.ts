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
    previousTouch: number;
    supportedPlaybackRange: [number, number];

    constructor(el: Element) {
        this.el = el;
        this.video = el.querySelector('#scroll-video');

        this.tuneForwardSpeed = 3.5;
        this.tuneDownwardSpeed = 3000;

        this.maxWidth = 1610;
        this.supportedPlaybackRange = [0, 0];
        //this.findSupportedPlaybackRange();

        const isMobile = window.innerWidth < 768;
        this.video.src = isMobile ? this.video.dataset.videoSrcMob : this.video.dataset.videoSrc;
        this.video.load();

        setTimeout(() => this.init(), 2000);
    }

    init() {
        this.video.play();
        this.video.pause();
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

            //if (deltaY < 0) {
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
            //}
            //else if (this.video.currentTime !== this.videoDuration) {
                // this.video.play();
                // let playbackRate = Math.abs(deltaY) / this.tuneForwardSpeed;
                // playbackRate = playbackRate < this.supportedPlaybackRange[0] ? this.supportedPlaybackRange[0] : playbackRate;
                // playbackRate = playbackRate > this.supportedPlaybackRange[1] ? this.supportedPlaybackRange[1] : playbackRate;

                // console.log('playbackRate', playbackRate);

                // this.video.playbackRate = playbackRate;

                // window.removeEventListener("wheel", lockScrollAndSyncVideo);

                // await new Promise(resolve => {
                //     setTimeout(resolve, 300);
                // });

                // window.addEventListener("wheel", lockScrollAndSyncVideo, { passive: false });

                // this.video.pause();



                // const scrollDelta = deltaY / this.tuneDownwardSpeed;

                // window.removeEventListener("wheel", lockScrollAndSyncVideo);

                // this.video.currentTime = Math.min(
                //     Math.max(this.video.currentTime + scrollDelta * this.videoDuration, 0),
                //     this.videoDuration
                // );

                // await new Promise(resolve => {
                //     setTimeout(resolve, 50);
                // });

                // window.addEventListener("wheel", lockScrollAndSyncVideo, { passive: false });

            //}

            if (this.video.currentTime === 0) {
                this.position = 'down'
                unlockScroll();
            }
            else if (this.videoDuration - this.video.currentTime <= 0.0001) {
                this.position = 'up';
                unlockScroll();
            }
        };

        const lockScroll = () => {
            isScrollLocked = true;
            this.el.scrollIntoView(true);
            document.body.style.overflow = 'hidden';
            this.video.classList.add('video-container--fixed');
        };

        const unlockScroll = () => {
            this.el.scrollIntoView(true);
            isScrollLocked = false;
            document.body.style.overflow = '';
            this.video.classList.remove('video-container--fixed');
        };

        window.addEventListener("scroll", (e) => {
            const rect = this.el.getBoundingClientRect();

            if (!isScrollLocked) {
                if (this.position === 'down' && rect.top <= this.startDelta && rect.top > 0) {
                    this.transformContainer(rect.top);
                }

                if (this.position === 'up' && (window.innerHeight - rect.bottom) <= this.startDelta && (window.innerHeight - rect.bottom) > 0) {
                    this.transformContainer(window.innerHeight - rect.bottom);
                }

                if (
                    (this.position === 'up' && window.innerHeight - rect.bottom <= 0) ||
                    (this.position === 'down' && rect.top <= 0)
                ) {
                    lockScroll();
                }
            }
            else {
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();
            }
        });

        window.addEventListener("wheel", lockScrollAndSyncVideo, { passive: false });

        window.addEventListener("touchstart", (event: TouchEvent) => {
            if (event.touches.length === 1) {
                this.previousTouch = event.touches[0].clientY;
            }
        }, { passive: false });

        window.addEventListener("touchmove", async (event: TouchEvent) => {
            if (isScrollLocked) {
                event.preventDefault();
                event.stopImmediatePropagation();
                event.stopPropagation();
            }

            if (event.touches.length === 1) {
                const deltaTouch = this.previousTouch - event.touches[0].clientY;
                this.simulateWheelEvent(deltaTouch);
                this.previousTouch = event.touches[0].clientY;
            }
        }, { passive: false });

        // window.addEventListener("touchend", (event: TouchEvent) => {
        //     console.log('touchend');
        // });
    }

    transformContainer = (delta: number) => {
        const width = this.maxWidth + ((this.startDelta - (delta)) / this.startDelta) * (window.innerWidth - this.maxWidth);
        const padding = 20 - ((this.startDelta - delta) / this.startDelta) * 20;

        (this.el as HTMLElement).style.maxWidth = width + 'px';
        (this.el as HTMLElement).style.padding = '0 ' + padding + 'px';
        (this.video as HTMLElement).style.borderRadius = padding + 'px';
    };

    simulateWheelEvent(deltaY: number) {
        const simulatedEvent = new WheelEvent("wheel", {
            deltaY: deltaY,
            bubbles: true,
            cancelable: true,
        });

        window.dispatchEvent(simulatedEvent);
    }

    findSupportedPlaybackRange = () => {
        let slowestMin = 0.0001;
        let slowestMax = 1.0;

        while (slowestMax - slowestMin >= 0.0001) {
            const slowest = (slowestMin + slowestMax) / 2;

            try {
                this.video.playbackRate = slowest;
                slowestMax = slowest;
            } catch (error) {
                slowestMin = slowest;
            }
        }

        this.supportedPlaybackRange[0] = slowestMax;

        let fastestMin = 1.0;
        let fastestMax = 30.0;

        while (fastestMax - fastestMin >= 0.0001) {
            const fastest = (fastestMin + fastestMax) / 2;

            try {
                this.video.playbackRate = fastest;
                fastestMin = fastest;
            } catch (error) {
                fastestMax = fastest;
            }
        }

        this.supportedPlaybackRange[1] = fastestMin;
    };
}

export default VideoAnimation;
