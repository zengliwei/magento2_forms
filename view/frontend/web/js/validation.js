/**
 * Copyright (c) Zengliwei. All rights reserved.
 * Each source file in this distribution is licensed under OSL 3.0, see LICENSE for details.
 */
define([
    'jquery',
    'uiRegistry'
], function ($, registry) {
    'use strict';

    return function (opts, element) {
        opts = $.extend(true, {
            blockId: null
        }, opts);

        registry.get(opts.blockId + '.provider', function (provider) {
            $(element).on('submit', function () {
                provider.set('params.invalid', false);
                provider.trigger('data.validate');
                return !provider.get('params.invalid');
            });
        });
    };
});
