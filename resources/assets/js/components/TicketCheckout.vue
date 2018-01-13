<template>
    <div>
        <div class="row middle-xs">
            <div class="col col-xs-6">
                <div class="form-group m-xs-b-4">
                    <label class="form-label">
                        Price
                    </label>
                    <span class="form-control-static">
                        £{{ priceInPounds }}
                    </span>
                </div>
            </div>
            <div class="col col-xs-6">
                <div class="form-group m-xs-b-4">
                    <label class="form-label">
                        Qty
                    </label>
                    <input v-model="quantity" class="form-control">
                </div>
            </div>
        </div>
        <div class="text-right">
            <button class="btn btn-primary btn-block"
                    @click="openStripe"
                    :class="{ 'btn-loading': processing }"
                    :disabled="processing"
            >
                Buy Tickets
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'price',
            'eventTitle',
            'eventId',
        ],
        data() {
            return {
                quantity: 1,
                stripeHandler: null,
                processing: false,
            }
        },
        computed: {
            description() {
                if (this.quantity > 1) {
                    return `${this.quantity} tickets to ${this.eventTitle}`
                }
                return `One ticket to ${this.eventTitle}`
            },
            totalPrice() {
                return this.quantity * this.price
            },
            priceInPounds() {
                return (this.price / 100).toFixed(2)
            },
            totalPriceInPounds() {
                return (this.totalPrice / 100).toFixed(2)
            },
        },
        methods: {
            initStripe() {
                const handler = StripeCheckout.configure({
                    key: 'pk_test_tLnoPurALFcHK8a7L1FYUmSF'
                })
                window.addEventListener('popstate', () => {
                    handler.close()
                })
                return handler
            },
            openStripe(callback) {
                this.stripeHandler.open({
                    name: 'TeeTime Beast',
                    description: this.description,
                    currency: "gbp",
                    allowRememberMe: false,
                    panelLabel: 'Pay {{amount}}',
                    amount: this.totalPrice,
                    image: 'https://d30y9cdsu7xlg0.cloudfront.net/png/139740-200.png',
                    token: this.purchaseTickets,
                })
            },
            purchaseTickets(token) {
                this.processing = true
                axios.post(`/events/${this.eventId}/orders`, {
                    email: token.email,
                    ticket_quantity: this.quantity,
                    payment_token: token.id,
                }).then(response => {
                    window.location = `/orders/${response.data.confirmation_number}`
                }).catch(response => {
                    this.processing = false
                })
            }
        },
        created() {
            this.stripeHandler = this.initStripe()
        }
    }
</script>