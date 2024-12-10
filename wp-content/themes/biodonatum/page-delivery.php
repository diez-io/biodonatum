<?php
/* Template Name: Delivery Page */
?>
<? get_template_part('components/header'); ?>

            <main class="main">
                <section class="section pt-50">
                    <div class="container">
                        <div class="breadcrumbs mb-40">
                            <a href="<?= home_url(); ?>" class="breadcrumbs__link">to index</a>
                            <span class="breadcrumbs__link">Delivery terms</span>
                        </div>
                        <div class="head">
                            <h2 class="title">Delivery terms</h2>
                        </div>
                        <div class="editor">
                            <p>
                                All orders are dispatched the next working day and dispatched from our warehouse in
                                France.
                            </p>
                            <p>We typically aim to deliver your products within 5-12 business days. This date is a guide
                                only, but we will do our best to get it done quickly. However, if our delivery is
                                delayed, we will contact you as soon as possible to let you know and we will take steps
                                to minimize the impact of the delay .</p>
                            <p>We accept returns within 14 days of purchase. To return an item, write to us at
                                info@biodonatum.fr - please include your order number. Once we confirm receipt of your
                                request, we will respond to you with a booking link.</p>
                            <p> To initiate a refund, the item must be unused in its original packaging.</p>
                        </div>
                    </div>
                </section>

                <? get_template_part('components/feedback'); ?>
            </main>

<? get_template_part('components/footer'); ?>
