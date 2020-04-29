pimcore.registerNS("pimcore.plugin.PersonalizedSearchBundle");

pimcore.plugin.PersonalizedSearchBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.PersonalizedSearchBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("PersonalizedSearchBundle ready!");
    }
});

var PersonalizedSearchBundlePlugin = new pimcore.plugin.PersonalizedSearchBundle();
