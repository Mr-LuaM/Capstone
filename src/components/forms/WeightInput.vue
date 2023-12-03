<template>
  <div :class="labelClass">
    {{ divLabel }}
  </div>
  <v-text-field
    type="number"
    v-model="weight"
    :rules="weightRules"
    :variant="customVariant"
    :density="customDensity"
    :label="customLabel"
    :placeholder="customPlaceholder"
    :suffix="customSuffix"
    @blur="updateValue"
    :readonly="readonly"
  ></v-text-field>
</template>

<script>
export default {
  props: {
    readonly: {
      type: Boolean,
      default: false,
    },
    modelValue: {
      type: String,
      default: "", // You can set a default value if needed
    },
    divLabel: {
      type: String,
      default: "",
    },
    customLabel: {
      type: String,
      default: "",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    labelClass: {
      type: String,
      default: "text-subtitle-1 text-medium-emphasis",
    },
    customPlaceholder: {
      type: String,
      default: "Enter Weight",
    },
    customSuffix: {
      type: String,
      default: "kg",
    },
  },
  data() {
    return {
      weight: this.modelValue,
      weightRules: [
        (v) => !!v || "Weight is required",
        (v) => !isNaN(v) || "Weight must be a number",
        (v) =>
          /^\d{1,3}(\.\d{1,2})?$/.test(v) ||
          "Enter a valid weight (e.g., 75 or 75.50)",
      ],
    };
  },
  watch: {
    modelValue(newVal) {
      this.weight = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.weight);
    },
  },
};
</script>
