pimcore.registerNS("pimcore.plugin.PimcoreElasticBundle");

pimcore.plugin.PimcoreElasticBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.PimcoreElasticBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("PimcoreElasticBundle ready!");
    }
});

var PimcoreElasticBundlePlugin = new pimcore.plugin.PimcoreElasticBundle();
