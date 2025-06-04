var __header;
var __scrolling = false;
var __pools = [];
var __olmo = false;
var __pricesSlideing = false;

function scrollToContent() {
    if (__scrolling === false) {
        content_position = $('a.anchor[name=content]').offset().top;
        if ($(window).scrollTop() < content_position) {
            __scrolling = true;
            $.smoothScroll({
                scrollElement: $('html,body'),
                scrollTarget: $('a.anchor[name=content]'),
                easing: 'swing',
                speed: 1000,
                offset: -90,
                afterScroll: function () {
                    __scrolling = false;
                }
            });
        }
    }
}

function scrollToTop() {
    $.smoothScroll({
        scrollElement: $('html,body'),
        scrollTarget: $('#wrapper'),
        easing: 'swing',
        speed: 1000,
        offset: 0
    });
}

function onTop() {
    if ($(this).scrollTop() < 50) {
        $('a.jump').removeClass('show');
    } else {
        $('a.jump').addClass('show');
    }
}

function slidePrices(dir){
    if(__pricesSlideing === false){
        var cols = $('section.overview.room-total div.row.th > div.rooms-wrapper div.rooms div.room').length;
        var step = $('section.overview.room-total div.row.th > div.rooms-wrapper div.rooms div.room').outerWidth() + 5;
        var max = ((step * cols) - (step*5))*-1;
        var act = parseInt($('section.overview.room-total div.row > div.rooms-wrapper div.rooms').css('left'));
        var value = dir == 'next' ? "-=" + step : "+=" + step;
        var np = dir == 'next' ? act - step : act + step;
        
        if((dir == 'prev' && act < 0) || (dir == 'next' && act > max)){
            __pricesSlideing = true;
            $('section.overview.room-total div.row > div.rooms-wrapper div.rooms').animate({
                left: value,
            }, 1000, function() {
                if(np < 0){
                    $('section.overview.room-total div.row.th a.nav.prev').removeClass('hidden');
                }else{
                    $('section.overview.room-total div.row.th a.nav.prev').addClass('hidden');
                }
                if(np > max){
                    $('section.overview.room-total div.row.th a.nav.next').removeClass('hidden');
                }else{
                    $('section.overview.room-total div.row.th a.nav.next').addClass('hidden');
                }
                __pricesSlideing = false; 
            });
        }
    }
}

function backlink(){
    window.history.back();
}

function initdatepicker() {
    $('input.date').each(function (k, v) {
        if ($(this).hasClass('picker__input') === false) {

            // init
            var __min = $(this).data('date-min') ? new Date($(this).data('date-min')) : undefined;
            var __max = $(this).data('date-max') ? new Date($(this).data('date-max')) : undefined;
            var __range = $(this).data('date-range');
            var __years = $(this).data('date-years') == false ? false : true;
            var __months = $(this).data('date-months') == false ? false : true;
            var __format = $(this).data('date-format') != undefined ? $(this).data('date-format') : 'dd.mm.yyyy';
            var __hidden = $(this).data('date-hidden') != undefined ? $(this).data('date-hidden') : 'yyyy-mm-dd';

            // classes
            var __class_year = 'picker__year';
            var __class_today = 'picker__button--today';

            $(this).data('value', $(this).val());
            var $input = $(this).pickadate({
                selectYears: __years,
                selectMonths: __months,
                container: '#datepicker-container',
                format: __format,
                formatSubmit: __hidden,
                hiddenName: true,
                closeOnSelect: true,
                closeOnClear: false,
                max: __max,
                min: __min,
                klass: {
                    buttonToday: __class_today,
                    year: __class_year
                }
            });

            if (__range) {
                var __clicked = $(this).hasClass('date-from') ? 'from' : 'to';
                var __opposite = __clicked == 'from' ? 'to' : 'from';
                $input.pickadate('picker').on('set', function (event) {
                    if (event.select) {
                        var __select = $('input.date.date-' + __clicked + '[data-date-range="' + __range + '"]').pickadate('picker').get('select');
                        var __related = $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').get('select');
                        if (__opposite == 'to') {
                            __select.obj.setDate(__select.obj.getDate() + 1);
                            $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('min', __select);
                            if (__related && __related.pick <= __select.pick) {
                                $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('select', __select);
                            }
                        } else {
                            __select.obj.setDate(__select.obj.getDate() - 1);
                            $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('max', __select);
                            if (__related && __related.pick >= __select.pick) {
                                $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('select', __select);
                            }
                        }
                    } else if ('clear' in event) {
                        if (__opposite == 'from') {
                            $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('max', false);
                        } else {
                            $('input.date.date-' + __opposite + '[data-date-range="' + __range + '"]').pickadate('picker').set('min', false);
                        }
                    }
                });
            }

        }
    });
}

