$(function() {
    var headings = $('h1');
    var headingHeight = headings.outerHeight();

    var picker = $('<i id="panel-picker" class="fa fa-bars">').css({
        height: headingHeight,
        lineHeight: headingHeight + "px",
        width: headingHeight,
    });
    document.body.appendChild(picker[0]);

    var pickerMenu = $('<ul id="panel-picker-menu">').css({
        top: headingHeight,
        display: "none",
    });

    headings.each(function(index) {
        var me = $(this);
        var target = document.getElementById("d-" + me.attr('id'));

        var item = $('<li>').text(me.text());
        item.data('target', target);
        item.attr('class', me.attr('class'));
        item.click(function() {
            pickerMenu[0].scrollTop = 0;
            pickerMenu.hide();
            $(this).data('target').scrollIntoView({ behavior: "smooth" });
        })
        pickerMenu.append(item);
    });
    document.body.appendChild(pickerMenu[0]);

    picker.click(function() {
        pickerMenu[0].scrollTop = 0;
        pickerMenu.toggle("fast");
    })

    $(document).click(function(event) {
        if(!$(event.target).closest('#panel-picker').length) {
            if($('#panel-picker-menu').is(":visible")) {
                $('#panel-picker-menu').hide();
            }
        }
    });
});
