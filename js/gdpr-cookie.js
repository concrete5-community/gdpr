(function() {
    var consentRoute = CCM_DISPATCHER_FILENAME + '/ccm/system/gdpr/consent';

    var GdprCookie = {
        reset: function() {
            document.cookie = 'cookieconsent_status=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';

            $.post(consentRoute, {
                'consent': 'reset'
            }).done(function(){
                window.location.reload();
            });
        },
        allowCookies: function() {
            $.post(consentRoute, {
                'consent': 'allow'
            }).done(function(){
                window.location.reload();
            });
        },
        denyCookies: function() {
            this.clearCookies();
            this.clearLocalStorage();
            this.clearSessionStorage();

            $.post(consentRoute, {
                'consent': 'deny'
            }).done(function(){
                window.location.reload();
            });
        },
        clearCookies: function() {
            document.cookie.split(";").forEach(function(c) {
                if (c.indexOf('cookieconsent_status') !== -1) {
                    return;
                }

                document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
            });
        },
        clearLocalStorage: function() {
            if (typeof localStorage !== "undefined") {
                localStorage.clear();
            }
        },
        clearSessionStorage: function() {
            // The same as Local Storage, except that it stores the data for only one session
            if (typeof sessionStorage !== "undefined") {
                sessionStorage.clear();
            }
        }
    };

    $(document).on('click', '.cc-compliance .cc-allow', function() {
        GdprCookie.allowCookies();
    });

    $(document).on('click', '.cc-compliance .cc-deny', function() {
        GdprCookie.denyCookies();
    });

    $(document).on('click', '.gdpr-reset-cookie-consent', function() {
        GdprCookie.reset();
    });
})();
