import { resolve } from "path";

class VideoAnimation {

    el: Element;
    videoForward: HTMLVideoElement;
    videoBackward: HTMLVideoElement;
    scrollVideoWrapper: HTMLElement;
    position: 'up' | 'down';
    videoDuration: number;
    tuneForwardSpeed: number;
    tuneDownwardSpeed: number;
    maxWidth: number;
    startDelta: number;
    previousTouch: number;
    supportedPlaybackRange: [number, number];
    isScrollLocked: boolean;
    smallestDelta: number;
    biggestDelta: number;
    currentDirection: 'forward' | 'backword';
    inertiaTimeoutId: NodeJS.Timeout;
    tuneInertia: number;
    headerElement: HTMLElement;

    constructor(el: Element) {
        this.el = el;
        this.videoForward = el.querySelector('.scroll-video__forward');
        this.videoBackward = el.querySelector('.scroll-video__backward');
        this.scrollVideoWrapper = el.querySelector('.scroll-video-wrapper');
        this.headerElement = document.querySelector('.header');

        //this.tuneForwardSpeed = 10;
        this.tuneForwardSpeed = 3;
        this.tuneDownwardSpeed = 9000;

        this.maxWidth = 1610;
        this.supportedPlaybackRange = [0, 0];
        this.findSupportedPlaybackRange();

        const isMobile = window.innerWidth < 768;
        this.videoForward.src = isMobile ? this.videoForward.dataset.videoSrcMob : this.videoForward.dataset.videoSrc;
        this.videoBackward.src = isMobile ? this.videoBackward.dataset.videoSrcMob : this.videoBackward.dataset.videoSrc;
        this.videoForward.load();
        this.videoBackward.load();

        this.isScrollLocked = false;

        this.smallestDelta = 1000;
        this.biggestDelta = 0;
        this.calibrateWheelDelta();
        this.calibrateTouchDelta();
        this.inertiaTimeoutId = null;
        this.tuneInertia = 0.1;

        const waitForMetadata = (video: HTMLVideoElement): Promise<void> => {
            return new Promise((resolve) => {
                if (video.readyState >= 1) {
                    resolve();
                }
                else {
                    video.addEventListener('loadedmetadata', () => resolve(), { once: true });
                }
            });
        };

        const initializeAfterMetadata = async (video1: HTMLVideoElement, video2: HTMLVideoElement) => {
            await Promise.all([waitForMetadata(video1), waitForMetadata(video2)]);

            this.init();
        };

        initializeAfterMetadata(this.videoForward, this.videoBackward);
    }

    init() {
        this.videoForward.play();
        this.videoBackward.play();
        this.videoForward.pause();
        this.videoBackward.pause();
        this.videoDuration = this.videoForward.duration;
        this.startDelta = 2 * window.innerHeight / 3;

        const rect = this.el.getBoundingClientRect();
        this.position = rect.top > 0 ? 'down' : 'up';

        if (this.position === 'up') {
            this.videoForward.currentTime = this.videoDuration;
            this.videoBackward.currentTime = 0;
            this.videoForward.style.visibility = 'hidden';
            this.currentDirection = 'backword';
        }
        else {
            this.videoForward.currentTime = 0;
            this.videoBackward.currentTime = this.videoDuration;
            this.videoBackward.style.visibility = 'hidden';
            this.currentDirection = 'forward';
        }

        window.addEventListener("scroll", this.scrollEventHandler);

        window.addEventListener("wheel", (e: WheelEvent) => {
            if (!this.isScrollLocked) return;

            this.inertiaTimeoutId && clearTimeout(this.inertiaTimeoutId);

            e.preventDefault();
            this.playVideo(e.deltaY);

            this.inertiaTimeoutId = setInterval(() => {
                this.inertia();
            }, 50);
        }, { passive: false });

        window.addEventListener("touchstart", (event: TouchEvent) => {
            if (event.touches.length === 1) {
                this.previousTouch = event.touches[0].clientY;
            }
        }, { passive: false });

        const touchmoveHandler = async (event: TouchEvent) => {
            if (this.isScrollLocked) {
                event.preventDefault();
                event.stopImmediatePropagation();
                event.stopPropagation();
            }
            else {
                return;
            }

            window.removeEventListener("touchmove", touchmoveHandler);

            await new Promise(resolve => {
                setTimeout(resolve, 50);
            });

            window.addEventListener("touchmove", touchmoveHandler, { passive: false });

            if (event.touches.length === 1) {
                this.inertiaTimeoutId && clearTimeout(this.inertiaTimeoutId);

                const deltaTouch = this.previousTouch - event.touches[0].clientY;
                this.playVideo(deltaTouch);
                this.previousTouch = event.touches[0].clientY;

                this.inertiaTimeoutId = setInterval(() => {
                    this.inertia();
                }, 50);
            }
        }

        window.addEventListener("touchmove", touchmoveHandler, { passive: false });

        // window.addEventListener("touchend", (event: TouchEvent) => {
        //     console.log('touchend');
        // });
    }

