<?php
/* Template Name: About Page */
?>
<? get_header(); ?>

<main class="main">
    <? get_template_part('components/mainHero'); ?>
    <section class="section pt-50">
        <div class="container">
            <div class="breadcrumbs mb-40">
                <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= __('Home', 'static') ?></a>
                <span class="breadcrumbs__link"><?= __('About us', 'static') ?></span>
            </div>
            <div class="about">
                <div class="head">
                    <h2 class="title"><?= __('About us', 'static') ?></h2>
                </div>
                <div class="editor">
                    <p>
                        <?= __('BIODONATUM WAS BORN BY THE BREATH OF THE UNIVERSE FOR YOUR HEALTH.', 'static') ?>
                        <br>
                        <?= __('Our mission is population health and active longevity for every person.', 'static') ?>
                    </p>
                </div>
                <div class="about__stories">
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-1.jpg" alt="">
                            </picture>
                            <picture class="about__stories__block__picture--sub">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-1-1.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                110 years of remarkable heritage
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                Kakutaro Masagaki created a family-run company and today the fourth generation of the family is working in the name of his dream – active longevity and health for every person on the planet.
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-2.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                The story began with Nobel Laureate Ilya Mechnikov
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                Humanity has been searching for a recipe for immortality since ancient times. Avicenna worked to create a cure for longevity, but never found it.
                            </div>
                            <div class="about__stories__block__text--text">
                                The first scientific breakthrough was made by the Soviet scientist, Nobel laureate in the field of medicine Ilya Mechnikov in 1903. It was the works of Ilya Mechnikov that formed the basis of his world-famous Theory of Longevity. Mechnikov studied the human microbiome and bacteria in more than 18 countries. He saw that a record number of people over 100 years old lived in Bulgaria for that time, and the secret of their longevity was sour milk with lactobacilli, which they ate every day. Mechnikov conducted research and saw that the Bulgarian bacillus creates an acidic environment, which has a beneficial effect on the intestinal microflora and prevents the development of putrefactive bacteria. It is the diversity of microflora and the reduction in the number of putrefactive bacteria that affect the quality and duration of life.
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-3.jpg" alt="">
                            </picture>
                            <picture class="about__stories__block__picture--sub">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-3-3.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                About Kakutaro Masagaki
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                The founder
                            </div>
                            <div class="about__stories__block__text--text">
                                Japanese researcher Kakutaro Masagaki in 1905 was inspired by the works of Mechnikov and decided to conduct research in the field of lactobacilli fermentation. He began producing fresh yoghurts in Japan, which were fermented overnight, and in the morning they were delivered to Japanese homes for breakfast. Thanks to yoghurts, Kakutaro and yoghurt consumers were able to cope with intestinal problems, get rid of painful thinness and become healthier. In 1914, he opened a laboratory for the study of lactobacilli in Kyoto. In 1925 by mixing four types of lactic acid bacteria culture solution and sugar solution, we developed and released Ellie’, which is sweet and nutritious. This is the origin of modern lactic acid bacteria drinks.
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-4.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                Kakutaro's eldest son continues his father's research
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                Kaziyoshi Masagaki
                            </div>
                            <div class="about__stories__block__text--text">
                                In 1936, Kakutaro’s son decided to continue his research. He saw that the largest percentage of lactobacilli entering the body with food products die, and those that enter the intestines in most cases are not accepted by the microbiome. Kazuyoshi Masagaki thinks about how to improve the effect of lactobacilli on intestinal microflora. He developed a method for co-cultivating 8 strains of lactis acid bacteria. Established symbiotic culture method for 16 species of lactic acid bacteria. Kazuyoshi Masagaki studied under Kozui Otani, who was also researching microorganisms, in Dalian. Reverend Kozui Otani, a scientist and researcher, was actually the 22nd head priest of Nishi Honganji Temple and a person dedicated to Buddhism. Master Kozui Otani uniquely proposed a method of extracting active ingredients from fermented products of lactic acid bacteria’ based on the description in the Great General Nirvana Sutra. The master responded with an excerpt from a Buddhist text:
                            </div>
                            <div class="about__stories__block__text--quote">
                                “From cows – fresh milk, from fresh milk – cream, from cream – sour milk, then butter and from butter – ghee (Biodonatum). Biodonatum is the best, it contains everything natural for the human body.”
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-5.jpg" alt="">
                            </picture>
                            <picture class="about__stories__block__picture--sub">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-5-5.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                Revolutionary lactobacilli fermentation technology
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                Returning, Kazuyoshi makes a revolution. He is developing technology and proposes to use waste products of lactobacilli, which in the human body will increase its own intestinal bacteria and fight antagonistic microbes.
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-6.jpg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                1982 production of microbiotic Biodonatum began
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                <p>
                                    A method has been developed to extract the active ingredients from the fermentation of lactic acid bacteria.
                                </p>
                                <p>
                                    Not only “secrets of lactic acid bacteria” were isolated, but also “substances that protect the cells of lactic acid bacteria.”
                                </p>
                                <p>
                                    Kazuyoshi fulfilled his father’s dream – the improvement of the Japanese nation and in 1982 the production of the fermentation extract of lactobacilli Baodonatum was started.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="about__stories__block">
                        <div class="about__stories__block__picture">
                            <picture class="about__stories__block__picture--main">
                                <img src="<?= get_template_directory_uri(); ?>/assets/images/about/about-7.jpeg" alt="">
                            </picture>
                        </div>
                        <div class="about__stories__block__text">
                            <div class="about__stories__block__text--title">
                                The family business continues today Masakatsu Fukui
                            </div>
                            <div class="about__stories__block__text--epigraph">
                                Today, the company is run by the fourth generation of the family. The president Yuko Murakoshi and her father Masakatsu Fukui.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section section--bg about__wide" style="background-image: url('<?= get_template_directory_uri(); ?>/assets/images/about/about-wide.png')">
        <div class="about__wide__content">
            <div class="about__wide__content-big"><?= __('2024 Biodonatum is available in the European Union', 'static') ?></div>
            <div class="about__wide__content-small"><?= __('Lactobacillus fermentation extract is now available in the European Union.', 'static') ?></div>
        </div>
    </section>

    <? get_template_part('components/feedback'); ?>

</main>

<? get_footer(); ?>
