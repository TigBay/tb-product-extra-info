import template from './tb-product-extra-info.html.twig';

const { Component, Mixin, Data: { Criteria } } = Shopware;

Component.register('tb-product-extra-info', {
    template,

    inject: [
        'repositoryFactory',
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    props: {
        productId: {
            type: String,
            required: true,
        },

        disabled: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            extraInfo: null,
            isLoading: false,
            isSaving: false,
            };
    },

    computed: {
        productExtraInfoRepository() {
            return this.repositoryFactory.create('product_extra_info');
        },
    },

    watch: {
        productId: {
            immediate: true,

            handler() {
                this.loadExtraInfo();
            },
        },
    },

    methods: {
        async loadExtraInfo() {
            if (!this.productId) {
                return;
            }

            this.isLoading = true;

            try {
                const criteria = new Criteria(1, 1);

                criteria.addFilter(
                    Criteria.equals('productId', this.productId),
                );

                const result = await this.productExtraInfoRepository.search(
                    criteria,
                    Shopware.Context.api,
                );

                this.extraInfo = result.first();

                if (!this.extraInfo) {
                    this.extraInfo = this.productExtraInfoRepository.create(
                        Shopware.Context.api,
                    );

                    this.extraInfo.productId = this.productId;
                    this.extraInfo.extraText = null;
                    this.extraInfo.priority = 0;
                }
            } catch (error) {
                console.error(
                    '[TbProductExtraInfo] Failed to load extra info',
                    error,
                );

                this.createNotificationError({
                    message: this.$tc('tb-product-extra-info.loadError'),
                });
            } finally {
                this.isLoading = false;
            }
        },

        async saveExtraInfo() {
            if (!this.extraInfo || this.disabled) {
                return;
            }

            this.isSaving = true;

            try {
                await this.productExtraInfoRepository.save(
                    this.extraInfo,
                    Shopware.Context.api,
                );

                await this.loadExtraInfo();

                this.createNotificationSuccess({
                    message: this.$tc('tb-product-extra-info.saveSuccess'),
                });
            } catch (error) {
                console.error(
                    '[TbProductExtraInfo] Failed to save extra info',
                    error,
                );

                this.createNotificationError({
                    message: this.$tc('tb-product-extra-info.saveError'),
                });
            } finally {
                this.isSaving = false;
            }
        }
    },
});