    scrollEventHandler = (e: Event) => {
        const rect = this.el.getBoundingClientRect();

        if (!this.isScrollLocked) {
            if (this.position === 'down' && rect.top <= this.startDelta && rect.top > 0) {
                this.transformContainer(rect.top);
            }

            if (this.position === 'up' && (window.innerHeight - rect.bottom) <= this.startDelta && (window.innerHeight - rect.bottom) > 0) {
                this.transformContainer(window.innerHeight - rect.bottom);
            }

            if (
                (this.position === 'up' && rect.top > 0) ||
                (this.position === 'down' && rect.top <= 0)
            ) {
                this.lockScroll();
            }
        }
        else {
            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();
        }
    }

    inertia = () => {
        const maxInertia = 1;
        const minInertia = 0.1;

        const computeInertia = (currentRate: number) => {
            const range = currentRate - this.supportedPlaybackRange[0];
            const maxRange = 20.0;

            return Math.max(minInertia, (range / maxRange) * maxInertia);
        };

        if (this.currentDirection === 'forward') {
            if (this.videoForward.playbackRate > this.supportedPlaybackRange[0]) {
                let playbackRate = this.videoForward.playbackRate - computeInertia(this.videoForward.playbackRate);
                playbackRate = playbackRate < this.supportedPlaybackRange[0] ? this.supportedPlaybackRange[0] : playbackRate;
                this.videoForward.playbackRate = playbackRate;
            }
            else {
                this.videoForward.pause();
                clearTimeout(this.inertiaTimeoutId);
            }
            this.videoBackward.currentTime = this.videoDuration - this.videoForward.currentTime;
        }
        else {
            if (this.videoBackward.playbackRate > this.supportedPlaybackRange[0]) {
                let playbackRate = this.videoBackward.playbackRate - this.tuneInertia;
                playbackRate = playbackRate < this.supportedPlaybackRange[0] ? this.supportedPlaybackRange[0] : playbackRate;
                this.videoBackward.playbackRate = playbackRate;
            }
            else {
                this.videoBackward.pause();
                clearTimeout(this.inertiaTimeoutId);
            }
            this.videoForward.currentTime = this.videoDuration - this.videoBackward.currentTime;
        }
    };

    lockScroll = () => {
        this.isScrollLocked = true;
        document.body.style.overflow = 'hidden';
        this.el.scrollIntoView(true);
        this.videoForward.classList.add('video-container--fixed');
        this.videoBackward.classList.add('video-container--fixed');
    };

    unlockScroll = () => {
        document.body.style.overflow = '';
        this.videoForward.classList.remove('video-container--fixed');
        this.videoBackward.classList.remove('video-container--fixed');
        const rect = this.el.getBoundingClientRect();

        if (this.position === 'down') {
            window.scrollBy(0, rect.top - 2);
        }
        else {
            window.scrollBy(0, rect.top + 2);
        }

        this.isScrollLocked = false;
    };

    playVideo = async (deltaY: number) => {
        if (deltaY < 0 && this.videoDuration - this.videoBackward.currentTime > 0.1) {
            // this.video.pause();
            // //const scrollDelta = Math.sign(deltaY) * 0.001;
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

            this.currentDirection = 'backword';
            this.videoForward.pause();
            this.videoForward.currentTime = this.videoDuration - this.videoBackward.currentTime;
            this.videoForward.style.visibility = 'hidden';
            this.videoBackward.style.visibility = '';
            this.videoBackward.play();
            let playbackRate = Math.abs(deltaY) / this.tuneForwardSpeed;
            playbackRate = playbackRate < this.supportedPlaybackRange[0] ? this.supportedPlaybackRange[0] : playbackRate;
            playbackRate = playbackRate > this.supportedPlaybackRange[1] ? this.supportedPlaybackRange[1] : playbackRate;


            this.videoBackward.playbackRate = playbackRate;



            // window.removeEventListener("wheel", this.playVideo);

            // await new Promise(resolve => {
            //     setTimeout(resolve, 50);
            // });

            // window.addEventListener("wheel", this.playVideo, { passive: false });

            //this.videoBackward.pause();
            // this.videoForward.currentTime = this.videoDuration - this.videoBackward.currentTime;
        }
        else if (deltaY > 0 && this.videoDuration - this.videoForward.currentTime > 0.1) {
            this.currentDirection = 'forward';

            this.videoBackward.pause();
            this.videoBackward.currentTime = this.videoDuration - this.videoForward.currentTime;
            this.videoForward.style.visibility = '';
            this.videoBackward.style.visibility = 'hidden';
            this.videoForward.play();
            let playbackRate = Math.abs(deltaY) / this.tuneForwardSpeed;
            playbackRate = playbackRate < this.supportedPlaybackRange[0] ? this.supportedPlaybackRange[0] : playbackRate;
            playbackRate = playbackRate > this.supportedPlaybackRange[1] ? this.supportedPlaybackRange[1] : playbackRate;


            this.videoForward.playbackRate = playbackRate;

            //window.removeEventListener("wheel", this.playVideo);

            // await new Promise(resolve => {
            //     setTimeout(resolve, 50);
            // });

            // window.addEventListener("wheel", (e: WheelEvent) => {
            //     e.preventDefault();
            //     this.playVideo(e.deltaY);
            // }, { passive: false });

            //this.videoForward.pause();
            // this.videoBackward.currentTime = this.videoDuration - this.videoForward.currentTime;
        }

        if (
            this.currentDirection === 'forward' &&
            this.videoDuration - this.videoForward.currentTime <= 0.1
        ) {
            this.videoForward.currentTime = this.videoDuration;
            this.videoBackward.pause();
            this.videoBackward.currentTime = 0;
            this.position = 'up';
            this.unlockScroll();
        }
        else if (
            this.currentDirection === 'backword' &&
            this.videoDuration - this.videoBackward.currentTime <= 0.1
        ) {
            this.videoBackward.currentTime = this.videoDuration;
            this.videoForward.pause();
            this.videoForward.currentTime = 0;
            this.position = 'down';
            this.unlockScroll();
        }
    };

