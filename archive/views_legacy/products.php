<?php $landingParts = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'landing' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR; ?>
<?php $head_title = "Products || Begawipbj || Begawipbj PHP Template" ?>
<?php require_once($landingParts . 'layout/top-layout.php'); ?>
<!-- header -->
<?php require_once($landingParts . 'header/header.php'); ?>
<?php
$page_title = "Products";
require_once($landingParts . 'page_header.php');

?>

<!--Product Start-->
<section class="product">
    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-lg-12">
                <div class="product__sidebar">
                    <div class="shop-search product__sidebar-single">
                        <form action="#">
                            <input type="text" placeholder="Search">
                            <button type="submit"><i class="fa fa-search"></i></button>
                        </form>
                    </div>
                    <div class="product__price-ranger product__sidebar-single">
                        <h3 class="product__sidebar-title">Price</h3>
                        <div class="price-ranger">
                            <div id="slider-range"></div>
                            <div class="ranger-min-max-block">
                                <input type="text" readonly class="min">
                                <span>-</span>
                                <input type="text" readonly class="max">
                                <input type="submit" value="Filter">
                            </div>
                        </div>
                    </div>

                    <div class="shop-category product__sidebar-single">
                        <h3 class="product__sidebar-title">Categories</h3>
                        <ul class="list-unstyled">
                            <li><a href="#">Web Design</a></li>
                            <li class="active"><a href="#">Products Design</a></li>
                            <li><a href="#">Artificial Intelligence</a></li>
                            <li><a href="#">Technology</a></li>
                            <li><a href="#">Environtment Recyle</a></li>
                        </ul>
                    </div>

                    <div class="shop-color-option product__sidebar-single">
                        <h3 class="product__sidebar-title">Color Option</h3>
                        <ul class="list-unstyled">
                            <li>Black</li>
                            <li>Blue</li>
                            <li>Brown</li>
                            <li>Pink</li>
                            <li>Orange</li>
                        </ul>
                    </div>

                    <div class="shop-product-recent-products product__sidebar-single">
                        <h3 class="product__sidebar-title">Recent Products</h3>
                        <ul class="clearfix">
                            <li>
                                <div class="img">
                                    <img src="<?= base_url('assets/landing/') ?>images/shop/product-thumb-1.jpg" alt="Product">
                                    <a href="#"><i class="fa fa-link" aria-hidden="true"></i></a>
                                </div>
                                <div class="content">
                                    <div class="title">
                                        <h5><a href="#">Classy chair</a></h5>
                                    </div>
                                    <div class="price">
                                        <p>$33.00</p>
                                    </div>
                                    <div class="review">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star color"></i>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="img">
                                    <img src="<?= base_url('assets/landing/') ?>images/shop/product-thumb-2.jpg" alt="Product">
                                    <a href="#"><i class="fa fa-link" aria-hidden="true"></i></a>
                                </div>
                                <div class="content">
                                    <div class="title">
                                        <h5><a href="#">Wooden stool</a></h5>
                                    </div>
                                    <div class="price">
                                        <p>$39.00</p>
                                    </div>
                                    <div class="review">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star color"></i>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="img">
                                    <img src="<?= base_url('assets/landing/') ?>images/shop/product-thumb-3.jpg" alt="Product">
                                    <a href="#"><i class="fa fa-link" aria-hidden="true"></i></a>
                                </div>
                                <div class="content">
                                    <div class="title">
                                        <h5><a href="#">Sofa chair</a></h5>
                                    </div>
                                    <div class="price">
                                        <p>$54.00</p>
                                    </div>
                                    <div class="review">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star color"></i>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="img">
                                    <img src="<?= base_url('assets/landing/') ?>images/shop/product-thumb-4.jpg" alt="Product">
                                    <a href="#"><i class="fa fa-link" aria-hidden="true"></i></a>
                                </div>
                                <div class="content">
                                    <div class="title">
                                        <h5><a href="#">Big sofa</a></h5>
                                    </div>
                                    <div class="price">
                                        <p>$44.00</p>
                                    </div>
                                    <div class="review">
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star color"></i>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="shop-product-tags product__sidebar-single">
                        <h3 class="product__sidebar-title">Product Tags</h3>
                        <div class="shop-product__tags-list">
                            <a href="#">Art</a>
                            <a href="#">Decor</a>
                            <a href="#">Design</a>
                            <a href="#">Electronix</a>
                            <a href="#">Envato</a>
                            <a href="#">Destination</a>
                        </div>
                    </div>

                    <!--Start Products Style1 Single Sidear -->
                    <div class="shop-product-review product__sidebar-single style">
                        <h3 class="product__sidebar-title">Reviews</h3>
                        <div class="sidebar-rating-box sidebar-rating-box--style2">
                            <ul>
                                <li>
                                    <input type="radio" id="fivestar" name="rating" checked="checked">
                                    <label for="fivestar">
                                        <i></i>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                    </label>
                                </li>
                                <li>
                                    <input type="radio" id="fourstar" name="rating">
                                    <label for="fourstar">
                                        <i></i>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star gray"></span>
                                    </label>
                                </li>
                                <li>
                                    <input type="radio" id="threestar" name="rating">
                                    <label for="threestar">
                                        <i></i>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star gray"></span>
                                        <span class="icon-star gray"></span>
                                    </label>
                                </li>
                                <li>
                                    <input type="radio" id="twostar" name="rating">
                                    <label for="twostar">
                                        <i></i>
                                        <span class="icon-star"></span>
                                        <span class="icon-star"></span>
                                        <span class="icon-star gray"></span>
                                        <span class="icon-star gray"></span>
                                        <span class="icon-star gray"></span>
                                    </label>
                                </li>
                                <li>
                                    <input type="radio" id="onestar" name="rating">
                                    <label for="onestar">
                                        <i></i>
                                        <span class="icon-star"></span>
                                        <span class="icon-star gray"></span>
                                        <span class="icon-star gray"></span>
                                        <span class="icon-star gray"></span>
                                        <span class="icon-star gray"></span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--End Products Style1 Single Sidebar -->





                </div>
            </div>

            <div class="col-xl-9 col-lg-12">
                <div class="product__items">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="product__showing-result">
                                <div class="product__showing-text-box">
                                    <p class="product__showing-text">Showing 1â€“9 of 12 results</p>
                                </div>
                                <div class="product__showing-sort">
                                    <div class="select-box">
                                        <select class="wide">
                                            <option data-display="Sort by popular">Sort by popular</option>
                                            <option value="1">Sort by popular</option>
                                            <option value="2">Sort by Price</option>
                                            <option value="3">Sort by Ratings</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="product__all">
                        <div class="row">
                            <!--Product All Single Start-->
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="product__all-single">
                                    <div class="product__all-img">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-1.jpg" alt="">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-1.jpg" alt="">
                                    </div>
                                    <div class="product__all-content">
                                        <div class="product__all-review">
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                        </div>
                                        <h4 class="product__all-title"><a href="product-details.php">Classy
                                                chair</a></h4>
                                        <p class="product__all-price">$33.00</p>
                                        <div class="product__all-btn-box">
                                            <a class="thm-btn product__all-btn" href="cart.php">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Product All Single End-->

                            <!--Product All Single Start-->
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="product__all-single">
                                    <div class="product__all-img">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-2.jpg" alt="">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-2.jpg" alt="">
                                    </div>
                                    <div class="product__all-content">
                                        <div class="product__all-review">
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                        </div>
                                        <h4 class="product__all-title"><a href="product-details.php">Comfy
                                                chair</a></h4>
                                        <p class="product__all-price">$68.00</p>
                                        <div class="product__all-btn-box">
                                            <a class="thm-btn product__all-btn" href="cart.php">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Product All Single End-->
                            <!--Product All Single Start-->
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="product__all-single">
                                    <div class="product__all-img">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-3.jpg" alt="">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-3.jpg" alt="">
                                    </div>
                                    <div class="product__all-content">
                                        <div class="product__all-review">
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                        </div>
                                        <h4 class="product__all-title"><a href="product-details.php">Boss
                                                chair</a></h4>
                                        <p class="product__all-price">$23.00</p>
                                        <div class="product__all-btn-box">
                                            <a class="thm-btn product__all-btn" href="cart.php">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Product All Single End-->
                            <!--Product All Single Start-->
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="product__all-single">
                                    <div class="product__all-img">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-4.jpg" alt="">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-4.jpg" alt="">
                                    </div>
                                    <div class="product__all-content">
                                        <div class="product__all-review">
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                        </div>
                                        <h4 class="product__all-title"><a href="product-details.php">Sofa
                                                chair</a></h4>
                                        <p class="product__all-price">$54.00</p>
                                        <div class="product__all-btn-box">
                                            <a class="thm-btn product__all-btn" href="cart.php">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Product All Single End-->
                            <!--Product All Single Start-->
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="product__all-single">
                                    <div class="product__all-img">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-5.jpg" alt="">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-5.jpg" alt="">
                                    </div>
                                    <div class="product__all-content">
                                        <div class="product__all-review">
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                        </div>
                                        <h4 class="product__all-title"><a href="product-details.php">Wooden
                                                stool</a></h4>
                                        <p class="product__all-price">$39.00</p>
                                        <div class="product__all-btn-box">
                                            <a class="thm-btn product__all-btn" href="cart.php">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Product All Single End-->
                            <!--Product All Single Start-->
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="product__all-single">
                                    <div class="product__all-img">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-6.jpg" alt="">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-6.jpg" alt="">
                                    </div>
                                    <div class="product__all-content">
                                        <div class="product__all-review">
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                        </div>
                                        <h4 class="product__all-title"><a href="product-details.php">Big
                                                sofa</a></h4>
                                        <p class="product__all-price">$44.00</p>
                                        <div class="product__all-btn-box">
                                            <a class="thm-btn product__all-btn" href="cart.php">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Product All Single End-->
                            <!--Product All Single Start-->
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="product__all-single">
                                    <div class="product__all-img">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-7.jpg" alt="">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-7.jpg" alt="">
                                    </div>
                                    <div class="product__all-content">
                                        <div class="product__all-review">
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                        </div>
                                        <h4 class="product__all-title"><a href="product-details.php">Round
                                                chair</a></h4>
                                        <p class="product__all-price">$96.00</p>
                                        <div class="product__all-btn-box">
                                            <a class="thm-btn product__all-btn" href="cart.php">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Product All Single End-->
                            <!--Product All Single Start-->
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="product__all-single">
                                    <div class="product__all-img">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-8.jpg" alt="">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-8.jpg" alt="">
                                    </div>
                                    <div class="product__all-content">
                                        <div class="product__all-review">
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                        </div>
                                        <h4 class="product__all-title"><a href="product-details.php">Big
                                                stool</a></h4>
                                        <p class="product__all-price">$23.00</p>
                                        <div class="product__all-btn-box">
                                            <a class="thm-btn product__all-btn" href="cart.php">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Product All Single End-->
                            <!--Product All Single Start-->
                            <div class="col-xl-4 col-lg-4 col-md-6">
                                <div class="product__all-single">
                                    <div class="product__all-img">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-9.jpg" alt="">
                                        <img src="<?= base_url('assets/landing/') ?>images/shop/shop-product-1-9.jpg" alt="">
                                    </div>
                                    <div class="product__all-content">
                                        <div class="product__all-review">
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                            <i class="icon-star"></i>
                                        </div>
                                        <h4 class="product__all-title"><a href="product-details.php">Classic
                                                chair</a></h4>
                                        <p class="product__all-price">$33.00</p>
                                        <div class="product__all-btn-box">
                                            <a class="thm-btn product__all-btn" href="cart.php">Add to Cart</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Product All Single End-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--Product End-->

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