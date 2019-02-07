var config = {
    map: {
        '*': {
            pollSuccess: 'Qliro_QliroOne/js/poll-success'
        }
    },
    config: {
        mixins: {
            "Magento_Checkout/js/view/shipping": {
                "Qliro_QliroOne/js/mixins/shipping": true
            }
        }
    }
};