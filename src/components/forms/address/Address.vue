<template>
  <div :class="labelClass">
    {{ divLabel }}
  </div>
  <v-text-field
    v-model="fullAddress"
    :label="customLabel"
    :rules="AddressRules"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    :hint="customHint"
    :readonly="readonly"
    @input="updateValue"
    :append-inner-icon="customAppendInnerIcon"
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
      default: "",
    },
    divLabel: {
      type: String,
      default: "Address",
    },
    customLabel: {
      type: String,
      default: "",
    },
    customPlaceholder: {
      type: String,
      default: "Enter Address",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    labelClass: {
      type: String,
      default: "text-subtitle-1 text-medium-emphasis",
    },
    customHint: {
      type: String,
      default: "Region, Province, City, Brgy, House Number (if applicable)",
    },
    customAppendInnerIcon: {
      type: String,
      default: "mdi-map-marker",
    },
  },
  data() {
    return {
      fullAddress: this.modelValue,
    };
  },
  computed: {
    AddressRules() {
      return [
        (v) => !!v || "Address is required",
        (v) =>
          (v && v.length <= 100) || "Address must be less than 100 characters",
      ];
    },
  },
  watch: {
    modelValue(newVal) {
      this.fullAddress = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.fullAddress);
    },
  },
};
</script>
