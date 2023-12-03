<template>
  <v-file-input
    v-model="internalValue"
    :label="customLabel"
    :density="customDensity"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :rules="fileRules"
    :readonly="readonly"
    :append-inner-icon="appendInnerIcon"
    prepend-icon=""
    accept="image/*"
    @input="updateValue"
  ></v-file-input>
</template>

<script>
export default {
  name: "FileInput",
  props: {
    modelValue: {
      type: [Array, File],
      default: () => [],
    },
    divLabel: {
      type: String,
      default: "File Input",
    },
    customLabel: {
      type: String,
      default: "Profile Picture",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    customPlaceholder: {
      type: String,
      default: "Choose a file...",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    readonly: {
      type: Boolean,
      default: false,
    },
    appendInnerIcon: {
      type: String,
      default: "mdi-camera",
    },
  },
  data() {
    return {
      internalValue: this.modelValue,
      fileRules: [(v) => v.length <= 1 || "Select only one file"],
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
