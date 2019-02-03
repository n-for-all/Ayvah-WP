function AVA_Analytics() {
    this.c_name = "_ava_utmz";
    this.getQueryVariable = function(variable) {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split("=");
            if (pair[0] == variable) {
                return pair[1];
            }
        }
        return (false);
    };
    this.events = function() {
        var _this = this;
        jQuery(document).ready(function() {
            if (_this.getQueryVariable("utm_source") != "") {
                _this.createCookie(_this.c_name, _this.getQueryVariable("utm_source") + "|" + _this.getQueryVariable("utm_medium") + "|" + _this.getQueryVariable("utm_term") + "|" + _this.getQueryVariable("utm_campaign") + "|" + _this.getQueryVariable("utm_content"), 60);
            }
            var hostName = _this.getHost(window.location.hostname);
            var refererHostName = _this.getHost(document.referrer);
            if (refererHostName != hostName) {
                _this.createCookie(_this.c_name + "_referer", document.referrer, 60);
            }
        });
    };
    this.createCookie = function(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toGMTString();
        }
        document.cookie = name + "=" + value + expires + "; path=/";
    };

    this.getHost = function(hostName) {
        var domain = hostName;

        if (hostName != null) {
            var parts = hostName.split('.').reverse();
            if (parts.length > 2) {
                return parts[1];
            }
            return parts[0];
        }
        return '';
    };

    this.readCookie = function(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    };

    this.eraseCookie = function(name) {
        createCookie(name, "", 1);
    };
    this.events();
}
var AVA_Analytics = new AVA_Analytics();
