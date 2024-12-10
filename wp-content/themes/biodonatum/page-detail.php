<?php
/* Template Name: Detail Page */
?>
<? get_template_part('components/header'); ?>

        <main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= home_url(); ?>" class="breadcrumbs__link">to index</a>
                        <a href="<?= get_permalink(get_page_by_path('blog')); ?>" class="breadcrumbs__link">Blog</a>
                        <span class="breadcrumbs__link">Science helps people</span>
                    </div>
                </div>
            </section>
            <section class="section section--bg section--hero blog-detail__hero" style="background-image: url('<?= get_template_directory_uri(); ?>/assets/images/blog/blog-detail.png')">
            </section>
            <section class="section pt-50">
                <div class="container">
                    <div class="blog-detail">
                        <div class="head">
                            <h2 class="title">Science helps people</h2>
                        </div>
                        <div class="editor">
                            <p>
                                The human gene network interacts with the microbial gene network, both in time and space.
                            </p>
                            <p>
                                The human microbiome has evolved into a remarkably diverse, delicately balanced, and highly environmentally dependent ecosystem. Each body site represents a distinct habitat that can include trillions of microbial cells and hundreds of strains that vary almost entirely from one site to another throughout the body.
                            </p>
                            <p>
                                “Then I almost always saw with great surprise that in this object there were many very small living animals, moving very beautifully.” Antonie van Leeuwenhoek (1632–1723)
                            </p>
                            <p>
                                While there is no doubt that germs create some of the world’s biggest problems (malaria, cholera, foodborne illnesses and other infectious diseases), the reality is that 99% of germs do not cause disease. There are many beneficial microbes that contribute to food production (eg, bread, cheese, yogurt, chocolate, coffee, beer); soil production and regeneration; decomposition of pollutants and toxins; oxygen production; and the health of plants, animals and humans. Every living thing on this planet has a microbiome… associated microbes that support health and well-being. Microbiome is the complete set of microbes (bacteria, viruses, including bacteriophages, fungi, protozoa) and their genes and genomes in or on the human body. That beneficial microbes live in and on the human body is not a new concept.
                            </p>
                            <p>
                                But it took us four centuries to really look at these microbial communities as deeply as possible and look at them as more than just pathogens. Notably, environmental microbiology and microbial ecology and evolution do provide a conceptual framework for… recognizing that the vast majority of microbes that live in and on us are not microbes or pathogens, but belong to them and do help maintain our health and well-being.
                            </p>
                        </div>
                        <div class="island">
                            We cooperate with research institutes and centers. Our in vitro and ex vivo clinical work allows us to discover new microbiotic benefits for human health and contribute to new advances in the study and understanding of the microbiome and probiotic organisms.
                            <br>
                            Probiotic microorganisms have enormous potential for our future. Their use is so extensive that today we are already on the path of global changes in the treatment and prevention of many diseases.
                            <br>
                            We still have a lot to learn about our little helper friends who are capable of solving problems on a planetary scale that will help us preserve our beautiful home – planet Earth for future generations.
                        </div>
                    </div>
                </div>
            </section>

            <? get_template_part('components/feedback'); ?>

        </main>

<? get_template_part('components/footer'); ?>
