<template>
  <div>
    <div class="text-subtitle-1 text-medium-emphasis">{{ divLabel }}</div>
    <v-text-field
      v-model="mobileNumber"
      :label="customLabel"
      :rules="mobileNumberRules"
      :variant="customVariant"
      :placeholder="customPlaceholder"
      :density="customDensity"
      append-inner-icon="mdi-cellphone"
      :persistent-hint="customPersistentHint"
      :readonly="readonly"
      :hint="customHint"
      @input="updateValue"
    ></v-text-field>
  </div>
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
      default: "Mobile Number",
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
      default: "Enter Mobile Number",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    customPersistentHint: {
      type: Boolean,
      default: true,
    },
    customHint: {
      type: String,
      default: "Example: 9123456789",
    },
  },
  data() {
    return {
      mobileNumber: this.modelValue,
      mobileNumberRules: [
        (v) => !!v || "Mobile number is required",
        (v) =>
          /^(\+\d{1,3})?(\d{10})$/.test(v) ||
          "Please Enter your 10 digit mobile number",
      ],
    };
  },
  watch: {
    modelValue(newVal) {
      this.mobileNumber = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.mobileNumber);
    },
  },
};
</script>
