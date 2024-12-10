<?php
/* Template Name: Scientists Page */
?>
<? get_template_part('components/header'); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= home_url(); ?>" class="breadcrumbs__link">to index</a>
                        <span class="breadcrumbs__link">Our Scientists</span>
                    </div>
                    <div class="scientists">
                        <div class="head">
                            <h2 class="title">Our Scientists</h2>
                        </div>
                        <div class="island">
                            Our Scientific Board is represented by doctors, scientists and researchers in such fields as microbiology, immunology, genetics, metabolomics, gastroenterology, pediatrics, gynecology and cosmetology. They head clinics and laboratories, teach at world-renowned institutes and are authors of scientific journals and publications.
                        </div>
                        <div class="slider" data-slider="scientists">
                            <div class="swiper">
                                <div class="swiper-wrapper load-more-items">
                                    <div class="swiper-slide card">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/scientists/Katya-Khammad.jpeg" alt="">
                                        </picture>
                                        <div class="card__text">
                                            <div class="card__text-title">
                                                Katya Khammad
                                            </div>
                                            <div class="card__text-description">
                                                Doctor of Medical Sciences Gastroenterologist Geriatrics Gerontology Ozone Therapy Member of the International Red Cross
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide card">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/scientists/Vasilii-Khammad.jpeg" alt="">
                                        </picture>
                                        <div class="card__text">
                                            <div class="card__text-title">
                                                Vasilii Khammad
                                            </div>
                                            <div class="card__text-description">
                                                Doctorate in Neuro-Oncology
                                                General Medicine (GM)
                                                Residency in Medical Oncology
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide card">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/scientists/Kalinchenko-Svetlana.jpeg" alt="">
                                        </picture>
                                        <div class="card__text">
                                            <div class="card__text-title">
                                                Svetlana Kalinchenko
                                            </div>
                                            <div class="card__text-description">
                                                Doctor of Medical Sciences
                                                Urologist Endocrinologist Andrologist
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide card">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/scientists/MAJIDOVA-Yokutkhon.jpeg" alt="">
                                        </picture>
                                        <div class="card__text">
                                            <div class="card__text-title">
                                                Yokutkhon Majidova
                                            </div>
                                            <div class="card__text-description">
                                                Professor Doctor of Medical Sciences President of the Association of Neurologists Child Neurology and Medical Genetics
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide card">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/scientists/Katya-Khammad.jpeg" alt="">
                                        </picture>
                                        <div class="card__text">
                                            <div class="card__text-title">
                                                Katya Khammad
                                            </div>
                                            <div class="card__text-description">
                                                Doctor of Medical Sciences Gastroenterologist Geriatrics Gerontology Ozone Therapy Member of the International Red Cross
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide card">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/scientists/Vasilii-Khammad.jpeg" alt="">
                                        </picture>
                                        <div class="card__text">
                                            <div class="card__text-title">
                                                Vasilii Khammad
                                            </div>
                                            <div class="card__text-description">
                                                Doctorate in Neuro-Oncology
                                                General Medicine (GM)
                                                Residency in Medical Oncology
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide card">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/scientists/Kalinchenko-Svetlana.jpeg" alt="">
                                        </picture>
                                        <div class="card__text">
                                            <div class="card__text-title">
                                                Svetlana Kalinchenko
                                            </div>
                                            <div class="card__text-description">
                                                Doctor of Medical Sciences
                                                Urologist Endocrinologist Andrologist
                                            </div>
                                        </div>
                                    </div>
                                    <div class="swiper-slide card">
                                        <picture>
                                            <img src="<?= get_template_directory_uri(); ?>/assets/images/scientists/MAJIDOVA-Yokutkhon.jpeg" alt="">
                                        </picture>
                                        <div class="card__text">
                                            <div class="card__text-title">
                                                Yokutkhon Majidova
                                            </div>
                                            <div class="card__text-description">
                                                Professor Doctor of Medical Sciences President of the Association of Neurologists Child Neurology and Medical Genetics
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="slider__control mob-hidden">
                                    <div class="slider__btn slider__btn--prev">
                                        <svg class="slider__icon">
                                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                        </svg>
                                    </div>
                                    <div class="slider__btn slider__btn--next">
                                        <svg class="slider__icon">
                                            <use xlink:href="<?= get_template_directory_uri(); ?>/assets/sprite.svg#icon-chevrone"></use>
                                        </svg>
                                    </div>
                                </div>
                                <div class="swiper-pagination"></div>
                                <div class="button button--wide scientists__load-more load-more-btn">
                                    LOAD MORE
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <? get_template_part('components/feedback'); ?>
        </main>

<? get_template_part('components/footer'); ?>