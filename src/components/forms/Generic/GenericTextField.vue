<!-- GenericTextField.vue -->
<template>
    <v-text-field
      :label="customLabel"
      :required="isRequired"
      v-model="internalValue"
      :variant="customVariant"
      :density="customDensity"
      :rules="textRules"
      @blur="updateValue"
    ></v-text-field>
  </template>
  
  <script>
  export default {
    props: {
      modelValue: {
        type: [String, Number],
        default: "",
      },
      customLabel: {
        type: String,
        default: "Enter a value*",
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
        internalValue: this.modelValue,
      };
    },
    computed: {
      textRules() {
        const rules = [];
        if (this.isRequired) {
          rules.push((v) => !!v || "This field is required");
        }
        // Add more rules as needed
        return rules;
      },
    },
    watch: {
      modelValue(newVal) {
        this.internalValue = newVal;
      },
      internalValue(newVal) {
        this.$emit("update:modelValue", newVal);
      },
    },
    methods: {
      updateValue() {
        this.$emit("update:modelValue", this.internalValue);
      },
    },
  };
  </script>
  