function selecotraction(e) {
    var elementCount = $('.rooms-wrap .room-wrap').length;

    //add/remove room
    if ($(e).hasClass('room-add')) {
        //add
        var cloned = $('.rooms-wrap .room-wrap:first').clone(true);
        cloned.addClass('fadedout');
        cloned.find('input, select').val('');
        cloned.find('.age').parents('.input').remove();
        cloned.insertAfter($('.rooms-wrap .room-wrap:last'));
        setTimeout(function () {
            $('.rooms-wrap .room-wrap.fadedout').removeClass('fadedout');
        }, 10);
        elementCount++;
    } else if (!$(e).hasClass('room-nodelete')) {
        //remove
        $(e).parents('.room-wrap').addClass('fadedout');
        setTimeout(function () {
            $(e).parents('.room-wrap').remove();
        }, 300);
        elementCount--;
    }

    //reorder
    setTimeout(function () {
        $('.rooms-wrap .room-wrap').each(function (k, v) {
            var _name = 'rooms[' + k + ']';
            // change nr
            console.log($(this));
            $(this).attr('data-room-key', k);
            $(this).attr('id', 'room-' + k);
            $(this).find('select.room-select').attr('id', 'rooms-' + k + '-room').attr('name', _name + '[room]');
            $(this).find('select.package-select').attr('id', 'rooms-' + k + '-package').attr('name', _name + '[package]');
            $(this).find('input.room-adults').attr('id', 'rooms-' + k + '-adults').attr('name', _name + '[adults]');
            $(this).find('input.room-children').attr('id', 'rooms-' + k + '-children').attr('name', _name + '[children]');
            $(this).find('input.age').each(function (k, v) {
                $(this).attr('id', 'rooms-' + k + '-ages-' + k + '-age').attr('name', _name + '[ages][' + k + '][age]');
            });
        });

        //show/hide delete button
        if (elementCount <= 1) {
            $('.rooms-wrap .room-wrap').find('.room-remove').addClass('room-nodelete');
        } else {
            $('.rooms-wrap .room-wrap').find('.room-remove').removeClass('room-nodelete');
        }
    }, 300);
}

function initchildrenaction() {

    $('input.room-children').keyup(function (event) {
        childrenageaction(this);
    });

    $('input.room-children').change(function (event) {
        childrenageaction(this);
    });

}

function childrenageaction(e) {
    var room = $(e).parents('.room-wrap');
    var roomKey = room.attr('data-room-key');
    var childCount = $(e).val();
    var childAgeInputsCount = room.find('input.age').size();

    //check childCount
    if (isNaN(childCount)) {
        childCount = 0;
        $(e).val(0);
    } else if (childCount > 4) {
        childCount = 4;
        $(e).val(4);
    }

    if (childCount > childAgeInputsCount) {

        for (var x = childAgeInputsCount; x < childCount; x++) {
            var __clone = $($(e).parent('div.input')).clone();
            __clone.addClass('room-child-age').addClass('fadedout');

            var _name = 'rooms[' + roomKey + '][ages][' + x + '][age]';

            // change nr
            $(__clone).find('label').text(__translations.childage).attr('for', 'room-' + roomKey + '-age-' + x);
            $(__clone).find('input').attr('id', 'room-' + roomKey + '-age-' + x);
            $(__clone).find('input').attr('name', _name);
            $(__clone).find('input').val('');
            $(__clone).find('input').removeClass('children');
            $(__clone).find('input').addClass('age');
            $(__clone).find('input').attr('placeholder', __translations.childage);


            // add
            $(e).parents('div.room-col').append(__clone);
            setTimeout(function () {
                $('.rooms-wrap .room-wrap .room-child-age.fadedout').removeClass('fadedout');
            }, 1);
        }

    } else {
        for (var x = childAgeInputsCount; x >= childCount; x--) {
            room.find('#room-' + roomKey + '-age-' + x).parent('div.input.fadedout').addClass('fadedout');
            room.find('#room-' + roomKey + '-age-' + x).parent('div.input').remove();
        }
    }
}

