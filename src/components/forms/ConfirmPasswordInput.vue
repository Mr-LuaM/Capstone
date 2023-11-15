<!-- ConfirmPasswordInput.vue -->

<template>
  <div :class="labelClass">
    {{ divLabel }}
  </div>
  <v-text-field
    @click:append-inner="visible = !visible"
    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
    :type="visible ? 'text' : 'password'"
    v-model="confirmPassword"
    :label="customLabel"
    :rules="confirmPasswordRules"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    :persistent-placeholder="customPersistentHint"
    :hint="customHint"
    @blur="updateValue"
  >
  </v-text-field>
</template>

<script>
export default {
  props: {
    modelValue: {
      type: String,
      default: "",
    },
    password: {
      type: String,
      default: "",
    },
    divLabel: {
      type: String,
      default: "Confirm Password",
    },
    customLabel: {
      type: String,
      default: "",
    },
    customPlaceholder: {
      type: String,
      default: "Confirm Password",
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
    customPersistentHint: {
      type: Boolean,
      default: true,
    },
    customHint: {
      type: String,
      default: "Please confirm your password.",
    },
  },
  data() {
    return {
      confirmPassword: this.modelValue,
      visible: false,
    };
  },
  computed: {
    confirmPasswordRules() {
      return [
        (v) => !!v || "Confirm Password is required",
        (v) => v === this.password || "Passwords do not match.",
      ];
    },
  },
  watch: {
    modelValue(newVal) {
      this.confirmPassword = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.confirmPassword);
    },
  },
};
</script>
