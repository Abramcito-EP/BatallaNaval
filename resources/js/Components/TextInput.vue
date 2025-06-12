<template>
    <input
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>

<script>
export default {
    name: 'TextInput',
    props: {
        modelValue: {
            type: String,
            required: true,
        },
    },
    emits: ['update:modelValue'],
    mounted() {
        if (this.$refs.input && this.$refs.input.hasAttribute('autofocus')) {
            this.$refs.input.focus();
        }
    },
    methods: {
        focus() {
            this.$refs.input.focus();
        },
    },
};
</script>