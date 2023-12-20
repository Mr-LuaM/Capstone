<template>
  <v-text-field
    type="time"
    :label="labelText"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    v-model="internalValue"
    @input="updateSelectedTime"
    :rules="timeRules"
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
      default: "Select time",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    customPlaceholder: {
      type: String,
      default: "HH:mm",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    customPersistentHint: {
      type: String,
      default: "HH:mm (24-hour format)",
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
    timeRules() {
      return [
        (v) => !!v || "Time is required",
        (v) =>
          /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/.test(v) || "Invalid time format",
      ];
    },
  },
  watch: {
    modelValue(newVal) {
      this.internalValue = newVal;
    },
  },
  methods: {
    updateSelectedTime() {
      this.$emit("update:modelValue", this.internalValue);
    },
  },
};
</script>
