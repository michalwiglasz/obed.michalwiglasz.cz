$(function() {
    var headings = $('h1');
    var headingHeight = headings.outerHeight();
    var bulgarianConstant = 4;
    var current = null;

    var panel = $('<h1>').attr('id', 'panel').css({
        height: headingHeight,
        display: "none",
    });

    panel.click(function() {
        if (current) {
            $('html, body').animate({
                scrollTop: current.offset().top
            }, "fast");
        }
    })

    var span = $('<span>');
    panel.append(span);
    document.body.appendChild(panel[0]);

    var panelHeight = panel.outerHeight();

    var picker = $('<i id="panel-picker" class="fa fa-bars">').css({
        height: panelHeight,
        lineHeight: panelHeight + "px",
        width: panelHeight,
    });
    document.body.appendChild(picker[0]);

    var pickerMenu = $('<ul id="panel-picker-menu">').css({
        top: panelHeight,
        display: "none",
    });
    headings.each(function(index) {
        var me = $(this);

        var item = $('<li>').text(me.text());
        item.data('target', me);
        item.attr('class', me.attr('class'));
        item.click(function() {
            pickerMenu.hide();
            $('html, body').animate({
                scrollTop: $(this).data('target').offset().top
            }, "fast");
        })
        pickerMenu.append(item);
    });
    document.body.appendChild(pickerMenu[0]);

    picker.click(function() {
        pickerMenu.toggle("fast");
    })

    $(document).click(function(event) {
        if(!$(event.target).closest('#panel-picker').length) {
            if($('#panel-picker-menu').is(":visible")) {
                $('#panel-picker-menu').hide();
            }
        }
    });

    /*
    var line = $('<div>').css({
        height: "1px",
        width: "100%",
        background: "black",
        zIndex: 10,
        content: " ",
        position: "absolute",
        left: 0,
    });
    document.body.appendChild(line[0]);
    */


    var scrollHandler = function() {
        var windowTop = $(window).scrollTop();
        headings.each(function(index) {
            var me = $(this);
            var h1top = me.offset().top;
            if (h1top - bulgarianConstant <= windowTop) {
                current = me;
            }
            //console.log(me.text(), "h1", h1top, "w", windowTop, "h-W", h1top - windowTop, "w+b", windowTop + bulgarianConstant);
            //console.log(me.text(), h1top, "--->", h1top - bulgarianConstant, "<=", windowTop, h1top - bulgarianConstant <= windowTop);
        });

        if (current)  {

            var ul = current.nextUntil('h1').last();
            var ending = ul.offset().top + ul.outerHeight();
            var panelTop = ending - windowTop - panelHeight;

            //line.css('top', ending);
            //console.log(current.text(), ending, windowTop, panelTop);

            span.html(current.html());
            panel.attr('class', current.attr('class'));
            panel.css('top', Math.min(0, panelTop));
            //picker.css('top', Math.min(0, panelTop));
            panel.show();
        } else {
            panel.hide();
        }
    };

    scrollHandler();
    $(window).on('scroll', scrollHandler);
});
