jQuery(function($) {
    var chimunthu_lgpd_window = localStorage.getItem("chimunthu_lgpd_window");

    if (!chimunthu_lgpd_window) {
        $('.chimunthu-lgpd-bar-main').show();
    }

    $('.chimunthu-lgpd-bar-main .chimunthu-lgpd-button').on('click', function(e) {
        localStorage.setItem("chimunthu_lgpd_window", true);
        $('.chimunthu-lgpd-bar-main').hide();
        e.preventDefault();
    })
})