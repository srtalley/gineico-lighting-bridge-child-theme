// v2.0.5
jQuery(function($) {

    $('document').ready(function() {

        // get the window location and click the appropriate tab
        var url_vars = getUrlParams(location.search);
        if(url_vars.hasOwnProperty('action')) {
            if(url_vars.action == 'login') {
                $('.login-button').click();
            }
            if(url_vars.action == 'register') {
                $('.registration-button').click();
            }
        }

        
    });
    $(document).on('click', '.login-button', function (e) {
        e.preventDefault();
        $('.registration-button, .registration-section').removeClass('active');
        $('.login-button, .login-section').addClass('active');
    });
    $(document).on('click', '.registration-button', function (e) {
        e.preventDefault();
        $('.login-button, .login-section').removeClass('active');
        $('.registration-button, .registration-section').addClass('active');
    });
    /**
     * Get the URL query vars
     */
    /**
     * Accepts either a URL or querystring and returns an object associating 
     * each querystring parameter to its value. 
     *
     * Returns an empty object if no querystring parameters found.
     */
    function getUrlParams(urlOrQueryString) {
        if ((i = urlOrQueryString.indexOf('?')) >= 0) {
        const queryString = urlOrQueryString.substring(i+1);
        if (queryString) {
            return _mapUrlParams(queryString);
        } 
        }
    
        return {};
    }

    /**
     * Helper function for `getUrlParams()`
     * Builds the querystring parameter to value object map.
     *
     * @param queryString {string} - The full querystring, without the leading '?'.
     */
    function _mapUrlParams(queryString) {
        return queryString    
        .split('&') 
        .map(function(keyValueString) { return keyValueString.split('=') })
        .reduce(function(urlParams, [key, value]) {
            if (Number.isInteger(parseInt(value)) && parseInt(value) == value) {
            urlParams[key] = parseInt(value);
            } else {
            urlParams[key] = decodeURI(value);
            }
            return urlParams;
        }, {});
    }
  
});