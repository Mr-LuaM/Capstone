
<template>
    <div>
      <v-autocomplete
        v-model="selectedItem"
        :items="courses"
        :item-title="item => item.Course_Name"
        :item-value="item => item.Course_ID"
        :rules="selectRules"
        :label="customLabel"
        :variant="customVariant"
        :placeholder="customPlaceholder"
        :density="customDensity"
        @blur="updateValue"
      ></v-autocomplete>
  
      <!-- for debugging -->
      <!-- <p>{{ selectedItem }}</p> -->
    </div>
  </template>
  
  <script>
  import { getCourse } from "../../services/BackendApi.js";
  
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
    mounted() {
      this.getCourse();
      
    },
    watch: {
      modelValue(newVal) {
        // Update the selectedItem when the modelValue changes
        this.selectedItem = newVal;
        // this.updateIdFromName(newVal);
      },
    },
//     created() {
 
//     this.updateValue(); // Call the updateValue method after fetching the course
//   },
    methods: {
      async getCourse() {
        try {
          this.courses = await getCourse();
          // If modelValue is provided initially, update the ID
        //   if (this.modelValue) {
        //     this.updateIdFromName(this.modelValue);
        //   }
        } catch (error) {
          console.error("Failed to fetch courses:", error);
        }
      },
    //   async updateIdFromName(name) {
    //     // If the name is provided, fetch the corresponding ID
    //     if (name) {
    //       const course = this.courses.find((item) => item.Course_Name === name);
    //       if (course) {
    //         this.selectedItem = course.Course_ID;
    //         this.$emit("update:modelId", course.Course_ID);
    //       }
    //     }
    //   },
      updateValue() {
        this.$emit("update:modelValue", this.selectedItem);
      },
    },
  };
  </script>