import template from './tb-product-extra-info.html.twig';

const { Component, Mixin, Data: { Criteria } } = Shopware;

Component.register('tb-product-extra-info', {
    template,

    inject: [
        'repositoryFactory',
        'syncService'
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
            isPersisted: false,
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
                    '[TbProductExtraInfo] Fehler beim Laden der Zusatzinfo',
                    error,
                );

                this.createNotificationError({
                    message: 'Die Zusatzinfo konnte nicht geladen werden.',
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
                    message: 'Die Zusatzinfo wurde gespeichert.',
                });
            } catch (error) {
                console.error(
                    '[TbProductExtraInfo] Fehler beim Speichern der Zusatzinfo',
                    error,
                );

                this.createNotificationError({
                    message: 'Die Zusatzinfo konnte nicht gespeichert werden.',
                });
            } finally {
                this.isSaving = false;
            }
        }
    },
});