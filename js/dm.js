/*globals $*/
/*jslint eqeq:true plusplus:true*/

var oApp = window.oApp || {};

(function () {

    'use strict';

    oApp.somevariable = 'test';

    oApp.immediatelyInvokedFunctionExpression = (function () {

        $('#result').on('click', '#tabs li', function(){
            var el = $(this),
                targetid = el.data('targetid'),
                targetdiv = $('#tab-' + targetid);

            $('.tab-content').addClass('hide');
            targetdiv.removeClass('hide');

            $('li', '#tabs').removeClass('active');
            el.addClass('active');

        });

        $('#result').on('click', '#tab-files .jsShowItems', function(){
            var el = $(this),
                target = el.closest('li'),
                list = $('.items', target);

            list.toggleClass('hide');

        });

        $('#result').on('click', '#tab-issues .jsShowItems', function(){
            var el = $(this),
                target = el.closest('li'),
                list = $('.items', target);

            list.toggleClass('hide');

        });


    }());

    oApp.standardFunctionExpression = function () {

    };

}());