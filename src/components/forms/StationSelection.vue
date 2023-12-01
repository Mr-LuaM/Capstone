<template>
  <div>
    <v-autocomplete
      v-model="selectedStation"
      :items="stations"
      :item-title="(item) => item.Station_Name"
      :item-value="(item) => item.Station_ID"
      :rules="selectRules"
      :label="customLabel"
      :variant="customVariant"
      :placeholder="customPlaceholder"
      :density="customDensity"
      :readonly="!isEditing"
    >
      @blur="updateValue" >
      <template v-slot:append>
        <v-slide-x-reverse-transition mode="out-in" v-if="showAppend">
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
  </div>
</template>

<script>
import { getStations } from "../../services/BackendApi.js";

export default {
  props: {
    modelValue: {
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
      default: "Select Station",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    showAppend: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      selectedStation: this.modelValue,
      isEditing: false,
      selectRules: [(v) => !!v || "Station is required"],
      stations: [],
    };
  },
  mounted() {
    this.getStations();
  },
  watch: {
    modelValue(newVal) {
      this.selectedStation = newVal;
    },
  },
  methods: {
    async getStations() {
      try {
        this.stations = await getStations();
      } catch (error) {
        console.error("Failed to fetch stations:", error);
      }
    },
    updateValue() {
      this.$emit("update:modelValue", this.selectedStation);
    },
  },
};
</script>
