<template>
  <v-container class="rounded-lg pa-4">
    <v-row>
      <!-- Dynamic Exam Cards -->
      <v-col v-for="(exam, index) in exams" :key="index" cols="12" md="2">
        <v-card variant="outlined" class="bg-surface">
          <v-card-title :class="getTitleClasses(exam)">
            {{ exam.Exam_Title }}
          </v-card-title>
          <v-card-subtitle class="pt-3">
            Duration: {{ exam.Duration_Minutes }} minutes
          </v-card-subtitle>
          <v-card-text>
            Start: {{ exam.Start_Time }}<br />
            End: {{ exam.End_Time }}<br />
            <span :class="getStatusClasses(exam)">
              Status: {{ exam.Assignment_Status }}
            </span>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              variant="outlined"
              :disabled="isExamUnavailable(exam)"
              @click="take(exam.Exam_ID)"
            >
              Take
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
    <!-- <v-row>
       Dynamic Exam Cards
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
            <v-btn
              color="secondary"
              variant="outlined"
              @click="view(exam.Exam_ID)"
              >View</v-btn
            >
           <v-btn color="primary" @click="deleteExam(exam.Exam_ID)"
                >Delete</v-btn
              > 
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>-->
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
        const response = await axios.post("/getStudentExams", { userId });

        // Handle the response
        this.exams = response.data; // Assuming the response has an exams array
      } catch (error) {
        console.error("Error fetching exams:", error);
        // Handle error (e.g., show a notification)
      }
    },
    take(examId) {
      const encodedId = btoa(examId.toString()); // Base64 encode the ID
      this.$router.push({ name: "takeExam", params: { examId: encodedId } });
    },
    isExamUnavailable(exam) {
      const now = new Date();
      const startDate = new Date(exam.Start_Time);
      const endDate = new Date(exam.End_Time);
      return (
        now < startDate ||
        now > endDate ||
        exam.Assignment_Status === "Completed"
      );
    },
    getCardClasses(exam) {
      return {
        "bg-surface": exam.Assignment_Status !== "Completed",
        "bg-secondary": exam.Assignment_Status === "Completed",
      };
    },
    getTitleClasses(exam) {
      return {
        "bg-primary": exam.Assignment_Status !== "Completed",
        "bg-secondary": exam.Assignment_Status === "Completed",
      };
    },
    getStatusClasses(exam) {
      return {
        "text-success": exam.Assignment_Status === "Completed",
        "text-warning": exam.Assignment_Status !== "Completed",
      };
    },
    goToManageExams() {
      this.$router.push({ name: "tmanageexams" }); // Use the route name or path
    },
    // ... other methods ...
  },
};
</script>
