<script src="<?php echo SITE_URL; ?>assets/js/viewport/withinviewport.js"></script>
<script src="<?php echo SITE_URL; ?>assets/js/viewport/jquery.withinviewport.js"></script>

<script>
    /* Check if user is activly visiting a content with this function */
    jQuery.fn.isInViewport = function() {

        if(!$) {
            $ = jQuery;
        }

        if(!$(this).offset()) {
            return false;
        }

        var elementTop = $(this).offset().top;
        var elementBottom = elementTop + $(this).outerHeight();

        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();

        return elementBottom > viewportTop && elementTop < viewportBottom;
    };

</script>


<script>

    window.sent_impressions = [];
    window.last_sent_impressions = [];
    window.start_updating_payment_event_sent = false;
    window.sent_addon = null;
    window.brand = 'NSA';

    function addToSentImpressions(arr, course) {
        const { length } = arr;
        const id = length + 1;
        const found = arr.some(el => el.name === course.name);
        if (!found) arr.push(course);
        return arr;
    }

    function triggerGAEvents() {

        jQuery(document).ready(function ($) {

            if ($('.popularCourseboxes .nsa-category-courses').isInViewport()) {

                $('body').trigger('course_list_visible');

                //console.log('course_list_visible triggered');

            }

            if ($('.popularCoursesSlider.popularCourseboxes').isInViewport()) {

                $('body').trigger('popular_courses_visible');

                //console.log('popular_courses_visible triggered');

            }


            if ($('.checkout-gift-container').isInViewport()) {

                $('body').trigger('checkout_addon_visible');

                //console.log('checkout_addon_visible triggered');

            }

            if ($('.nsa-offer-courses').isInViewport()) {

                $('body').trigger('offer_courses_visible');

                //console.log('checkout_addon_visible triggered');

            }


        });


    }


    jQuery(document).ready(function ($) {

        triggerGAEvents();

        //Trigger event only if the courses list is visible to the user.
        //$(window).on('resize scroll', function(e) {
        $(window).on('scroll', function(e) {

            triggerGAEvents();

        });

    });

</script>



<script>

    jQuery(document).ready(function ($) {

        let brand = 'NSA';

        let products = [];

        $('#ajaxItems').on('click', '.cart-items .remove-items', function(e){

            let $this = $(this);
            let item = $this.closest('.cart-items');

            //console.log(item);

            let course_title = $.trim( item.find('.nsa_course_title').text() );

            //console.log(course_title);

            let cat_names = item.attr('data-course-cats');
            let course_type = item.attr('data-course_type');
            //let course_id = item.attr('data-course_id');
            //let course_id = item.attr('data-product_id');
            let course_id = item.attr('data-oldproductid');

            if(!course_id || SITE_TYPE == 'us') {
                course_id = item.attr('data-course-id');
            }

            //let price_html = item.find('.woocommerce-Price-amount.amount').clone();
            //price_html.find('span').remove();
            //let course_price = parseFloat(price_html.text()).toFixed(2);

            let course_price = item.find('.course_price').text().replace("£", "").replace("$", "");
            course_price = parseFloat(course_price).toFixed(2);


            let course_object = {
                'name': course_title,
                'id': course_id,
                'price': course_price,
                'brand': brand,
                'category': cat_names,
                'variant': course_type,
                'quantity': 1
            };

            products.push(course_object);

            dataLayer.push({
                'event': 'removeFromCart',
                'ecommerce': {
                    'remove': {
                        'products': products
                    }
                }
            });


            //console.log('removeFromCart event triggered on Cart Page');


        });



    });

</script>


