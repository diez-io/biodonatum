<?php
/* Template Name: Delivery Page */
?>
<? get_header(); ?>

            <main class="main">
                <section class="section pt-50">
                    <div class="container">
                        <div class="breadcrumbs mb-40">
                            <a href="<?= home_url(); ?>" class="breadcrumbs__link"><?= __('Home', 'static') ?></a>
                            <span class="breadcrumbs__link"><?= __('Delivery terms', 'static') ?></span>
                        </div>
                        <div class="head">
                            <h2 class="title"><?= __('Delivery terms', 'static') ?></h2>
                        </div>
                        <div class="editor">
                            <p>
                                <?= __('All orders are dispatched the next working day and dispatched from our warehouse in France.', 'static') ?>
                            </p>
                            <p>
                                <?= __('We typically aim to deliver your products within 5-12 business days.', 'static') ?>
                                <?= __('This date is a guide only, but we will do our best to get it done quickly.', 'static') ?>
                                <?= __('However, if our delivery is delayed, we will contact you as soon as possible to let you know and we will take steps to minimize the impact of the delay.', 'static') ?>
                            </p>
                            <p>
                                <?= __('We accept returns within 14 days of purchase.', 'static') ?>
                                <?= __('To return an item, write to us at info@biodonatum.fr - please include your order number.', 'static') ?>
                                <?= __('Once we confirm receipt of your request, we will respond to you with a booking link.', 'static') ?>
                            </p>
                            <p>
                                <?= __('To initiate a refund, the item must be unused in its original packaging.', 'static') ?>
                            </p>
                        </div>
                    </div>
                </section>

                <? get_template_part('components/feedback'); ?>
            </main>

<? get_footer(); ?>
