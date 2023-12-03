<template>
  <div :class="labelClass">
    {{ divLabel }}
  </div>
  <v-autocomplete
    v-model="selectedItem"
    :items="nationalities"
    :item-text="(item) => item"
    :rules="selectRules"
    :label="customLabel"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    :readonly="readonly"
    s
    @blur="updateValue"
  ></v-autocomplete>
</template>

<script>
import axios from "axios";

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
    customPlaceholder: {
      type: String,
      default: "Select a nationality",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    labelClass: {
      type: String,
      default: "text-subtitle-1 text-medium-emphasis",
    },
  },
  data() {
    return {
      selectedItem: this.modelValue, // Use selectedItem for v-model
      selectRules: [(v) => !!v || `Nationality is required`],
      nationalities: ["hi", "hello"], // Initialize as an empty array
    };
  },
  created() {
    // Fetch the list of countries from the REST Countries API
    axios
      .get("https://restcountries.com/v3.1/all")
      .then((response) => {
        this.nationalities = response.data.map(
          (country) => country.name.common
        );
      })
      .catch((error) => {
        console.error("Failed to fetch countries:", error);
      });
  },
  watch: {
    modelValue(newVal) {
      this.selectedItem = newVal; // Update selectedItem when modelValue changes
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.selectedItem); // Emit the updated selectedItem
    },
  },
};
</script>
