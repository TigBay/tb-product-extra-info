import template from './sw-product-settings-form.html.twig';

const { Component } = Shopware;

Component.override('sw-product-settings-form', {
    template,

    computed: {
        hasProductExtraInfo() {
            return Boolean(
                this.product
                && this.product.extensions
                && this.product.extensions.productExtraInfo
            );
        },
    },
});