<?php if( REQUEST == "checkout" ) : ?>

    <script>

        let brand = 'NSA';

        function triggerCheckoutEvent(cart_data) {

            if(!$) {
                $ = jQuery;
            }


            let order_details = $('.order-details');
            let order_id = order_details.find('.nsa_order_number').text();

            //console.log('inside triggerCheckoutEvent', $('.order-summery .cart-items').length);
            //console.log('cart_data', cart_data);

            let course_items = cart_data.items;
            //console.log(course_items);

            //console.log('cart_items_num', course_items.length);

            if(course_items.length <= 0) {
                return false;
            }

            let courses = [];
            let checkout_first_course_cats = course_items[0].categories;
            let checkout_total_amount = cart_data.totalPriceAmount;
            let checkout_first_course_title = course_items[0].title;
            let course_ids = [];

            let contents = [];

            let tiktok_contents = [];

            course_items.forEach(function(course, index){


                //console.log($(obj));

                //console.log(course);

                let course_title = course.title;
                let cat_names = course.categories;
                let course_type = course.courseType;
                //let course_id = course.courseID;
                //let course_id = course.attr('data-product_id');
                let course_id = course.productID;

                if(!course_id || SITE_TYPE == 'us') {
                    course_id = course.courseID;
                }

                let course_price = course.price.replace("£", "").replace("$", "");
                course_price = parseFloat(course_price).toFixed(2);

                //let discount_html = $(obj).find('.cart-discount .woocommercePriceamount.amount').clone();
                //discount_html.find('span').remove();
                //let course_discount = parseFloat(price_html.text()).toFixed(2);

                //if(isNaN(course_discount)) {
                //    course_discount = 0;
                //}

                //let position = index + 1;

                if(!course_id) {
                    let premium_subscription_id = course.premiumSubPlanID;
                    if(premium_subscription_id) {
                        course_id = premium_subscription_id;
                        subscription_total = course_price;
                    }
                }

                let content = {"id":course_id,"quantity":1};

                let course_object = {
                    'name': course_title,
                    'id': course_id,
                    'price': course_price,
                    //'discount': course_discount,
                    'brand': brand,
                    'category': cat_names,
                    'variant': course_type,
                    'quantity': 1
                };

                courses.push(course_object);
                course_ids.push(course_id);
                contents.push(content);


                let tiktok_content = {
                    'content_id': course_id,
                    'content_type': 'product',
                    'content_name': course_title,
                    'quantity': 1,
                    'price': course_price,
                };

                tiktok_contents.push(tiktok_content);

            });

            let checkoutStep = getCookie('checkoutStep', true);

            let checkout_courses_from_cookie = getCookie('checkoutCourses', true);
            let string_checkout_courses = JSON.stringify(courses);

            //console.log(checkout_courses_from_cookie);
            //console.log(string_checkout_courses);
            //console.log(checkoutStep);

            if(checkout_courses_from_cookie != string_checkout_courses || checkoutStep != 1) {

                dataLayer.push({
                    'event': 'checkout',
                    'ecommerce': {
                        'checkout': {
                            'actionField': {'step': 1},
                            'products': courses
                        }
                    }
                });



                fbq(
                    'track',
                    'InitiateCheckout',
                    {
                        content_type: "product",
                        domain: DOMAIN_NAME,
                        subtotal: checkout_total_amount,
                        num_items: courses.length,
                        event_hour: event_hour,
                        //user_roles: "student",
                        category_name: checkout_first_course_cats,
                        currency: CURRENCY,
                        value: checkout_total_amount,
                        content_name: checkout_first_course_title,
                        content_ids: course_ids,
                        event_day: event_day,
                        contents: contents,
                        event_month: event_month

                    }
                );


                //ttq.track('InitiateCheckout');

                ttq.track('InitiateCheckout',{
                    //content_id: course_id,
                    //content_name: course_title,
                    contents: tiktok_contents,
                    quantity: tiktok_contents.length,
                    price: checkout_total_amount,
                    value: checkout_total_amount,
                    currency: CURRENCY,
                });

                //snaptr('track','START_CHECKOUT');

                snaptr('track', 'START_CHECKOUT', {
                    'currency': "GBP",
                    'price': checkout_total_amount,
                    'item_category': checkout_first_course_cats,
                    'item_ids': course_ids,
                    'number_items': courses.length,
                    'transaction_id': order_id
                });


                createCookie('checkoutStep', 1, 1);
                createCookie('checkoutCourses', string_checkout_courses, 1);

            }


            //console.log('checkout event triggered on Checkout page');

        }

        function triggeraddToCartAddonEvent() {

            let addon = $('.checkout-gift-container');

            //let addon_id = $('.checkout-gift-container .the-offer').attr('data-id');
            //let addon_id = addon.attr('data-product_id');
            let addon_id = addon.attr('data-oldproductid');

            if(!addon_id) {
                addon_id = addon.attr('data-course-id');
            }

            let addon_title = addon.find('.nsa_course_title').text();
            let addon_cats = addon.attr('data-course-cats');
            let addon_course_type = addon.attr('data-course_type');

            //let price_html = $('.checkout-gift-container').find('.title-offer .woocommerce-Price-amount.amount').clone();
            //price_html.find('span').remove();
            //let addon_price = parseFloat(price_html.text()).toFixed(2);


            let addon_price = addon.find('.course_price').text().replace("£", "").replace("$", "");
            addon_price = parseFloat(addon_price).toFixed(2);

            let checkout_gift_checked = $('#addon').is(':checked');

            if(checkout_gift_checked) {

                dataLayer.push({
                    'event': 'addToCartAddon',
                    'ecommerce': {
                        'currencyCode': 'USD',
                        'add': {
                            'products': [{
                                'name': addon_title,
                                'id': addon_id,
                                'price': addon_price,
                                'brand': 'Add On',
                                'category': addon_cats,
                                'variant': addon_course_type,
                                'quantity': 1
                            }]

                        }
                    }

                });


                gtag('event', 'add on', {
                    'event_label': addon_title,
                    'event_category': 'offer',
                    'non_interaction': false
                });

            }

        }


        jQuery(document).ready(function ($) {


            //triggerCheckoutEvent();

            //not required (by Prateek).
            //$( document.body ).on( 'updated_checkout', triggerCheckoutEvent );


            <?php

            $redirected_back_from_login = false;

            //$referrer_url = wp_get_referer();
            $referrer_url = $_SERVER['HTTP_REFERER'];

            $query_string = parse_url($referrer_url, PHP_URL_QUERY);

            //var_dump($query_string);

            $queries = array();
            parse_str($query_string, $queries);

            //var_dump($queries);

            if(isset($queries['redirect_url'])) {

                if(strpos($queries['redirect_url'], 'checkout') !== false) {

                    $redirected_back_from_login = true;

                }

            }

            ?>


            <?php if($redirected_back_from_login): ?>

            dataLayer.push({
                'event': 'checkout',
                'ecommerce': {
                    'checkout': {
                        'actionField': {'step': 2, 'option': 'login'}
                    }
                }
            });

            <?php endif; ?>


            $('.payment-method').on('click', '.nav-tabs', function (e) {

                //console.log('on payment button click 1');

                let payment_gateway = $(this).find('.nav-item').attr('data-name');

                if(!window.start_updating_payment_event_sent) {

                    dataLayer.push({
                        'event': 'checkout',
                        'ecommerce': {
                            'checkout': {
                                'actionField': {'step': 3, 'option': payment_gateway}
                            }
                        }
                    });

                    window.start_updating_payment_event_sent = true;

                }

            });


            $('#place_order').on('click', function (e) {

                let payment_gateway = $('#payment .tab-link.current').find('label span').text();

                dataLayer.push({
                    'event': 'checkout',
                    'ecommerce': {
                        'checkout': {
                            'actionField': {'step': 4, 'option': payment_gateway}
                        }
                    }
                });

                delete_cookie('checkoutStep', '/');


            });

            $('body').on('checkout_addon_visible', function (e) {

                //console.log(window.sent_addon);

                if (!window.sent_addon) {

                    //let impressions_from_cookie = getCookie('productImpression', true);
                    //let string_impressions = JSON.stringify(impressions);

                    //console.log(impressions_from_cookie);
                    //console.log(string_impressions);

                    //if(impressions_from_cookie != string_impressions) {


                    let addon = $('.checkout-gift-container');

                    //let addon_id = $('.checkout-gift-container .the-offer').attr('data-id');
                    //let addon_id = addon.attr('data-product_id');
                    let addon_id = addon.attr('data-oldproductid');

                    if(!addon_id) {
                        addon_id = addon.attr('data-course-id');
                    }

                    let addon_title = addon.find('.nsa_course_title').text();
                    let addon_cats = addon.attr('data-course-cats');
                    let addon_course_type = addon.attr('data-course_type');

                    //let price_html = $('.checkout-gift-container').find('.title-offer .woocommerce-Price-amount.amount').clone();
                    //price_html.find('span').remove();
                    //let addon_price = parseFloat(price_html.text()).toFixed(2);


                    let addon_price = addon.find('.course_price').text().replace("£", "").replace("$", "");
                    addon_price = parseFloat(addon_price).toFixed(2);

                    dataLayer.push({
                        'event': 'productDetailViewAddon',
                        'ecommerce': {
                            'detail': {
                                'products': [{
                                    'name': addon_title,
                                    'id': addon_id,
                                    'price': addon_price,
                                    'brand': 'Add On',
                                    'category': addon_cats,
                                    'variant': addon_course_type
                                }]
                            }
                        }
                    });


                    window.sent_addon = addon_id;
                    //    createCookie('productImpression', string_impressions, 0.001);

                    //}

                    //console.log('productDetailViewAddon event triggered on checkout');

                } else {

                    //console.log('productImpression event already triggered');

                }


            });



            /*
            //$('.addon_label_container').on('click', function (e) {
            $('.checkout-box').on('click', '.addon_label_container', function (e) {

                console.log('add addon');

                let checkout_gift_checked = $('#addon').is(':checked');

                console.log('checkout_gift_checked', checkout_gift_checked);

                if(checkout_gift_checked == true) {

                    //let checkout_gift_container = $(this).closest('.checkout-gift-container');

                    //let addon_id = checkout_gift_container.find('.the-offer').attr('data-id');

                    let addon = $('.checkout-gift-container');

                    //let addon_id = $('.checkout-gift-container .the-offer').attr('data-id');
                    //let addon_id = addon.attr('data-product_id');
                    let addon_id = addon.attr('data-oldproductid');

                    if(!addon_id) {
                        addon_id = addon.attr('data-course-id');
                    }

                    let addon_title = addon.find('.nsa_course_title').text();
                    let addon_cats = addon.attr('data-course-cats');
                    let addon_course_type = addon.attr('data-course_type');

                    //let price_html = $('.checkout-gift-container').find('.title-offer .woocommerce-Price-amount.amount').clone();
                    //price_html.find('span').remove();
                    //let addon_price = parseFloat(price_html.text()).toFixed(2);


                    let addon_price = addon.find('.course_price').text().replace("£", "").replace("$", "");
                    addon_price = parseFloat(addon_price).toFixed(2);

                    let checkout_gift_checked = $('#addon').is(':checked');

                    if(checkout_gift_checked) {

                        dataLayer.push({
                            'event': 'addToCartAddon',
                            'ecommerce': {
                                'currencyCode': 'USD',
                                'add': {
                                    'products': [{
                                        'name': addon_title,
                                        'id': addon_id,
                                        'price': addon_price,
                                        'brand': 'Add On',
                                        'category': addon_cats,
                                        'variant': addon_course_type,
                                        'quantity': 1
                                    }]

                                }
                            }

                        });


                        gtag('event', 'add on', {
                            'event_label': addon_title,
                            'event_category': 'offer',
                            'non_interaction': false
                        });

                    }


                }


            });
            */

            $('.woocommerce').on('DOMNodeInserted', function(e) {

                if ($(e.target).is('.woocommerce-error')) {

                    //console.log('checkout error occurred');

                    // How Many Errors
                    let error_count = $('.woocommerce-error li').size();
                    // This extracts the contents of the <li></li> tags, separated by commas
                    let messages = $(".woocommerce-error li").map(function() { return $(this).text() }).get().join();
                    if(messages == ''){
                        messages = $(".woocommerce-error").text();
                    }

                    gtag('event', 'payment error', {
                        'event_label': messages,
                        'event_category': 'error',
                        'non_interaction': false
                    });

                }

            });




        });





    </script>

