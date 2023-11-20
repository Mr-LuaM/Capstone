<template>
  <v-text-field
    ref="emailField"
    v-model="email"
    :label="customLabel"
    :rules="computedEmailRules"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    :append-inner-icon="appendInnerIcon"
    @input="updateValue"
  >
    <template v-if="showTooltip" v-slot:append>
      <v-tooltip location="bottom">
        <template v-slot:activator="{ props }">
          <v-icon color="error" v-bind="props" icon="mdi-information"></v-icon>
        </template>
        ** VALID and ACTIVE email address is required. Notifications for your
        online application will be sent to this email address. Only one (1)
        application per email address is allowed. **
      </v-tooltip>
    </template>
  </v-text-field>
</template>

<script>
export default {
  props: {
    modelValue: {
      type: String,
    },
    showTooltip: {
      type: Boolean,
      default: true,
    },
    divLabel: {
      type: String,
      default: "Email",
    },
    customLabel: {
      type: String,
    },
    customPlaceholder: {
      type: String,
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    customVariant: {
      type: String,
      default: "outlined",
    },
    appendInnerIcon: {
      type: String,
      default: "mdi-email",
    },
    emailRules: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      email: this.modelValue || "",
    };
  },
  computed: {
    computedEmailRules() {
      // Include default rules and any additional rules passed via props
      return [
        ...this.emailRules,
        (v) => !!v || "Email is required",
        (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) || "Invalid email address",
      ];
    },
  },
  watch: {
    modelValue(newVal) {
      this.email = newVal || "";
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.email);
    },
  },
};
</script>
