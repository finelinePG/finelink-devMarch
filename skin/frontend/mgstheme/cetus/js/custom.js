function parallaxInit() {
    mgsjQuery('.parallax').parallax("30%", 0.1);
}

function initSlider(el, number, aplay, stophv, nav, pag) {
    small = number;
    small768 = 1;
    if (number == 1) {
        small = 1;
    }
    else {
        if (number > 3) {
            small = 3;
        }
        small768 = 2;
    }
    mgsjQuery("#" + el).owlCarousel({
        items: number,
        lazyLoad: true,
        navigation: nav,
        pagination: pag,
        autoPlay: aplay,
        stopOnHover: stophv,
        navigationText: ["<button class='circle-prev icon-wrap t-nav-prev'><i class='fa fa-angle-left'></i></button>", "<button class='circle-next icon-wrap t-nav-next'><i class='fa fa-angle-right'></i></button>"],
        itemsDesktop: [1199, number],
        itemsDesktopSmall: [970, small],
        itemsTablet: [768, small768],
        itemsTabletSmall: false,
        itemsMobile: [480, 1],
        itemsCustom: false
    });
}

function toggleEl(el) {
    //mgsjQuery('.toggle-el').hide();
    mgsjQuery('#' + el).slideToggle('fast');
}

function initThemeJs() {
    // init tooltip
    mgsjQuery('.tooltip-links').tooltip({
        selector: "[data-toggle=tooltip]",
        container: "body"
    });
}

mgsjQuery(window).load(function () {
    mgsjQuery(window).bind('body', function () {
        parallaxInit();
    });

    var $container = mgsjQuery('.masonry-grid');
    // initialize
    $container.masonry({
        itemSelector: '.item'
    });

    initThemeJs();

    if (mgsjQuery('.scroll-to-top').length) {
        mgsjQuery(window).scroll(function () {
            if (mgsjQuery(this).scrollTop() > 1) {
                mgsjQuery('.scroll-to-top').css({bottom: "25px"});
            } else {
                mgsjQuery('.scroll-to-top').css({bottom: "-100px"});
            }
        });

        mgsjQuery('.scroll-to-top').click(function () {
            mgsjQuery('html, body').animate({scrollTop: '0px'}, 800);
            return false;
        });
    }

});

// init gmap
function initGmap(address, html, image, grayscaleColor) {
    mgsjQuery.ajax({
        type: "GET",
        dataType: "json",
        url: "http://maps.googleapis.com/maps/api/geocode/json",
        data: {'address': address, 'sensor': false},
        success: function (data) {
            if (data.results.length) {
                latitude = data.results[0].geometry.location.lat;
                longitude = data.results[0].geometry.location.lng;

                var locations = [
                    [html, latitude, longitude, 2]
                ];
				
				var style = '';
				if(grayscaleColor==1){
					style = [ { "stylers": [ { "hue": "#000" },  {saturation: -100},
					{gamma: 0.90} ] } ];
				}
				
                var map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 14,
                    scrollwheel: false,
                    navigationControl: true,
                    mapTypeControl: false,
                    scaleControl: false,
                    draggable: true,
					styles: style,
                    center: new google.maps.LatLng(latitude, longitude),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

                var infowindow = new google.maps.InfoWindow();

                var marker, i;

                for (i = 0; i < locations.length; i++) {

                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                        map: map,
                        icon: image
                    });


                    google.maps.event.addListener(marker, 'click', (function (marker, i) {
                        return function () {
                            infowindow.setContent(locations[i][0]);
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                }
            }
        }
    });
}

var newCount = 2;
var hotCount = 2;
var featuredCount = 2;
var saleCount = 2;

// load more products
function loadMore(count, type, productCount, perRow) {
    mgsjQuery('#' + type + '_loadmore_button .loading').show();
    var request = new Ajax.Request(WEB_URL + 'mpanel/loadmore/' + type + '?perrow=' + perRow + '&p=' + count + '&limit=' + productCount, {
        onSuccess: function (response) {
            result = response.responseText;
            mgsjQuery('#' + type + '_product_container').append(result);
            mgsjQuery('#' + type + '_loadmore_button .loading').hide();

            initThemeJs();
        }
    });
}

// open overlay popup
function openOverlay() {
    mgsjQuery('#theme-popup').show();
}

// close overlay popup
function closeOverlay() {
    mgsjQuery('#theme-popup').hide();
}


var active = false;
var data = "";

// Price slider
function sliderAjax(url) {
    if (!active) {
        active = true;
        openOverlay();
        oldUrl = url;
        try {
            mgsjQuery.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: data,
                success: function (data) {
                    if (data.leftcontent) {
                        if (mgsjQuery('.block-layered-nav')) {
                            mgsjQuery('.block-layered-nav').empty();
                            mgsjQuery('.block-layered-nav').append(data.leftcontent);
                        }
                    }
                    if (data.maincontent) {
                        mgsjQuery('#product-list-container').empty();
                        mgsjQuery('#product-list-container').append(data.maincontent);
                    }
                    var hist = url.split('?');
                    if (window.history && window.history.pushState) {
                        window.history.pushState('GET', data.title, url);
                    }
                    initThemeJs();
                    closeOverlay();
                }
            });
        } catch (e) {
        }

        active = false;
    }
    return false;
}


