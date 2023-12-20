<template>
  <v-text-field
    type="date"
    :label="labelText"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    v-model="internalValue"
    @input="updateSelectedDate"
    :rules="dateRules"
    :persistent-hint="customPersistentHint"
    :readonly="readonly"
  ></v-text-field>
</template>

<script>
export default {
  props: {
    modelValue: {
      type: String,
      required: true,
    },
    labelText: {
      type: String,
      default: "",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    customPlaceholder: {
      type: String,
      default: "Select a date",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    customPersistentHint: {
      type: String,
      default: "dd/mm/yyyy",
    },
    minDate: {
      type: String,
      default: "1900-01-01",
    },
    maxDate: {
      type: String,
      default: "2100-12-31",
    },
    readonly: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      internalValue: this.modelValue,
    };
  },
  computed: {
    dateRules() {
      return [(v) => !!v || "Date is required"];
    },
  },
  watch: {
    modelValue(newVal) {
      this.internalValue = newVal;
    },
  },
  methods: {
    updateSelectedDate() {
      this.$emit("update:modelValue", this.internalValue);
    },
  },
};
</script>
