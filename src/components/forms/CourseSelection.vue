<template>
  <div>
    <v-autocomplete
      v-model="selectedCourse"
      :items="courses"
      :item-title="(item) => item.Course_Name"
      :item-value="(item) => item.Course_ID"
      :rules="selectRules"
      :label="customLabel"
      :variant="customVariant"
      :placeholder="customPlaceholder"
      :density="customDensity"
      @blur="updateValue"
    ></v-autocomplete>
  </div>
</template>

<script>
import { getCoursesByStation } from "../../services/BackendApi.js";

export default {
  props: {
    modelValue: {
      type: String,
      default: "",
    },
    stationId: {
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
      default: "Select Course",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
  },
  data() {
    return {
      selectedCourse: this.modelValue,
      selectRules: [(v) => !!v || "Course is required"],
      courses: [],
    };
  },
  watch: {
    stationId(newStationId) {
      this.getCourses(newStationId);
    },
  },
  mounted() {
    // Fetch courses initially
    this.getCourses(this.stationId);
  },
  methods: {
    async getCourses(stationId) {
      try {
        if (stationId) {
          // Fetch courses based on the selected station
          this.courses = await getCoursesByStation(stationId);
        } else {
          // No station selected, clear the course list
          this.courses = [];
        }
      } catch (error) {
        console.error("Failed to fetch courses:", error);
      }
    },
    updateValue() {
      this.$emit("update:modelValue", this.selectedCourse);
    },
  },
};
</script>
