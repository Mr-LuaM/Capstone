<template>
  <div :class="labelClass">
    {{ divLabel }}
  </div>
  <v-text-field
    type="number"
    v-model="height"
    :rules="heightRules"
    :variant="customVariant"
    :density="customDensity"
    :label="customLabel"
    :placeholder="customPlaceholder"
    :suffix="customSuffix"
    @blur="updateValue"
  ></v-text-field>
</template>

<script>
export default {
  props: {
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
      default: "Enter Height",
    },
    customSuffix: {
      type: String,
      default: "cm",
    },
  },
  data() {
    return {
      height: this.modelValue,
      heightRules: [
        (v) => !!v || "Height is required",
        (v) => !isNaN(v) || "Height must be a number",
        (v) => /^\d{3}$/.test(v) || "Enter a valid height (e.g., 150)",
      ],
    };
  },
  watch: {
    modelValue(newVal) {
      this.height = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.height);
    },
  },
};
</script>
