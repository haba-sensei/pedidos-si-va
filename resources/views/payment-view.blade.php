<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>
        @yield('title')
    </title>
    <!-- SEO Meta Tags-->
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- Viewport-->
    {{--<meta name="_token" content="{{csrf_token()}}">--}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon and Touch Icons-->
    <link rel="shortcut icon" href="favicon.ico">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">
    <script
        src="{{asset('public/assets/admin')}}/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js"></script>
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/toastr.css">

    <style>
        .stripe-button-el{
            display: none!important;
        }
        .razorpay-payment-button{
            display: none!important;
        }
    </style>
</head>
<!-- Body-->
<body class="toolbar-enabled">
<!-- Page Content-->
<div class="container pb-5 mb-2 mb-md-4">
    <div class="row">
        <div class="col-md-12 mb-5 pt-5">
            <center class="">
                <h1>Payment method</h1>
            </center>
        </div>
        <section class="col-lg-12">
            <div class="checkout_details mt-3">
                <h2 class="h6 pb-3 mb-2 mt-5">Choose payment</h2>
                <div class="row">
                    <div class="col-md-6 mb-4" style="cursor: pointer">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('ssl_commerz_payment'))
                        @if($config['status'])
                            <div class="card" onclick="setPaymentMethod('ssl_commerz_payment')">
                                <div class="card-body">
                                    <input type="radio" name="payment_gateway"
                                           id="ssl_commerz_payment" {{session('payment_method')=='ssl_commerz_payment'?'checked':''}}>
                                    <span class="checkmark" style="margin-right: 10px"></span>
                                    <span>SSLCOMMERZ Payment</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-4" style="cursor: pointer">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('razor_pay'))
                        @if($config['status'])
                            <div class="card" onclick="setPaymentMethod('razor_pay')">
                                <div class="card-body">
                                    <input type="radio" name="payment_gateway"
                                           id="razor_pay" {{session('payment_method')=='razor_pay'?'checked':''}}>
                                    <span class="checkmark" style="margin-right: 10px"></span>
                                    <span>Razor Pay</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-4" style="cursor: pointer">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('paypal'))
                        @if($config['status'])
                            <div class="card" onclick="setPaymentMethod('paypal')">
                                <div class="card-body">
                                    <input type="radio" name="payment_gateway"
                                           id="paypal" {{session('payment_method')=='paypal'?'checked':''}}>
                                    <span class="checkmark" style="margin-right: 10px"></span>
                                    <span>Paypal</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mb-4" style="cursor: pointer">
                        @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                        @if($config['status'])
                            <div class="card" onclick="setPaymentMethod('stripe')">
                                <div class="card-body">
                                    <input type="radio" name="payment_gateway"
                                           id="stripe" {{session('payment_method')=='stripe'?'checked':''}}>
                                    <span class="checkmark" style="margin-right: 10px"></span>
                                    <span>Stripe</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="col-md-12 mb-5 pt-5">
            @if(session('payment_method')=='ssl_commerz_payment')
                <form action="{{ url('/pay-ssl') }}" method="POST" class="needs-validation">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token"/>
                    <button class="btn btn-primary btn-block" type="submit">
                        <i class="czi-card"></i> Pay Now
                    </button>
                </form>
            @elseif(session('payment_method')=='paypal')
                <form class="needs-validation" method="POST" id="payment-form"
                      action="{{route('pay-paypal')}}">
                    {{ csrf_field() }}
                    <button class="btn btn-primary btn-block" type="submit">
                        <i class="czi-card"></i> Pay Now
                    </button>
                </form>
            @elseif(session('payment_method')=='stripe')
                @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                <form class="needs-validation" method="POST" id="payment-form"
                      action="{{route('pay-stripe')}}">
                    {{ csrf_field() }}
                    <button class="btn btn-primary btn-block" type="button"
                            onclick="$('.stripe-button-el').click()">
                        <i class="czi-card"></i> Pay Now
                    </button>
                    @php($order=\App\Model\Order::find(session('order_id')))
                    <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                            data-key="{{$config['published_key']}}"
                            data-amount="{{$order->order_amount*100}}"
                            data-name="{{auth()->check()?auth()->user()->f_name:''}}"
                            data-description=""
                            data-image="https://stripe.com/img/documentation/checkout/marketplace.png"
                            data-locale="auto"
                            data-currency="{{\App\CentralLogics\Helpers::currency_code()}}">
                    </script>
                </form>
            @elseif(session('payment_method')=='razor_pay')
                @php($config=\App\CentralLogics\Helpers::get_business_settings('razor_pay'))
                @php($order=\App\Model\Order::find(session('order_id')))
                <form action="{!!route('payment-razor')!!}" method="POST">
                    @csrf
                    <!-- Note that the amount is in paise = 50 INR -->
                    <!--amount need to be in paisa-->
                    <script src="https://checkout.razorpay.com/v1/checkout.js"
                            data-key="{{ Config::get('razor.razor_key') }}"
                            data-amount="{{$order->order_amount*100}}"
                            data-buttontext="Pay {{$order->order_amount}} INR"
                            data-name="{{\App\Model\BusinessSetting::where(['key'=>'restaurant_name'])->first()->value}}"
                            data-description="{{$order['id']}}"
                            data-image="{{asset('storage/app/public/restaurant/'.\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)}}"
                            data-prefill.name="{{$order->customer->f_name}}"
                            data-prefill.email="{{$order->customer->email}}"
                            data-theme.color="#ff7529">
                    </script>
                </form>

                <button class="btn btn-primary btn-block" type="button"
                        onclick="$('.razorpay-payment-button').click()">
                    <i class="czi-card"></i> Pay Now
                </button>
            @endif
        </div>

    </div>
