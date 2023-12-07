<template>
  <div class="text-subtitle-1 text-medium-emphasis">{{ divLabel }}</div>
  <v-text-field
    v-model="internalValue"
    :rules="nameRules"
    :label="customLabel"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    :readonly="readonly"
    @input="updateValue"
  >
    <template v-slot:append>
      <v-tooltip location="bottom">
        <template v-slot:activator="{ props }">
          <v-icon v-bind="props" icon="mdi-information"></v-icon>
        </template>
        Leave empty if no middle name. In case with middle name, kindly fill
        this out properly. Avoid using a middle initial.
      </v-tooltip>
    </template>
  </v-text-field>
</template>

<script>
export default {
  name: "MiddleNameInput",
  props: {
    readonly: {
      type: Boolean,
      default: false,
    },
    modelValue: {
      type: String,
      default: "Default Value",
    },
    divLabel: {
      type: String,
      default: "Middle Name",
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
      default: "Enter Middle Name",
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
  (v) =>
    (v === null || /^[A-Za-z\s]*$/.test(v)) ||
    "Only letters and spaces are allowed", // Allow letters and spaces
  (v) =>
    (!v || v.length <= 50) ||
    "Middle name should not exceed 50 characters", // Limit the length
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
