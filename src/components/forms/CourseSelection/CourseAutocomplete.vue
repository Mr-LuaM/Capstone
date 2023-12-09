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
      :readonly="!isEditing"
      @blur="updateValue"
    >
      <template v-slot:append>
        <v-slide-x-reverse-transition mode="out-in" v-if="showAppend">
          <v-icon
            :key="`icon-${isEditing}`"
            :color="isEditing ? 'success' : 'info'"
            :size="isEditing ? '18' : '16'"
            @click="toggleEdit"
          >
            {{ isEditing ? "mdi-content-save" : "mdi-circle-edit-outline" }}
          </v-icon>
        </v-slide-x-reverse-transition>
      </template>
    </v-autocomplete>
  </div>
</template>

<script>
import { getCoursesInStation } from "../../../services/BackendApi.js";

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
      default: "Select Course",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
    showAppend: {
      type: Boolean,
      default: true,
    },
    stationId: {
      type: String,
      required: true,
    },
    teacherId: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      selectedCourse: this.modelValue,
      isEditing: !this.showAppend,
      selectRules: [(v) => !!v || "Course is required"],
      courses: [],
    };
  },
  mounted() {
    this.getCourses();
  },
  watch: {
    modelValue(newVal) {
      this.selectedCourse = newVal;
    },
    stationId(newStationId) {
      this.getCourses(newStationId);
    },
  },
  methods: {
    async getCourses(stationId) {
      try {
        this.courses = await getCoursesInStation(stationId || this.stationId);
      } catch (error) {
        console.error("Failed to fetch courses:", error);
      }
    },
    updateValue() {
      this.$emit("update:modelValue", this.selectedCourse);
      console.log(this.selectedCourse);
    },
    toggleEdit() {
      this.isEditing = !this.isEditing;
      if (!this.isEditing) {
        this.$emit("courseSaved", {
          course: this.selectedCourse,
          stationId: this.stationId,
          teacherId: this.teacherId, // Include Teacher_ID in the emitted data
        });
      }
    },
  },
};
</script>
