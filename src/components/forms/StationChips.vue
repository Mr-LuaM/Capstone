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
    
        @blur="updateValue"
        multiple
        closable-chips
        chips
      >
        
      </v-autocomplete>
    </div>
  </template>
  
  <script>
  import { getStations } from "../../services/BackendApi.js";
  
  export default {
    props: {
      modelValue: {
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
      stationAdminId: {
        type: String,
        default: "",
      },
    },
    data() {
      return {
        selectedStation: this.modelValue,
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
        console.log(this.selectedStation);
      },
      toggleEdit() {
        this.isEditing = !this.isEditing;
        if (!this.isEditing) {
          this.$emit("stationSaved", {
            station: this.selectedStation,
            stationAdminId: this.stationAdminId,
          });
        }
      },
    },
  };
  </script>
  