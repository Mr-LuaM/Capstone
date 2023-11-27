<template>
    <v-autocomplete
      v-model="selectedItem"
      :items="courses"
      :item-text="item => item.Course_Name"
      :value="item => item.Course_ID"
      :rules="selectRules"
      :label="customLabel"
      :variant="customVariant"
      :placeholder="customPlaceholder"
      :density="customDensity"
      @blur="updateValue"
    ></v-autocomplete>
    <p v-for="course in courses"> {{ courses.Course_ID }}</p>
  </template>
  
  <script>
  import axios from "axios";
  import { getCourse } from "../../services/BackendApi.js";
  
  export default {
    props: {
      modelValue: {
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
        default: "Select Course",
      },
      customDensity: {
        type: String,
        default: "compact",
      },
    },
    data() {
      return {
        selectedItem: this.modelValue,
        selectRules: [(v) => !!v || "Course is required"],
        courses: [],
      };
    },
    created() {
      this.getCourse();
    },
    watch: {
      modelValue(newVal) {
        this.selectedItem = newVal;
      },
    },
    methods: {
      async getCourse() {
        try {
          this.courses = await getCourse();
        } catch (error) {
          console.error("Failed to fetch courses:", error);
        }
      },
      updateValue() {
        this.$emit("update:modelValue", this.selectedItem);
      },
    },
  };
  </script>
  