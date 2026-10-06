<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Product Details || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Product Details";
require_once($landingParts . 'page_header.php');

?>

<!--Start Product Details-->
<section class="product-details">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-xl-6">
                <div class="product-details__img">
                    <img src="<?= base_url('assets/landing/') ?>images/shop/product-details-img-1.jpg" alt="" />
                </div>
            </div>

            <div class="col-lg-6 col-xl-6">
                <div class="product-details__top">
                    <h3 class="product-details__title">
                        Custom T-shirts <span>$46.00</span>
                    </h3>
                </div>
                <div class="product-details__reveiw">
                    <i class="icon-star"></i>
                    <i class="icon-star"></i>
                    <i class="icon-star"></i>
                    <i class="icon-star"></i>
                    <i class="icon-star"></i>
                    <span>2 customer reviews</span>
                </div>
                <div class="product-details__content">
                    <p class="product-details__content-text1">Aliquam hendrerit a augue insuscipit. Etiam
                        aliquam massa quis des mauris commodo venenatis ligula commodo leez sed blandit
                        convallis dignissim onec vel pellentesque neque. It has survived not only five
                        centuries, but also the leap into electronic typesetting, remaining essentially
                        unchanged.</p>
                    <p class="product-details__content-text2">REF. 4231/406 <br>
                        Available in store</p>
                </div>

                <div class="product-details__quantity">
                    <h3 class="product-details__quantity-title">Quantity</h3>
                    <div class="quantity-box">
                        <button type="button" class="sub"><i class="fa fa-minus"></i></button>
                        <input type="number" id="1" value="1" />
                        <button type="button" class="add"><i class="fa fa-plus"></i></button>
                    </div>
                </div>

                <div class="product-details__buttons">
                    <div class="product-details__buttons-1">
                        <a class="thm-btn" href="#">Add to Wishlist</a>
                    </div>

                    <div class="product-details__buttons-2">
                        <a class="thm-btn" href="cart.php">Add to Cart</a>
                    </div>
                </div>

                <div class="product-details__social">
                    <div class="title">
                        <h3>Share with friends:</h3>
                    </div>
                    <div class="product-details__social-link">
                        <a href="#"><span class="fab fa-twitter"></span></a>
                        <a href="#"><span class="fab fa-facebook"></span></a>
                        <a href="#"><span class="fab fa-pinterest-p"></span></a>
                        <a href="#"><span class="fab fa-instagram"></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--End Product Details-->

<!--Start Product Description-->
<section class="product-description">
    <div class="container">
        <h3 class="product-description__title">Description</h3>
        <p class="product-description__text1">Lorem ipsum dolor sit amet, cibo mundi ea duo, vim exerci
            phaedrum. There are many variations of passages of Lorem Ipsum available, but the majority have
            alteration in some injected or words which don't look even slightly believable. If you are going to
            use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrang hidden in the
            middle of text.</p>
        <div class="product-description__list">
            <ul class="list-unstyled">
                <li>
                    <p><span class="icon-angles-right"></span> Nam at elit nec neque suscipit gravida.</p>
                </li>
                <li>
                    <p><span class="icon-angles-right"></span> Aenean egestas orci eu maximus tincidunt.</p>
                </li>
                <li>
                    <p><span class="icon-angles-right"></span> Curabitur vel turpis id tellus cursus laoreet.
                    </p>
                </li>
            </ul>
        </div>
        <p class="product-description__tex2">All the Lorem Ipsum generators on the Internet tend to repeat
            predefined chunks as necessary, making this the first true generator on the Internet. It uses a
            dictionary of over 200 Latin words, combined with a handful of model sentence structures, to
            generate Lorem Ipsum which looks reasonable. </p>
    </div>
</section>
<!--End Product Description-->

<!--Start Review One-->
<section class="review-one">
    <div class="container">
        <div class="comments-area">
            <div class="review-one__title">
                <h3>2 reviews</h3>
            </div>
            <!--Start Comment Box-->
            <div class="comment-box">
                <div class="comment">
                    <div class="author-thumb">
                        <figure class="thumb"><img src="<?= base_url('assets/landing/') ?>images/shop/review-1-1.jpg" alt="">
                        </figure>
                    </div>

                    <div class="review-one__content">
                        <div class="review-one__content-top">
                            <div class="info">
                                <h2>Kevin martin <span>20 july 2025 . 4:00 pm</span></h2>
                            </div>
                            <div class="reply-btn">
                                <i class="icon-star"></i>
                                <i class="icon-star"></i>
                                <i class="icon-star"></i>
                                <i class="icon-star"></i>
                                <i class="icon-star"></i>
                            </div>
                        </div>

                        <div class="review-one__content-bottom">
                            <p>It has survived not only five centuries, but also the leap into electronic
                                typesetting unchanged. It was popularised in the sheets containing lorem ipsum
                                is simply free text. Class aptent taciti sociosqu ad litora torquent per conubia
                                nostra, per inceptos himenaeos. Vestibulum sollicitudin varius mauris non
                                dignissim.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--End Comment Box-->

            <!--Start Comment Box-->
            <div class="comment-box">
                <div class="comment">
                    <div class="author-thumb">
                        <figure class="thumb"><img src="<?= base_url('assets/landing/') ?>images/shop/review-1-2.jpg" alt="">
                        </figure>
                    </div>

                    <div class="review-one__content">
                        <div class="review-one__content-top">
                            <div class="info">
                                <h2>Sarah albert <span>20 july 2025 . 4:00 pm</span></h2>
                            </div>
                            <div class="reply-btn">
                                <i class="icon-star"></i>
                                <i class="icon-star"></i>
                                <i class="icon-star"></i>
                                <i class="icon-star"></i>
                                <i class="icon-star"></i>
                            </div>
                        </div>

                        <div class="review-one__content-bottom">
                            <p>It has survived not only five centuries, but also the leap into electronic
                                typesetting unchanged. It was popularised in the sheets containing lorem ipsum
                                is simply free text. Class aptent taciti sociosqu ad litora torquent per conubia
                                nostra, per inceptos himenaeos. Vestibulum sollicitudin varius mauris non
                                dignissim.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--End Comment Box-->
        </div>
    </div>
</section>
<!--End Review One-->

<!--Start Review Form-->
<section class="review-form-one">
    <div class="container">
        <div class="review-form-one__inner">
            <h3 class="review-form-one__title">Add a review</h3>
            <div class="review-form-one__rate-box">
                <p class="review-form-one__rate-text">Rate this product?</p>
                <div class="review-form-one__rate">
                    <i class="icon-star"></i>
                    <i class="icon-star"></i>
                    <i class="icon-star"></i>
                    <i class="icon-star"></i>
                    <i class="icon-star"></i>
                </div>
            </div>
            <form action="<?= base_url('assets/landing/') ?>inc/sendemail.php" class="review-form-one__form contact-form-validated"
                novalidate="novalidate">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="review-form-one__input-box text-message-box">
                            <textarea name="message" placeholder="Write comment"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6 col-lg-6">
                        <div class="review-form-one__input-box">
                            <input type="text" placeholder="Your name" name="name">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6">
                        <div class="review-form-one__input-box">
                            <input type="email" placeholder="Email address" name="email">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <button type="submit" class="thm-btn review-form-one__btn"> <span
                                class="icon-right"></span>
                            Submit comment</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!--End Review Form-->

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