<template>
  <v-text-field
    @click:append-inner="visible = !visible"
    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
    :type="visible ? 'text' : 'password'"
    v-model="password"
    :label="customLabel"
    :rules="passwordRules"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    :persistent-placeholder="customPersistentHint"
    :hint="customHint"
    :error-messages="passwordError"
    @input="updateValue"
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
    divLabel: {
      type: String,
      default: "Password",
    },
    customLabel: {
      type: String,
      default: "",
    },
    customPlaceholder: {
      type: String,
      default: "Enter Password",
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
      default:
        "Password must be at least 8 characters with a combination of 1 uppercase, 1 lowercase, 1 digit, and 1 special character.",
    },
    includeUppercase: {
      type: Boolean,
      default: true,
    },
    includeLowercase: {
      type: Boolean,
      default: true,
    },
    includeDigit: {
      type: Boolean,
      default: true,
    },
    includeSpecialChar: {
      type: Boolean,
      default: true,
    },
    passwordError: {
      type: String,
      default: " ",
    },
  },
  data() {
    return {
      password: this.modelValue,
      visible: false,
    };
  },
  computed: {
    passwordRules() {
      // Define predefined rules
      const rules = [(v) => !!v || "Password is required"];

      // Include additional rules based on props
      if (this.includeUppercase) {
        rules.push(
          (v) => /[A-Z]/.test(v) || "Include at least 1 uppercase character"
        );
      }
      if (this.includeLowercase) {
        rules.push(
          (v) => /[a-z]/.test(v) || "Include at least 1 lowercase character"
        );
      }
      if (this.includeDigit) {
        rules.push((v) => /\d/.test(v) || "Include at least 1 digit");
      }
      if (this.includeSpecialChar) {
        rules.push(
          (v) => /[@$!%*?&]/.test(v) || "Include at least 1 special character"
        );
      }

      return rules;
    },
  },
  watch: {
    modelValue(newVal) {
      this.password = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.password);
    },
  },
};
</script>