// Ajax catalog load
function shopMore(url) {
    oldHtml = mgsjQuery('.category-products ul.products-grid').html();
    openOverlay();
    oldUrl = url;
    try {
        mgsjQuery.ajax({
            url: url,
            dataType: 'json',
            type: 'post',
            data: data,
            success: function (data) {
                if (data.leftcontent) {
                    if (mgsjQuery('.block-layered-nav')) {
                        mgsjQuery('.block-layered-nav').empty();
                        mgsjQuery('.block-layered-nav').append(data.leftcontent);
                    }
                }
                if (data.maincontent) {
                    mgsjQuery('#product-list-container').empty();
                    mgsjQuery('#product-list-container').append(data.maincontent);
                    mgsjQuery('.category-products ul.products-grid').prepend(oldHtml);
                }
                initThemeJs();
                closeOverlay();
            }
        });
    } catch (e) {
    }
}

mgsjQuery(document).ready(function () {
    var TopFixCart = mgsjQuery(".cart-sidebar .style-dropdow-menu");
    if (mgsjQuery(this).width() > 992) {
        TopFixCart.addClass("dropdown-menu-cart");
        TopFixCart.removeClass("dropdown-menu");
    }
    var TopFixMenu = mgsjQuery(".sticky-menu .nav-container .main-menu");
    var TopFixHeader = mgsjQuery(".sticky-menu .header-content");
    mgsjQuery(window).scroll(function () {
        if (mgsjQuery(this).scrollTop() > 50 && mgsjQuery(this).width() >= 992) {
            TopFixMenu.addClass("sticky_menu");
            TopFixHeader.addClass("sticky_menu");
			mgsjQuery(".sticky-menu .nav-container .main-menu .sub-container").addClass('container');
        } else {
            TopFixMenu.removeClass("sticky_menu");
            TopFixHeader.removeClass("sticky_menu");
			mgsjQuery(".sticky-menu .nav-container .main-menu .sub-container").removeClass('container');
        }
    });
    if (!mgsjQuery('body').hasClass('cms-index-index') || mgsjQuery(this).width() <= 767) {
        mgsjQuery('.nav-sub-menu .img-vertical-menu').hide();
    }
    mgsjQuery('.btn-menu').on('click', function () {
        mgsjQuery('.nav-sub-menu .img-vertical-menu').slideToggle('fast');
    });
    
    if(mgsjQuery(window).width() <= 991){
        mgsjQuery('.btn-menu').click(function(){
            mgsjQuery('.nav-sub-menu .img-vertical-menu .mega-nav').slideToggle('fast');
        });
    }
    
    mgsjQuery('.normal-search > button').on('click', function () {
        mgsjQuery('.normal-search .search-mini-form').slideToggle('fast');
    });
    
    mgsjQuery('.n-search > button').on('click', function () {
        mgsjQuery('.n-search .search-mini-form').slideToggle('fast');
    });

    mgsjQuery('.toplinks-mobile > button').on('click', function () {
        mgsjQuery('.toplinks-mobile > ul').slideToggle('slow');
    });
    mgsjQuery('.block-cart-header > .cart3').on('click', function () {
        mgsjQuery('.block-cart-header > .dropdown-menu').slideToggle('slow');
    });

    mgsjQuery('.block-cart-header > .menu-button').on('click', function () {
        mgsjQuery('.block-cart-header > .dropdown-menu').slideToggle('slow');
    });
    mgsjQuery('.navbar-toggle.menu-button').on('click', function () {
        mgsjQuery('.menu-mobile #mainMenu').slideToggle('slow');
    });
    var w = mgsjQuery(window).width();
    if (w > 992) {
        mgsjQuery('.mega-nav .level0').addClass('show-submenu');
    }
    if (w < 992) {
        mgsjQuery('.mega-nav .level0').removeClass('show-submenu');
    }
    setTimeout(fixHeightBrand, 3000);
	mgsjQuery(window).resize(function () {
		 setTimeout(fixHeightBrand, 1000);
	});
    /* jQuery product tab responsive */
    mgsjQuery('.tip-electronics').click(function () {            
		mgsjQuery('html, body').animate({
				scrollTop : mgsjQuery('.electronics').offset().top
		}, 'slow');
		return false;
    });
    mgsjQuery('.tip-furnitures').click(function () {			
		mgsjQuery('html, body').animate({
				scrollTop : mgsjQuery('.furnitures').offset().top
		}, 'slow');
		return false;
    });
    mgsjQuery('.tip-health').click(function () {			
		mgsjQuery('html, body').animate({
				scrollTop : mgsjQuery('.health').offset().top
		}, 'slow');
		return false;
    });
    mgsjQuery('.tip-toys').click(function () {
		mgsjQuery('html, body').animate({
				scrollTop : mgsjQuery('.toys').offset().top
		}, 'slow');
		return false;
    });
    mgsjQuery('.tip-shoes').click(function () {
		mgsjQuery('html, body').animate({
				scrollTop : mgsjQuery('.shoes').offset().top
		}, 'slow');
		return false;
    });
    mgsjQuery('.tip-sport').click(function () {
		mgsjQuery('html, body').animate({
				scrollTop : mgsjQuery('.sport').offset().top
		}, 'slow');
		return false;
    });
	
	if(mgsjQuery('.scroll-to-top').length){
		mgsjQuery(window).scroll(function(){
			if (mgsjQuery(this).scrollTop() > 1) {
				mgsjQuery('.scroll-to-top').css({bottom:"25px"});
			} else {
				mgsjQuery('.scroll-to-top').css({bottom:"-100px"});
			}
		});

		mgsjQuery('.scroll-to-top').click(function(){
			mgsjQuery('html, body').animate({scrollTop: '0px'}, 800);
			return false;
		});
	}

});

