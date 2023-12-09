<!-- GradeNumberArea.vue -->
<template>
  <v-text-field
    :label="customLabel"
    :required="isRequired"
    v-model="internalValue"
    :variant="customVariant"
    :density="customDensity"
    :rules="gradeRules"
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
      default: "Enter a grade (max 99)*",
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
    gradeRules() {
      const rules = [];
      if (this.isRequired) {
        rules.push((v) => !!v || "This field is required");
      }
      // Add more rules as needed
      rules.push(
        (v) => /^\d*\.?\d*$/.test(v) || "Only numbers are allowed",
        (v) =>
          v === null || (v >= 0 && v <= 99) || "Grade must be between 0 and 99"
      );
      return rules;
    },
  },
  watch: {
    modelValue(newVal) {
      this.internalValue = newVal !== null ? newVal.toString() : "";
    },
    internalValue(newVal) {
      const numericValue = /^\d*\.?\d*$/.test(newVal)
        ? parseFloat(newVal)
        : null;
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
