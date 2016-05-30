var raygunPulse = (function (global) {

    return {
        init: function (options) {
            global.console.log("calling rg4js: " + options.apiKey +" with: " + options.identifier);
            global.rg4js('apiKey', options.apiKey);
            global.rg4js('enablePulse', true);
            global.rg4js('setUser', {
                identifier: options.identifier,
                isAnonymous: false
            });
        }
    };
})(window);
