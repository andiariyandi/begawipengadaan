<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Cart || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Cart";
require_once($landingParts . 'page_header.php');

?>

<!--Start Cart Page-->
<section class="cart-page">
    <div class="container">
        <div class="table-responsive">
            <table class="table cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td>
                            <div class="product-box">
                                <div class="img-box">
                                    <img src="<?= base_url('assets/landing/') ?>images/shop/cart-page-img-1.jpg" alt="">
                                </div>
                                <h3><a href="product-details.php">Comfy chair</a></h3>
                            </div>
                        </td>
                        <td>$10.99</td>
                        <td>
                            <div class="quantity-box">
                                <button type="button" class="sub"><i class="fa fa-minus"></i></button>
                                <input type="number" id="product-1" value="1" />
                                <button type="button" class="add"><i class="fa fa-plus"></i></button>
                            </div>
                        </td>
                        <td>
                            $10.99
                        </td>
                        <td>
                            <div class="cross-icon">
                                <i class="fas fa-times"></i>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="product-box">
                                <div class="img-box">
                                    <img src="<?= base_url('assets/landing/') ?>images/shop/cart-page-img-2.jpg" alt="">
                                </div>
                                <h3><a href="product-details.php">Classic chair</a></h3>
                            </div>
                        </td>
                        <td>$10.99</td>
                        <td>
                            <div class="quantity-box">
                                <button type="button" class="sub"><i class="fa fa-minus"></i></button>
                                <input type="number" id="product-2" value="1" />
                                <button type="button" class="add"><i class="fa fa-plus"></i></button>
                            </div>
                        </td>
                        <td>
                            $10.99
                        </td>
                        <td>
                            <div class="cross-icon">
                                <i class="fas fa-times"></i>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <form action="#" class="default-form cart-cupon__form">
                    <input type="text" placeholder="Enter Coupon Code" class="cart-cupon__input">
                    <button class="thm-btn" type="submit">Apply Coupon</button>
                </form>
            </div>
            <div class="col-xl-4 col-lg-5">
                <ul class="cart-total list-unstyled">
                    <li>
                        <span>Subtotal</span>
                        <span>$20.98 USD </span>
                    </li>
                    <li>
                        <span>Shipping Cost</span>
                        <span>$0.00 USD</span>
                    </li>
                    <li>
                        <span>Total</span>
                        <span class="cart-total-amount">$20.98 USD</span>
                    </li>
                </ul>
                <div class="cart-page__buttons">
                    <div class="cart-page__buttons-1">
                        <a class="thm-btn" href="#">Update</a>
                    </div>
                    <div class="cart-page__buttons-2">
                        <a href="checkout.php" class="thm-btn">Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--End Cart Page-->

<!--Newsletter Two Start -->
<section class="newsletter-two">
    <div class="container">
        <div class="newsletter-two__inner">
            <div class="newsletter-two__img">
                <img src="<?= base_url('assets/landing/') ?>images/resources/newsletter-two-img-1.png" alt="">
            </div>
            <div class="newsletter-two__inner-content">
                <div class="newsletter-two__shape-bg"
                    style="background-image: url(<?= base_url('assets/landing/') ?>images/shapes/newsletter-two-shape-bg.png);"></div>
                <div class="newsletter-two__like">
                    <img src="<?= base_url('assets/landing/') ?>images/shapes/newsletter-two-like.png" alt="">
                </div>
                <div class="newsletter-two__title-box">
                    <h3 class="newsletter-two__title">Subscribe our <span>Newsletter</span></h3>
                    <p class="newsletter-two__text">Explore a diverse selection of courses all in one platform,
                        <br>
                        designed to cater to various learning
                    </p>
                </div>
                <div class="newsletter-two__form-box">
                    <form class="newsletter-two__form">
                        <div class="newsletter-two__input">
                            <input type="email" placeholder="Enter Your Email">
                        </div>
                        <button type="submit" class="newsletter-two__btn"> <span
                                class="icon-arrow-circle"></span>
                            Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Newsletter Two End -->

<!--Contact Info One Start -->
<section class="contact-info-one">
    <div class="container">
        <ul class="list-unstyled contact-info-one__list">
            <li>
                <div class="contact-info-one__single">
                    <div class="contact-info-one__icon">
                        <span class="icon-envelope"></span>
                    </div>
                    <div class="contact-info-one__content">
                        <p class="contact-info-one__sub-title">Email Address:</p>
                        <h5 class="contact-info-one__email"><a
                                href="mailto:info@example.com">info@example.com</a></h5>
                    </div>
                </div>
            </li>
            <li>
                <div class="contact-info-one__single">
                    <div class="contact-info-one__icon">
                        <span class="icon-phone"></span>
                    </div>
                    <div class="contact-info-one__content">
                        <p class="contact-info-one__sub-title">Phone Number</p>
                        <h5 class="contact-info-one__email"><a href="tel:001239957689">+00 123 (99) 57689</a>
                        </h5>
                    </div>
                </div>
            </li>
            <li>
                <div class="contact-info-one__single">
                    <div class="contact-info-one__icon">
                        <span class="icon-location"></span>
                    </div>
                    <div class="contact-info-one__content">
                        <p class="contact-info-one__sub-title">Our Address</p>
                        <h5 class="contact-info-one__email">1234 Elm Street Springfield,</h5>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</section>
<!--Contact Info One End -->

<?php require_once($landingParts . 'footer/footer_two.php'); ?>
<?php require_once($landingParts . 'layout/bottom-layout.php'); ?>