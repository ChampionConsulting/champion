(function ($, CFG) {
    function setupAccordion(config) {
        var k, j, selector;

        for (k in config) {
            if (!config.hasOwnProperty(k)) continue;

            if (k.indexOf('*') !== -1) {
                selector = 'a[class*=' + k.substr(0, k.length -1) + ']';
            }
            else {
                selector = 'a.' + k;
            }

            for (j in config[k]) {
                if (!config[k].hasOwnProperty(j)) continue;

                $(selector).data(j, config[k][j]);

                if (j === 'state' && config[k][j] === 'collapsed') {
                    $('#' + k).css('display', 'block');
                }
            }
        }
    }

    $(document).ready(function () {
        setupAccordion(CFG);

        $('a[class*=viewmore]').click(function (e) {
            e.preventDefault();

            var contName = this.className.replace(/^.+\s(viewmore\d?)(\s+)?.*$/, '$1');

            $('#' + contName).slideToggle(500);

            if ($(this).data('state') === 'collapsed') {
                $('.' + contName)
                .data('state', 'expanded')
                .html($(this).data('expanded'))
                .attr('title', $('<div/>').html($(this).data('expanded')).text())
                .addClass($(this).data('expandedClass'));
            }
            else {
                $('.' + contName)
                .data('state', 'collapsed')
                .html($(this).data('collapsed'))
                .attr('title', $('<div/>').html($(this).data('collapsed')).text())
                .removeClass($(this).data('expandedClass'));

                $('html, body').animate({
                    scrollTop:$('#' + contName).prev('table').offset().top
                }, 50);
            }
        });

        $('a[class*=viewall]').click(function (e) {
            e.preventDefault();

            var i, n, classes = this.className.split(' '), contNames = [];

            for (i = 0, n = classes.length; i < n; i++) {
                if (classes[i].indexOf('viewall') === 0) {
                    contNames.push(classes[i]);
                }
            }

            $('#' + contNames.join(',#')).slideToggle(500);

            if ($(this).data('state') === 'collapsed') {
                $('.' + contNames[0])
                .data('state', 'expanded')
                .html($(this).data('expanded'))
                .attr('title', $('<div/>').html($(this).data('expanded')).text())
                .parents('ul')
                .addClass($(this).data('expandedClass'));
            }
            else {
                $('.' + contNames[0])
                .data('state', 'collapsed')
                .html($(this).data('collapsed'))
                .attr('title', $('<div/>').html($(this).data('collapsed')).text())
                .parents('ul')
                .removeClass($(this).data('expandedClass'));

                $('html, body')
                .animate({
                    scrollTop:$('#' + contNames[0]).offset().top
                }, 50);
            }
        });
    });
})(jQuery, ACCORDION_TITLES);