<template>
  <div>
    <v-autocomplete
      v-model="selectedTeacher"
      :items="teachers"
      :item-title="(teacher) => `${teacher.First_Name} ${teacher.Last_Name}`"
      :item-value="(teacher) => teacher.Teacher_ID"
      :rules="selectRules"
      :label="customLabel"
      :variant="customVariant"
      :placeholder="customPlaceholder"
      :density="customDensity"
      @blur="updateValue"
    ></v-autocomplete>

    <!-- for debugging -->
    <!-- <p>{{ selectedTeacher }}</p> -->
  </div>
</template>

<script>
import axios from "axios"; // Import axios for making HTTP requests

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
      default: "Select Teacher",
    },
    customDensity: {
      type: String,
      default: "compact",
    },
  },
  data() {
    return {
      selectedTeacher: this.modelValue,
      selectRules: [(v) => !!v || "Teacher is required"],
      teachers: [],
    };
  },
  mounted() {
    this.getTeacherAssignmentsDetails();
  },
  watch: {
    modelValue(newVal) {
      // Update the selectedTeacher when the modelValue changes
      this.selectedTeacher = newVal;
    },
  },
  methods: {
    async getTeacherAssignmentsDetails() {
      try {
        const response = await axios.post(
          "/getTeacherAssignmentsDetailsperStation",
          {
            station_id: this.$store.state.stationId,
          }
        );

        this.teachers = response.data;

        console.log("Teachers loaded successfully:", this.teachers);
      } catch (error) {
        console.error("Failed to fetch teachers:", error);
      }
    },
    updateValue() {
      this.$emit("update:modelValue", this.selectedTeacher);
    },
  },
};
</script>
