<!-- GenericNumberArea.vue -->
<template>
  <v-text-field
    :label="customLabel"
    :required="isRequired"
    v-model="internalValue"
    :variant="customVariant"
    :density="customDensity"
    :rules="numberRules"
    @blur="onInput"
  ></v-text-field>
</template>

<script>
export default {
  props: {
    modelValue: {
      type: Number,
      default: null, // Set default value for numbers
    },
    customLabel: {
      type: String,
      default: "Enter a number*",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    isRequired: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      internalValue: this.modelValue !== null ? this.modelValue.toString() : "",
    };
  },
  computed: {
    numberRules() {
      const rules = [];
      if (this.isRequired) {
        rules.push((v) => !!v || "This field is required");
      }
      // Add more rules as needed
      rules.push((v) => /^\d*$/.test(v) || "Only numbers are allowed");
      return rules;
    },
  },
  watch: {
    modelValue(newVal) {
      this.internalValue = newVal !== null ? newVal.toString() : "";
    },
    internalValue(newVal) {
      const numericValue = /^\d*$/.test(newVal) ? parseInt(newVal, 10) : null;
      this.$emit("update:modelValue", numericValue);
    },
  },
  methods: {
    onInput() {
      // Additional logic if needed on input
    },
  },
};
</script>
