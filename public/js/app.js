class Application {

    constructor() {
        $("a[theme-toggle]").each(function () {
            $(this).click(function () {
                Application.toggleTheme($(this));
            });
        });

        $(window).scroll(function() {
            $( "nav.main-nav.fixed" ).each(function () {
                console.log($(this).height());
                $(this).toggleClass("scrolled", $(window).scrollTop() > 0);
            });
        });
    }

    static toggleTheme(button) {
        $.ajax({
            type: "POST",
            url: window.location.protocol + "//" + window.location.host + "/api/toggleTheme/",
            success: function(response) {
                if (response.result == "success") {
                    var body = $("body");
                    var icon = button.children().first();

                    if (body.hasClass("theme--dark")) {
                        body.removeClass("theme--dark");
                        body.addClass("theme--light");
                        icon.removeClass("fa-sun");
                        icon.addClass("fa-moon");
                        button.removeClass("active");
                        icon.parent().parent().attr("title", "Dark Theme");
                    } else {
                        body.removeClass("theme--light");
                        body.addClass("theme--dark");
                        icon.removeClass("fa-moon");
                        icon.addClass("fa-sun");
                        button.addClass("active");
                        icon.parent().parent().attr("title", "Light Theme");
                    }
                } else {
                    alert("API Call Failed");
                }
            }
        });
    }
}

$(document).ready(function () {
    app = new Application();
});