<!-- GenericAutocomplete.vue -->
<template>
  <v-autocomplete
    v-model="selectedValue"
    :items="options"
    :rules="autocompleteRules"
    :label="customLabel"
    :variant="customVariant"
    :density="customDensity"
    :placeholder="customPlaceholder"
    @blur="updateValue"
  ></v-autocomplete>
</template>

<script>
export default {
  props: {
    modelValue: {
      type: [String, Number],
      default: null,
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
    customPlaceholder: {
      type: String,
      default: "Select",
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