<?php endif; ?>


<?php if( strpos(REQUEST, 'checkout/confirmation') !== false) : ?>

    <script>

        let brand = 'NSA';


        jQuery(document).ready(function ($) {

            let courses = [];

            let order_number = $('.order-details .nsa_order_number').first().text();
            //let tax = $('.order-details .total_tax').text();
            let tax = 0;
            //let shipping = $('.order-details .shipping_cost').text();
            let shipping = 0;
            let coupons = $('.order-details .coupon_codes').first().text();

            //let price_html = $('.order-details .woocommerce-Price-amount.amount').clone();
            //price_html.find('span').remove();
            //let total_amount = parseFloat(price_html.text()).toFixed(2);

            //let total_amount = $('.order-details').find('.course_price').text().replace("£", "").replace("$", "");
            //total_amount = parseFloat(total_amount).toFixed(2);

            let total_amount = 0;

            $('.checkout-thnkyou-box .thankyou-order-item').each(function(index, obj){

                let is_print_material = $(obj).find('.is_print_material').text();

                //Do not include printed materials
                if(is_print_material == '1') {

                    return; //equivalnet to continue in JS

                }

                let course_title = $(obj).find('.nsa_course_title').text();
                //let cat_names = $(obj).attr('data-cat_names');
                let cat_names = $(obj).find('.course_cats').text();
                //let course_type = $(obj).attr('data-course_type');
                let course_type = $(obj).find('.course_type').text();
                //let course_id = $(obj).attr('data-course_id');
                //let course_id = $(obj).attr('data-product_id');

                let course_id = $(obj).find('.old_product_id').text();

                if(!course_id || SITE_TYPE == 'us') {
                    course_id = $(obj).find('.course_id').text();
                }

                //let price_html = $(obj).find('.course-price .woocommerce-Price-amount.amount').clone();
                //price_html.find('span').remove();
                //let course_price = parseFloat(price_html.text()).toFixed(2);

                //let purchased_price = $(obj).find('.purchased_price').text();

                //let position = index + 1;

                let course_price = $(obj).find('.course_price').text().replace("£", "").replace("$", "");
                course_price = parseFloat(course_price).toFixed(2);


                let course_object = {
                    'name': course_title,
                    'id': course_id,
                    'price': course_price,
                    'brand': brand,
                    'category': cat_names,
                    'variant': course_type,
                    'quantity': 1
                };

                courses.push(course_object);

                if(typeof(course_price) == 'string') {
                    course_price = parseFloat(course_price);
                }

                //console.log('total_amount', total_amount);
                //console.log('course_price', course_price);

                total_amount += course_price;

                //console.log('total_amount after', total_amount);


            });



            let thankyou_courses_from_cookie = getCookie('thankyouCourses', true);
            let string_thankyou_courses = JSON.stringify(courses);

            //Temporary solution
            total_amount = $('.order_total_amount').text().replace("£", "").replace("$", "");
            total_amount = parseFloat(total_amount).toFixed(2);

            if(thankyou_courses_from_cookie != string_thankyou_courses) {

                if(order_number) {

                    dataLayer.push({
                        'event': 'purchase',
                        'ecommerce': {
                            'purchase': {
                                'actionField': {
                                    'id': order_number,
                                    'affiliation': 'Online Store',
                                    'revenue': total_amount,
                                    //'tax': tax,
                                    //'shipping': shipping,
                                    'coupon': coupons
                                },
                                'products': courses
                            }
                        }
                    });

                    createCookie('thankyouCourses', string_thankyou_courses, 1);

                    //console.log('purchase event triggered on order received page');

                }

            }




        });


    </script>


    <?php

    //var_dump($nsa->current_user_id);

    $trigger_user_registered_event = false;


    if (!empty(CUR_ID_FRONT)) {

        $registration_date = $this->user->whenCreated;

        $registration_datetime = strtotime(date('Y-m-d H:i:s', strtotime($registration_date)));
        $current_datetime = strtotime(date('Y-m-d H:i:s'));

        //var_dump($current_datetime - $registration_datetime);

        if ( $current_datetime - $registration_datetime <= 300 ) { //if user was registered maximum 300 seconds ago.

            $trigger_user_registered_event = true;

        }

    }

    ?>

    <?php if($trigger_user_registered_event): ?>

        <script>

            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                'event' : 'signup',
                'user_id' : '<?php echo CUR_ID_FRONT; ?>',
                'user_email' : '<?php echo CUR_EMAIL_FRONT; ?>'
            });

            gtag('event', 'sign up', {
                'event_label': '<?php echo CUR_ID_FRONT; ?>',
                'event_category': 'registration',
                'non_interaction': true
            });


            window.dataLayer.push({
                'event' : 'login',
                'user_id' : '<?php echo CUR_ID_FRONT; ?>'
            })


        </script>


    <?php endif; ?>




<?php endif; ?>



