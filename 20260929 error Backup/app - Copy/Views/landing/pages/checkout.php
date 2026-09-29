<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Checkout || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Checkout";
require_once($landingParts . 'page_header.php');

?>

<!--Start Checkout Page-->
<section class="checkout-page">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="billing_details">
                    <div class="billing_title">
                        <p>Returning Customer? <span>Click here to Login</span></p>
                        <h2>Billing details</h2>
                    </div>
                    <form class="billing_details_form">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="billing_input_box">
                                    <div class="select-box">
                                        <select class="wide">
                                            <option data-display="Select a country">Select a country</option>
                                            <option value="1">Canada</option>
                                            <option value="2">England</option>
                                            <option value="3">Australia</option>
                                            <option value="3">USA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row bs-gutter-x-20">
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input type="text" name="first_name" value="" placeholder="First name"
                                        required="">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input type="text" name="last_name" value="" placeholder="Last name"
                                        required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="billing_input_box">
                                    <input type="text" name="company_name" value="" placeholder="Company">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="billing_input_box">
                                    <input type="text" name="Address" value="" placeholder="Address">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="billing_input_box">
                                    <input type="text" name="company_name" value=""
                                        placeholder="Appartment, unit, etc. (optional)">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="billing_input_box">
                                    <input type="text" name="Town/City" value="" placeholder="Town / City"
                                        required="">
                                </div>
                            </div>
                        </div>
                        <div class="row bs-gutter-x-20">
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input type="text" name="State" value="" placeholder="State" required="">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input name="form_zip" type="text" pattern="[0-9]*" placeholder="Zip code">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input name="email" type="email" placeholder="Email address">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input type="tel" name="form_phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}"
                                        required="" placeholder="Phone">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="checked-box">
                                    <input type="checkbox" name="skipper1" id="skipper" checked="">
                                    <label for="skipper"><span></span>Create an account?</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xl-6 col-lg-6">
                <div class="billing_details ship_different_address">
                    <div class="billing_title ship_different_address_title">
                        <h2>Ship to a different address <span class="fa fa-check-circle"></span></h2>
                    </div>
                    <form class="billing_details_form ship_different_address_form">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="billing_input_box">
                                    <div class="select-box">
                                        <select class="wide">
                                            <option data-display="Select a country">Select a country</option>
                                            <option value="1">Canada</option>
                                            <option value="2">England</option>
                                            <option value="3">Australia</option>
                                            <option value="3">USA</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row bs-gutter-x-20">
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input type="text" name="first_name" value="" placeholder="First name"
                                        required="">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input type="text" name="last_name" value="" placeholder="Last name"
                                        required="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="billing_input_box">
                                    <input type="text" name="company_name" value="" placeholder="Company">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="billing_input_box">
                                    <input type="text" name="Address" value="" placeholder="Address">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="billing_input_box">
                                    <input type="text" name="company_name" value=""
                                        placeholder="Appartment, unit, etc. (optional)">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="billing_input_box">
                                    <input type="text" name="Town/City" value="" placeholder="Town / City"
                                        required="">
                                </div>
                            </div>
                        </div>
                        <div class="row bs-gutter-x-20">
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input type="text" name="State" value="" placeholder="State" required="">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input name="form_zip" type="text" pattern="[0-9]*" placeholder="Zip code">
                                </div>
                            </div>
                        </div>

                        <div class="row bs-gutter-x-20">
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input name="email" type="email" placeholder="Email address">
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="billing_input_box">
                                    <input type="tel" name="form_phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}"
                                        required="" placeholder="Phone">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12">
                                <div class="ship_different_input">
                                    <textarea placeholder="Notes about order"
                                        name="form_order_notes"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="your_order">
            <h2>Your order</h2>
            <div class="row">
                <div class="col-xl-6 col-lg-6">
                    <div class="order_table_box">
                        <table class="order_table_detail">
                            <thead class="order_table_head">
                                <tr>
                                    <th>Product</th>
                                    <th class="right">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="pro__title">Product Name</td>
                                    <td class="pro__price">$10.99 USD</td>
                                </tr>
                                <tr>
                                    <td class="pro__title">Subtotal</td>
                                    <td class="pro__price">$10.99 USD</td>
                                </tr>
                                <tr>
                                    <td class="pro__title">Shipping</td>
                                    <td class="pro__price">$0.00 USD</td>
                                </tr>
                                <tr>
                                    <td class="pro__title">Total</td>
                                    <td class="pro__price">$20.98 USD</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="checkout__payment">
                        <div class="checkout__payment__item checkout__payment__item--active">
                            <h3 class="checkout__payment__title">Direct bank transfer</h3>
                            <div class="checkout__payment__content">
                                Make your payment directly into our bank account. Please
                                use your Order ID as the payment reference. Your order
                                wont be shipped until the funds have cleared.
                            </div><!-- /.checkout__payment__content -->
                        </div><!-- /.checkout__payment__item -->
                        <div class="checkout__payment__item">
                            <h3 class="checkout__payment__title">Paypal payment <img
                                    src="<?= base_url('assets/landing/') ?>images/shop/paypal-1.jpg" alt=""></h3>
                            <div class="checkout__payment__content">
                                Make your payment directly into our bank account. Please
                                use your Order ID as the payment reference. Your order
                                wont be shipped until the funds have cleared.
                            </div><!-- /.checkout__payment__content -->
                        </div><!-- /.checkout__payment__item -->
                    </div><!-- /.checkout__payment -->
                    <div class="text-right d-flex justify-content-end">
                        <a class="thm-btn" href="checkout.php"> <span class="icon-angles-right"></span> Place
                            your
                            order</a>
                    </div><!-- /.text-right -->

                </div><!-- /.col-lg-6 -->
            </div>
        </div>
    </div>
</section>
<!--End Checkout Page-->

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