<?php
/* Template Name: Cart Page */
?>
<? get_template_part('components/header'); ?>

<main class="main">
            <section class="section pt-50">
                <div class="container">
                    <div class="breadcrumbs mb-40">
                        <a href="<?= home_url(); ?>" class="breadcrumbs__link">to index</a>
                        <span class="breadcrumbs__link">Your basket</span>
                    </div>
                    <div class="cart">
                        <div class="head">
                            <h2 class="title">Your basket</h2>
                        </div>
                        <div class="island">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>SubTotal</th>
                                        <th>Loyalty program</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div>
                                                <picture>
                                                    <img src="<?= get_template_directory_uri(); ?>/assets/images/cart/item.jpg" alt="">
                                                </picture>
                                                <div>
                                                    Microbiotic Biodonatium
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <button class="button">-</button>
                                                <div>
                                                    2
                                                </div>
                                                <button class="button">+</button>
                                            </div>
                                        </td>
                                        <td>$ 100</td>
                                        <td>$ 200</td>
                                        <td>$ 0</td>
                                        <td>
                                            <button class="button">del</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <? get_template_part('components/feedback'); ?>
        </main>

<? get_template_part('components/footer'); ?>
