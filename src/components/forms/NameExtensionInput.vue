<template>
  <div class="text-subtitle-1 text-medium-emphasis">{{ divLabel }}</div>
  <v-text-field
    v-model="internalValue"
    :rules="extensionRules"
    :label="customLabel"
    :variant="customVariant"
    :placeholder="customPlaceholder"
    :density="customDensity"
    :persistent-hint="customPersistentHint"
    :hint="customHint"
    :readonly="readonly"
    @input="updateValue"
    @click:append-inner="visible = !visible"
  >
    <template v-slot:append>
      <v-tooltip location="bottom">
        <template v-slot:activator="{ props }">
          <v-icon v-bind="props" icon="mdi-information"></v-icon>
        </template>
        Leave empty if no name extension
      </v-tooltip>
    </template>
  </v-text-field>
</template>

<script>
export default {
  name: "NameExtensionInput",
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
      default: "Name Extension (Optional)",
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
      default: "Enter Name Extension",
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
      default: "Example: Jr., I, II, III",
    },
  },
  data() {
    return {
      internalValue: this.modelValue,
      extensionRules: [
        (v) =>
          /^[A-Za-z\s\.,]*$/.test(v) ||
          "Only letters, spaces, and periods are allowed",
        (v) =>
          v.length <= 50 || "Name extension should not exceed 50 characters",
      ],
    };
  },
  watch: {
    modelValue(newVal) {
      this.internalValue = newVal;
    },
  },
  methods: {
    updateValue() {
      this.$emit("update:modelValue", this.internalValue);
    },
  },
};
</script>
