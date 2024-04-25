/*!
 * Countdown JS
 */

function count() {
    const timerIds = [];

    !function (n) {
        function o(n) {
            return n < 10 ? "0" + n : n
        }

        n.fn.showclock = function () {
            var s = new Date, t = n(this).data("date").split("-"), a = [0, 0];
            null != n(this).data("time") && (a = n(this).data("time").split(":"));
            var c = new Date(t[0], t[1] - 1, t[2], a[0], a[1]).getTime() / 1e3 - s.getTime() / 1e3;
            if (c <= 0 || isNaN(c)) {
                let product = n(this).data("value").split(":");
                clearInterval(timerIds[product[1]]);
                timerIds.splice(product[1], 1);
                this.remove()
                deleteProduct(product[0], product[1], 'unverified')
                return null;
            }
            var e = Math.floor(c / 86400);
            c %= 86400;
            var i = Math.floor(c / 3600);
            c %= 3600;
            var u = Math.floor(c / 60);
            c = Math.floor(c % 60);
            var d = "";
            0 != e && (d += "<div class='countdown-container days'>", d += "<span class='countdown-value days-bottom'>" + o(e) + "</span>", d += "<span class='countdown-heading days-top'>Day</span>", d += "</div>"), d += "<div class='countdown-container hours'>", d += "<span class='countdown-value hours-bottom'>" + o(i) + "</span>", d += "<span class='countdown-heading hours-top'>Hr's</span>", d += "</div>", d += "<div class='countdown-container minutes'>", d += "<span class='countdown-value minutes-bottom'>" + o(u) + "</span>", d += "<span class='countdown-heading minutes-top'>Min's</span>", d += "</div>", d += "<div class='countdown-container seconds'>", d += "<span class='countdown-value seconds-bottom'>" + o(c) + "</span>", d += "<span class='countdown-heading seconds-top'>Sec</span>", d += "</div>", this.html(d)
        }
            , n.fn.countdown = function () {
            var o = n(this);
            let timer;
            o.showclock(), timer = setInterval(function () {
                o.showclock()
            }, 1e3)
            timerIds.push(timer)
        }

    }

    (jQuery), jQuery(document).ready(function () {
        jQuery(".countdown").length > 0 && jQuery(".countdown").each(function () {
            jQuery(this).countdown()
        })
    });
}

count()