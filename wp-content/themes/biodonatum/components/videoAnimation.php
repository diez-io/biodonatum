<section class="section">
    <div class="video-container video-animation">
        <div class="scroll-video-wrapper">
            <video class="scroll-video scroll-video__forward" playsinline muted data-video-src="<?= wp_get_attachment_url(get_static_content('video_animation')) ?>" data-video-src-mob="<?= wp_get_attachment_url(get_static_content('video_animation_mob')) ?>">
            </video>
            <video class="scroll-video scroll-video__backward" playsinline muted data-video-src="<?= wp_get_attachment_url(get_static_content('video_animation_reverse')) ?>" data-video-src-mob="<?= wp_get_attachment_url(get_static_content('video_animation_mob_reverse')) ?>">
            </video>
        </div>
    </div>
</section>