$(window).scroll(function () {
    onTop();
});

$(document).ready(function () {

    //fadeout loading-animation
    var fadeoutTime = docRoute === homeRoute ? 300 : 0;
    setTimeout(function () {
        $('body').addClass('fully-loaded');
        setTimeout(function () {
            $('.loading-overlay').remove();
        }, 1000);
    }, fadeoutTime);

    //videojs
    $('video.video-js').each(function () {
        videojs($(this).attr('id'), {}, function () {
            // Player (this) is initialized and ready.
        });
    });

    // backlink
    var referer = document.referrer;
    var domain = window.location.protocol + '//' + window.location.hostname;
    if(referer != '' && referer.indexOf(domain) == 0){
        $('section.quicklinks > .quicklink.back').removeClass('hidden');
    }

    // mobile menu
    $('.nav-trigger-wrapper').click(function (event) {
        event.preventDefault();
        $('body').toggleClass('mobile-menu');
    });

    $(document).on('click', 'body.mobile-menu nav.menu ul.menu > li > a, body.mobile-menu nav.menu ul.menu > li > .fake', function (event) {
        event.preventDefault();
        var state = $(this).parents('li').hasClass('open') ? 'open' : 'hidden';
        $('nav.menu ul.menu li').removeClass('open');
        if (state == 'hidden') {
            $(this).parents('li').addClass('open');
        }
    });

    // languages
    $('div.menu div.right div.top span.languages > a').click(function (event) {
        event.preventDefault();
        $('div.menu div.right div.top span.languages').toggleClass('show');
    });

    // jump
    $('a.jump').click(function (event) {
        event.preventDefault();
        scrollToTop();
    });

    // header
    __header = $('header .bxslider').bxSlider({
        auto: $('header .bxslider .bxslide').length > 1 ? true : false,
        pager: true,
        controls: true,
        speed: 800,
        pause: 8000,
        nextText: '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
        prevText: '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
//        onSliderLoad: function (currentIndex) {
//            $('header.images .bx-controls .bx-controls-direction').appendTo($('header.images .bx-controls .bx-pager'));
//        },
        onSlideBefore: function (e, oi, ni) {
            $('header.images .header-text').animate({
                opacity: 0
            }, 600, function () {
                if ($(e).find('div.bxslide').data('type') === 'teaser') {
                    $('header.images .header-text .line-1').text($(e).find('div.bxslide').data('line-1'));
                    $('header.images .header-text .line-2').text($(e).find('div.bxslide').data('line-2'));
                    $('header.images .header-text').animate({
                        opacity: 1
                    }, 600, function () {

                    });
                }
            });

        }
    });

    // news
    $('header.images section.news > a').click(function (event) {
        event.preventDefault();
        var fws = 1000;
        var cs = 500;
        console.log(window.innerWidth);
        if (window.innerWidth > 510) {
            if ($(this).parent('section.news').hasClass('show')) {
                $('section.mobile-news').slideToggle();
                $('header.images section.news').animate({ left: "-456px" }, fws, function () {
                    $('header.images section.news a.star').removeClass('hidden');
                    $('header.images section.news > div').addClass('hidden');
                }).animate({ left: "-376px" }, cs, function () {
                    $('header.images section.news a.arrow i').css({ 'transform': 'rotate(' + 0 + 'deg)' });
                    $('header.images section.news').removeClass('show');
                });
            } else {
                $('section.mobile-news').slideToggle();
                $('header.images section.news').animate({ left: "-456px" }, cs, function () {
                    $('header.images section.news a.star').addClass('hidden');
                    $('header.images section.news > div').removeClass('hidden');
                }).animate({ left: "0" }, fws, function () {
                    $('header.images section.news').addClass('show');
                    $('header.images section.news a.arrow i').css({ 'transform': 'rotate(' + 180 + 'deg)' });
                });
            }
        } else {
            if ($(this).parent('section.news').hasClass('show')) {
                $('section.mobile-news').slideToggle();
                $('header.images section.news').animate({ left: "-376px" }, 200, function () { });
                $('header.images section.news').removeClass('show');
            } else {
                $('section.mobile-news').slideToggle();
                $('header.images section.news').animate({ left: "-456px" }, 200, function () { });
                $('header.images section.news').addClass('show');
                $.smoothScroll({
                    scrollElement: $('html,body'),
                    scrollTarget: $('#mobile-news'),
                    easing: 'swing',
                    speed: 400,
                    offset: -50,
                    afterScroll: function () {
                        __scrolling = false;
                    }
                });
            }
        }
    });
    if ($(window).width() > 1050) {
        $('header.images section.news.open > a.button').trigger('click');
    }
    // jobs
    $('section.jobs > ul > li > h3').click(function(event){
        event.preventDefault();
        $(this).parents('li').toggleClass('open');
    });

    // impressions
    $('div.impressions .bxslider').bxSlider({
        auto: false,
        pager: false,
        controls: true,
        speed: 800,
        pause: 8000,
        nextText: '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
        prevText: '<i class="fa fa-chevron-left" aria-hidden="true"></i>'
    });

    // gallery
    $('section.gallery .bxslider').bxSlider({
        auto: false,
        pager: false,
        controls: true,
        speed: 800,
        pause: 8000,
        nextText: '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
        prevText: '<i class="fa fa-chevron-left" aria-hidden="true"></i>'
    });

    // pool
    $('section.pool').each(function (k, v) {

        $(this).attr('data-pool', k);

        __pools[k] = $(this).find('div.images .bxslider').bxSlider({
            auto: false,
            pager: false,
            controls: false,
            speed: 800
        });

        $(this).find('div.packages .bxslider').bxSlider({
            auto: false,
            pager: false,
            controls: $('section.pool[data-pool="' + k + '"] div.packages .bxslide').length > 1 ? true : false,
            speed: 800,
            pause: 8000,
            nextText: '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
            prevText: '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
            onSlideNext: function (se, oi, ni) {
                __pools[k].goToNextSlide();
            },
            onSlidePrev: function (se, oi, ni) {
                __pools[k].goToPrevSlide();
            }
        });

    });

    // datepicker
    initdatepicker();

    // selectors/childage
    initchildrenaction();

    // quicklinks
    $('html.touchevents .quicklinks .quicklink').click(function (event) {
        $(this).toggleClass('active');
    });

    // privacy
    $('a.cookie-hint-button').click(function (event) {
        event.preventDefault();
        $('#cookie-hint').remove();

        var d = new Date();
        d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = "hint=false; " + expires + "; path=/";

    });

    // treatments
    $("section.treatments .treatment-wrapper > a.button").click(function (event) {
        event.preventDefault();
        if ($(this).parents("div.treatment-wrapper").hasClass('show-details')) {
            $(this).find('span').text($(this).data('text-open'));
        } else {
            $(this).find('span').text($(this).data('text-close'));
        }
        $(this).parents("div.treatment-wrapper").toggleClass('show-details');
    });

    // overviews
    $("section.overview > div.show-more a").click(function (event) {
        event.preventDefault();
        if ($(this).parents("section.overview").hasClass('show-all')) {
            $(this).find('span').text($(this).data('text-more'));
            $(this).find('i').removeClass($(this).data('icon-less')).addClass($(this).data('icon-more'));
        } else {
            $(this).find('span').text($(this).data('text-less'));
            $(this).find('i').removeClass($(this).data('icon-more')).addClass($(this).data('icon-less'));
        }
        $(this).parents("section.overview").toggleClass('show-all');
    });

    // last-minute
    $('section.last-minute a.book, a.close').click(function (event) {
        event.preventDefault();
        if ($(this).hasClass('close')) {
            $('section.last-minute div.more').addClass('hidden');
            $('#lmo').val('');
            $('#lmon').val('');
            $('#lmor').val('');
            __olmo = false;
        } else {
            var __rel = $(this).parents('section.last-minute').data('rel');
            var __name = $(this).parents('section.last-minute').data('offer');
            var __room = $(this).parents('section.last-minute').data('room');
            if (__olmo != __rel) {
                $('.last-minute[data-rel!="' + __rel + '"] div.more').addClass('hidden');
                $('#lmf').detach().appendTo('section.last-minute[data-rel="' + __rel + '"] div.form');
                $('.last-minute[data-rel="' + __rel + '"] div.more').removeClass('hidden');
                $('#lmo').val(__rel);
                $('#lmon').val(__name);
                $('#lmor').val(__room);
            }
            __olmo = __rel;
        }
    });

    //set focus class in forms
    $(".input > *").focus(function () {
        $(this).parents('.input').addClass("focused");
    }).blur(function () {
        $(this).parents('.input').removeClass("focused");
    });

});
