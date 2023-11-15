<template>
  <div class="text-subtitle-1 text-medium-emphasis">{{ divLabel }}</div>
  <v-text-field
    v-model="internalValue"
    :rules="nameRules"
    :label="customLabel"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    @input="updateValue"
  ></v-text-field>
</template>

<script>
export default {
  name: "FirstNameInput",
  props: {
    modelValue: {
      type: String,
      default: "Default Value",
    },
    divLabel: {
      type: String,
      default: "Last Name",
    },
    customLabel: {
      type: String,
      default: "",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    customPlaceholder: {
      type: String,
      default: "Enter First Name",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
  },
  data() {
    return {
      internalValue: this.modelValue,
      nameRules: [
        (v) => !!v || "First name is required",
        (v) =>
          (v && v.length >= 2) || "First name should be at least 2 characters",
        (v) => v.length <= 50 || "First name should not exceed 50 characters", // Limit the length
      ],
    };
  },
  watch: {
    modelValue(newVal) {
      this.internalValue = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.internalValue);
    },
  },
};
</script>
