<section class="section">
    <div class="video-container video-animation">
        <div class="scroll-video-wrapper">
            <div class="video-container__loader">
                <picture>
                    <img class="video-container__loader__desktop" src="<?= wp_get_attachment_url(get_static_content('video_animation_preview')) ?>" alt="">
                    <img class="video-container__loader__mob" src="<?= wp_get_attachment_url(get_static_content('video_animation_preview_mob')) ?>" alt="">
                </picture>
                <div class="video-container__loader__spinner">
                    <div class="video-container__loader__spinner__element"></div>
                </div>
            </div>
            <video class="scroll-video scroll-video__forward" playsinline muted data-video-src="<?= wp_get_attachment_url(get_static_content('video_animation')) ?>" data-video-src-mob="<?= wp_get_attachment_url(get_static_content('video_animation_mob')) ?>">
            </video>
            <video class="scroll-video scroll-video__backward" playsinline muted data-video-src="<?= wp_get_attachment_url(get_static_content('video_animation_reverse')) ?>" data-video-src-mob="<?= wp_get_attachment_url(get_static_content('video_animation_mob_reverse')) ?>">
            </video>
            <div class="video-container__scroll-fix"></div>
        </div>
    </div>
</section>