    transformContainer = (delta: number) => {
        const percent = (this.startDelta - delta) / this.startDelta;

        const width = this.maxWidth + percent * (window.innerWidth - this.maxWidth);
        const padding = 20 - percent * 20;
        const headerBackground = 100 - percent * 32;

        (this.el as HTMLElement).style.maxWidth = width + 'px';
        (this.el as HTMLElement).style.padding = '0 ' + padding + 'px';
        this.scrollVideoWrapper.style.borderRadius = padding + 'px';
        this.headerElement.style.backgroundColor = 'rgb(255 255 255 / ' + headerBackground + '%)';
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
                this.videoForward.playbackRate = slowest;
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
                this.videoForward.playbackRate = fastest;
                fastestMin = fastest;
            } catch (error) {
                fastestMax = fastest;
            }
        }

        this.supportedPlaybackRange[1] = fastestMin;
    };

    calibrateWheelDelta = () => {
        let countEvents = 0;

        function calculate(x: number) {
            if (x <= 2) return 10;
            if (x <= 3) return 30;
            if (x >= 15) return 50;

            const min = 80;
            const middle = 186;
            const max = 300;

            if (x <= 10) {
                return middle + (max - middle) * ((x - 3) / (10 - 3));
            }
            else {
                return max + (min - max) * ((x - 10) / (15 - 10));
            }
        }

        const handleWheelCalibration = (event: WheelEvent) => {
            const deltaY = Math.abs(event.deltaY);

            if (deltaY < this.smallestDelta) {
                this.smallestDelta = deltaY;
                this.tuneForwardSpeed = calculate(deltaY);
            }

            if (deltaY > this.biggestDelta) {
                this.biggestDelta = deltaY;
            }

            if (++countEvents > 150) {
                window.removeEventListener("wheel", handleWheelCalibration);
            }
        }

        window.addEventListener("wheel", handleWheelCalibration);
    };

    calibrateTouchDelta = () => {
        // let countEvents = 0;
        // let previousTouch = 0;
        // let smallestDelta = 1000;

        const setInitialTouch = (e: TouchEvent) => {
            //if (e.touches.length === 1) {
                //previousTouch = e.touches[0].clientY;
            //}
            this.tuneForwardSpeed = 15;
        };

        // const handleTouchCalibration = (e: TouchEvent) => {
        //     if (e.touches.length === 1) {
        //         const clientY = e.touches[0].clientY;
        //         const deltaTouch = Math.abs(previousTouch - clientY);

        //         if (deltaTouch === 0) return;

        //         if (deltaTouch < smallestDelta) {
        //             smallestDelta = deltaTouch;
        //             this.tuneForwardSpeed = 1;
        //         }

        //         previousTouch = clientY;

        //         if (++countEvents > 150) {
        //             window.removeEventListener("touchstart", setInitialTouch);
        //             window.removeEventListener("touchmove", handleTouchCalibration);
        //         }
        //     }
        // }

        window.addEventListener("touchstart", setInitialTouch, { once: true });
        //window.addEventListener("touchmove", handleTouchCalibration, { passive: false });
    };

    scrollToTop() {
        window.removeEventListener("scroll", this.scrollEventHandler);
        clearTimeout(this.inertiaTimeoutId);
        document.body.style.overflow = '';
        this.videoForward.classList.remove('video-container--fixed');
        this.videoBackward.classList.remove('video-container--fixed');

        this.videoBackward.pause();
        this.videoForward.pause();
        this.position = 'down';
        this.currentDirection = 'forward';
        this.videoForward.style.visibility = 'hidden';
        this.videoBackward.style.visibility = '';

        (this.el as HTMLElement).style.maxWidth = '';
        (this.el as HTMLElement).style.padding = '';
        this.scrollVideoWrapper.style.borderRadius = '';
        this.headerElement.style.backgroundColor = '';

        this.videoBackward.currentTime = this.videoDuration;
        this.videoForward.currentTime = 0;
        this.isScrollLocked = false;

        setTimeout(() => window.addEventListener("scroll", this.scrollEventHandler), 1000);
    }
}

export default VideoAnimation;