</div>

{{--loader--}}
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" style="display: none;">
                <div style="position: fixed;z-index: 9999; left: 40%;top: 37% ;width: 100%">
                    <img width="200" src="{{asset('public/assets/front-end/img/loader.gif')}}">
                </div>
            </div>
        </div>
    </div>
</div>
{{--loader--}}

<!-- ========== END SECONDARY CONTENTS ========== -->
<script src="{{asset('public/assets/admin')}}/js/demo.js"></script>
<!-- JS Implementing Plugins -->

<!-- JS Front -->
<script src="{{asset('public/assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('public/assets/admin')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

<!-- JS Plugins Init. -->
<script>
    $(document).on('ready', function () {
        // ONLY DEV
        // =======================================================
        if (window.localStorage.getItem('hs-builder-popover') === null) {
            $('#builderPopover').popover('show')
                .on('shown.bs.popover', function () {
                    $('.popover').last().addClass('popover-dark')
                });

            $(document).on('click', '#closeBuilderPopover', function () {
                window.localStorage.setItem('hs-builder-popover', true);
                $('#builderPopover').popover('dispose');
            });
        } else {
            $('#builderPopover').on('show.bs.popover', function () {
                return false
            });
        }
        // END ONLY DEV
        // =======================================================

        // BUILDER TOGGLE INVOKER
        // =======================================================
        $('.js-navbar-vertical-aside-toggle-invoker').click(function () {
            $('.js-navbar-vertical-aside-toggle-invoker i').tooltip('hide');
        });

        // INITIALIZATION OF MEGA MENU
        // =======================================================
        var megaMenu = new HSMegaMenu($('.js-mega-menu'), {
            desktop: {
                position: 'left'
            }
        }).init();


        // INITIALIZATION OF NAVBAR VERTICAL NAVIGATION
        // =======================================================
        var sidebar = $('.js-navbar-vertical-aside').hsSideNav();


        // INITIALIZATION OF TOOLTIP IN NAVBAR VERTICAL MENU
        // =======================================================
        $('.js-nav-tooltip-link').tooltip({boundary: 'window'})

        $(".js-nav-tooltip-link").on("show.bs.tooltip", function (e) {
            if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
                return false;
            }
        });


        // INITIALIZATION OF UNFOLD
        // =======================================================
        $('.js-hs-unfold-invoker').each(function () {
            var unfold = new HSUnfold($(this)).init();
        });


        // INITIALIZATION OF FORM SEARCH
        // =======================================================
        $('.js-form-search').each(function () {
            new HSFormSearch($(this)).init()
        });


        // INITIALIZATION OF SELECT2
        // =======================================================
        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });


        // INITIALIZATION OF DATERANGEPICKER
        // =======================================================
        $('.js-daterangepicker').daterangepicker();

        $('.js-daterangepicker-times').daterangepicker({
            timePicker: true,
            startDate: moment().startOf('hour'),
            endDate: moment().startOf('hour').add(32, 'hour'),
            locale: {
                format: 'M/DD hh:mm A'
            }
        });

        var start = moment();
        var end = moment();

        function cb(start, end) {
            $('#js-daterangepicker-predefined .js-daterangepicker-predefined-preview').html(start.format('MMM D') + ' - ' + end.format('MMM D, YYYY'));
        }

        $('#js-daterangepicker-predefined').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);


        // INITIALIZATION OF CLIPBOARD
        // =======================================================
        $('.js-clipboard').each(function () {
            var clipboard = $.HSCore.components.HSClipboard.init(this);
        });
    });
</script>

<script>
    function setPaymentMethod(name) {
        $.get({
            url: '{{ url('/') }}/payment-mobile/set-payment-method/' + name,
            success: function () {
                $('#' + name).prop('checked', true);
                /*toastr.success(name.replace(/_/g, " ") + ' has been selected successfully');*/
                setTimeout(function () {
                    location.reload();
                }, 10);
            }
        });
    }

    setTimeout(function () {
        $('.stripe-button-el').hide();
        $('.razorpay-payment-button').hide();
    }, 10)
</script>

</body>
</html>
