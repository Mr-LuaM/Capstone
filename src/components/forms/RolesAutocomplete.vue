<template>
  <div>
    <v-autocomplete
      v-model="selectedRole"
      :items="roles"
      :item-title="(item) => item.Role_Name"
      :item-value="(item) => item.Role_ID"
      :rules="selectRules"
      :label="customLabel"
      :variant="customVariant"
      :placeholder="customPlaceholder"
      :density="customDensity"
      @blur="updateValue"
    ></v-autocomplete>

    <!-- for debugging -->
    <!-- <p>{{ selectedRole }}</p> -->
  </div>
</template>

<script>
import { getRoles } from "../../services/BackendApi.js";

export default {
  props: {
    modelValue: {
      type: String,
      default: null,
    },
    modelId: {
      type: String,
      default: "",
    },
    divLabel: {
      type: String,
      default: "",
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
      default: "Select Role",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
  },
  data() {
    return {
      selectedRole: this.modelValue,
      selectRules: [(v) => !!v || "Role is required"],
      roles: [],
    };
  },
  mounted() {
    this.getRoles();
  },
  watch: {
    modelValue(newVal) {
      // Update the selectedRole when the modelValue changes
      this.selectedRole = newVal;
    },
  },
  methods: {
    async getRoles() {
      try {
        // Fetch roles from your backend
        this.roles = await getRoles();
      } catch (error) {
        console.error("Failed to fetch roles:", error);
      }
    },
    updateValue() {
      // Emit the selected role value to the parent component
      this.$emit("update:modelValue", this.selectedRole);
    },
  },
};
</script>
