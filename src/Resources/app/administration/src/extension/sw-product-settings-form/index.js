import template from './sw-product-detail-base.html.twig';

const { Component } = Shopware;

Component.override('sw-product-detail-base', {
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