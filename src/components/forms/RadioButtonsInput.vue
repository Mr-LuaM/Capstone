<template>
  <div>
    <label :class="labelClass" :style="labelStyle">
      {{ labelText }}
    </label>
    <v-radio-group
      v-model="internalValue"
      :color="color"
      :inline="inline"
      @input="updateSelectedValue"
      :rules="validationRules"
      :readonly="readonly"
    >
      <v-radio
        v-for="(option, index) in options"
        :key="index"
        :label="option"
        :value="option"
      ></v-radio>
    </v-radio-group>
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
      // Rename value to modelValue
      type: String,
      required: true,
    },
    labelText: {
      type: String,
      default: "Radio Label",
    },
    labelStyle: {
      type: Object,
      default: () => ({}),
    },
    labelClass: {
      type: String,
      default: "text-subtitle-1 text-medium-emphasis",
    },
    color: {
      type: String,
      default: "primary",
    },
    options: {
      type: Array,
      default: () => [],
    },
    inline: {
      type: Boolean,
      default: false,
    },
    validationRules: {
      type: Array,
      default: () => [(v) => !!v || "This field is required"],
    },
  },
  data() {
    return {
      internalValue: this.modelValue, // Update to use modelValue
    };
  },
  watch: {
    modelValue(newVal) {
      // Update to use modelValue
      this.internalValue = newVal;
    },
  },
  methods: {
    updateSelectedValue() {
      this.$emit("update:modelValue", this.internalValue); // Update to use update:modelValue
    },
  },
};
</script>
