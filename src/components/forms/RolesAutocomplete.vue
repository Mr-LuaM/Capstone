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
      :readonly="!isEditing"
      @blur="updateValue"
    >
      <template v-slot:append>
        <v-slide-x-reverse-transition mode="out-in">
          <v-icon
            :key="`icon-${isEditing}`"
            :color="isEditing ? 'success' : 'info'"
            :size="isEditing ? '18' : '16'"
            @click="isEditing = !isEditing"
          >
            {{ isEditing ? "mdi-content-save" : "mdi-circle-edit-outline" }}
          </v-icon>
        </v-slide-x-reverse-transition>
      </template>
    </v-autocomplete>

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
      default: "",
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
      isEditing: false,
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
        // Fetch roles from your backend, passing the user role from the store
        const userRole = this.$store.state.role; // Replace with your actual store path
        this.roles = await getRoles(userRole);
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
