<template>
  <v-container class="rounded-lg pa-4">
    <v-row>
      <!-- Add Exam Card -->
      <v-col cols="12" md="2">
        <v-card
          variant="outlined"
          class="bg-surface d-flex justify-center align-center"
        >
          <v-btn
            block
            rounded-0
            variant="plain"
            @click="goToManageExams"
            class="d-flex justify-center align-center"
            style="min-width: 100px; min-height: 200px"
          >
            <v-icon color="primary" size="x-large">mdi-plus</v-icon>
          </v-btn>
        </v-card>
      </v-col>

      <!-- Dynamic Exam Cards -->
      <v-col v-for="(exam, index) in exams" :key="index" cols="12" md="2">
        <v-card variant="outlined" class="bg-surface">
          <v-card-title class="bg-secondary">{{
            exam.Exam_Title
          }}</v-card-title>
          <v-card-subtitle class="pt-3">
            Duration: {{ exam.Duration_Minutes }} minutes
          </v-card-subtitle>
          <v-card-text>
            Start: {{ exam.Start_Time }}<br />
            End: {{ exam.End_Time }}
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="secondary" @click="editExam(exam.Exam_ID)"
              >Edit</v-btn
            >
            <!-- <v-btn color="primary" @click="deleteExam(exam.Exam_ID)"
              >Delete</v-btn
            > -->
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      exams: [], // Will hold the exam data
    };
  },
  created() {
    this.fetchExams();
  },
  methods: {
    async fetchExams() {
      try {
        // Retrieve the user ID from the Vuex store
        const userId = this.$store.state.userId; // Adjust this path according to your store structure

        // Send a POST request with the user ID
        const response = await axios.post("/getExams", { userId });

        // Handle the response
        this.exams = response.data; // Assuming the response has an exams array
      } catch (error) {
        console.error("Error fetching exams:", error);
        // Handle error (e.g., show a notification)
      }
    },
    editExam(examId) {
      const encodedId = btoa(examId.toString()); // Base64 encode the ID
      this.$router.push({ name: "teditexams", params: { id: encodedId } });
    },
    goToManageExams() {
      this.$router.push({ name: "tmanageexams" }); // Use the route name or path
    },
    // ... other methods ...
  },
};
</script>
