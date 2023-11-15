<template>
  <div :class="labelClass">
    {{ divLabel }}
  </div>
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
  },
  data() {
    return {
      password: this.modelValue,
      visible: false,
    };
  },
  computed: {
    passwordRules() {
      return [
        (v) => !!v || "Password is required",
        (v) =>
          /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/.test(
            v
          ) ||
          "Password must be at least 8 characters with a combination of 1 uppercase, 1 lowercase, 1 digit, and 1 special character.",
      ];
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