function setTabBackground(url) {
    $('tab-background').setStyle({backgroundImage: 'url(' + url + ')'});
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
            c = c.substring(1);
        if (c.indexOf(name) == 0)
            return c.substring(name.length, c.length);
    }
    return "";
}

function dontShowPopup(el) {
    if ($(el).checked == true) {
        var d = new Date();
        d.setTime(d.getTime() + (24 * 60 * 60 * 1000 * 365));
        var expires = "expires=" + d.toUTCString();
        document.cookie = 'newsletterpopup' + "=" + 'nevershow' + "; " + expires;
    } else {
        document.cookie = 'newsletterpopup' + "= ''; -1";
    }


}
/* Tab product */
mgsjQuery(document).ready(function () {
    mgsjQuery(".rating-category .rating-links a").click(function (event) {
        event.preventDefault();
        mgsjQuery("#product_tabs .tab-content .active").removeClass("active");
        mgsjQuery("#product_tabs .nav-tabs .active").removeClass("active");
        mgsjQuery("#product_tabs #box-reviews").addClass("active");
        mgsjQuery("#product_tabs #reviews-tab").addClass("active");
        mgsjQuery('html, body').animate({
            scrollTop: mgsjQuery("#product_tabs").offset().top
        }, 1000);
    });
});

function closeMgs() {
    mgsjQuery.magnificPopup.close();
}
/* Fix height brands */
function fixHeightBrand(){
	    var maxbrandheight = -1;
    mgsjQuery('.featured-brands .brands-content .brands > div img').each(function () {
        var currentheight = mgsjQuery(this).height();
        if (currentheight > maxbrandheight) {
            maxbrandheight = currentheight;
        }
    });
    mgsjQuery('.featured-brands .brands-content .brands > div').css('height', maxbrandheight);
}