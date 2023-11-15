<template>
  <v-autocomplete
    v-model="selectedItem"
    :items="choices"
    :label="customLabel"
    :rules="[(v) => !!v || 'This field is required']"
    :variant="customVariant"
    :placeholder="computedPlaceholder"
    :density="customDensity"
    @change="updateValue"
  ></v-autocomplete>
</template>

<script>
export default {
  props: {
    modelValue: {
      type: String,
      default: "",
    },
    choices: Array,
    customLabel: {
      type: String,
      default: "",
    },
    divLabel: {
      type: String,
      default: "",
    },
    labelClass: {
      type: String,
      default: "text-subtitle-1 text-medium-emphasis",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
  },
  data() {
    return {
      selectedItem: this.modelValue,
    };
  },
  computed: {
    computedPlaceholder() {
      return `Select a ${this.divLabel}`;
    },
  },
  watch: {
    selectedItem(newItem, oldItem) {
      if (newItem !== oldItem) {
        this.$emit("item-selected", newItem);
      }
    },
    modelValue(newVal) {
      this.selectedItem = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.selectedItem);
    },
  },
};
</script>
