<!-- GenericAutocomplete.vue -->
<template>
  <v-autocomplete
    v-model="selectedValue"
    :items="options"
    :rules="autocompleteRules"
    :label="customLabel"
    :variant="customVariant"
    :density="customDensity"
    @blur="updateValue"
  ></v-autocomplete>
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
      default: "Select an option*",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    options: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      selectedValue: this.modelValue,
      autocompleteRules: [(v) => !!v || "Selection is required"],
    };
  },
  watch: {
    modelValue(newVal) {
      this.selectedValue = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.selectedValue);
    },
  },
};
</script>
