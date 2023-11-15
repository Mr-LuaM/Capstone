<template>
  <div>
    <label :class="labelClass" :style="labelStyle">
      {{ divLabel }}
    </label>
  </div>
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
    divLabel: {
      type: String,
      default: "Date Label",
    },
    labelStyle: {
      type: Object,
      default: () => ({}),
    },
    labelClass: {
      type: String,
      default: "text-subtitle-1 text-medium-emphasis",
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
      // Define minimum accepted date
      type: String,
      default: "1900-01-01",
    },
    maxDate: {
      // Define maximum accepted date
      type: String,
      default: "2100-12-31",
    },
  },
  data() {
    return {
      internalValue: this.modelValue,
    };
  },
  computed: {
    dateRules() {
      return [
        (v) => !!v || "Date is required",
        (v) =>
          (new Date(v) >= new Date(this.minDate) &&
            new Date(v) <= new Date(this.maxDate)) ||
          "Date must be between " + this.minDate + " and " + this.maxDate,
      ];
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
