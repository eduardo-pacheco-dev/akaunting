<template>
    <div class="relative bg-body z-10 rounded-lg shadow-2xl p-5 sm:p-10 full-height-mobile overflow-hidden">
        <WizardSteps :active_state="active"></WizardSteps>

        <div class="flex flex-col items-center justify-center text-center" style="height:523px;">
            <span class="material-icons text-8xl text-green">check_circle</span>

            <h2 class="text-2xl font-medium mt-6">
                {{ translations.finish.title }}
            </h2>

            <p class="text-black-300 mt-3 max-w-lg">
                {{ translations.finish.ready_message }}
            </p>

            <base-button
                class="flex items-center justify-center text-base rounded-lg mt-10 bg-green hover:bg-green-700 text-white py-2 px-7 font-medium disabled:bg-gray-100"
                :disabled="anchor_loading"
                @click="finish()"
            >
                {{ translations.finish.create_first_invoice }}
            </base-button>
        </div>
    </div>
</template>

<script>
import WizardSteps from "./Steps.vue";

export default {
    name: "Finish",

    components: {
        WizardSteps
    },

    props: {
        translations: {
            type: [Object, Array],
        },
    },

    data() {
        return {
            active: 2,
            anchor_loading: false
        };
    },

    created() {
        window.axios({
            method: "PATCH",
            url: url + "/wizard/finish",
        })
        .then((response) => {
        })
        .catch((error) => {
            this.$notify({
                verticalAlign: 'bottom',
                horizontalAlign: 'left',
                message: this.translations.finish.error_message,
                timeout: 1000,
                icon: "",
                type: 0
            });

            this.prev();
        });
    },

    methods: {
        prev() {
            this.$router.push("/wizard/currencies");
        },

        finish() {
            this.anchor_loading = true;

            window.location.href = url + "/sales/invoices/create";
        },
    },
};
</script>
