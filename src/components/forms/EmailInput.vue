<template>
  <div :class="labelClass">
    {{ divLabel }}
  </div>
  <v-text-field
    v-model="email"
    :label="customLabel"
    :rules="emailRules"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    :append-inner-icon="appendInnerIcon"
    @blur="updateValue"
    ><template v-slot:append>
      <v-tooltip location="bottom">
        <template v-slot:activator="{ props }">
          <v-icon color="error" v-bind="props" icon="mdi-information"></v-icon>
        </template>
        ** VALID and ACTIVE email address is required. Notifications for your
        online application will be sent to this email address. Only one (1)
        application per email address is allowed. **
      </v-tooltip>
    </template></v-text-field
  >
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
      default: "Email",
    },
    customLabel: {
      type: String,
      default: "",
    },
    customPlaceholder: {
      type: String,
      default: "Enter Email Address",
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
    labelClass: {
      type: String,
      default: "text-subtitle-1 text-medium-emphasis",
    },
  },
  data() {
    return {
      email: this.modelValue,
    };
  },
  computed: {
    emailRules() {
      return [
        (v) => !!v || "Email is required",
        (v) =>
          /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(v) ||
          "Invalid email address",
      ];
    },
  },
  watch: {
    modelValue(newVal) {
      this.email = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.email);
    },
  },
};
</script>
