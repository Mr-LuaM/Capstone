<template>
  <v-card color="basil" variant="flat">
    <v-card-title class="text-center justify-center py-6 bg-secondary">
      <h1 class="font-weight-bold text-h2 text-basil">{{ examTitle }}</h1>
    </v-card-title>

    <v-card-text>
      <!-- Timer Display -->
      <div class="text-center">
        <h2>Time Remaining: {{ remainingTime }}</h2>
      </div>

      <!-- Questions and Options -->
      <div v-for="(question, qIndex) in questions" :key="qIndex" class="my-5">
        <div class="text-h5 mb-3">{{ qIndex + 1 }}. {{ question.text }}</div>
        <v-radio-group v-model="question.selectedAnswer">
          <v-radio
            v-for="(option, oIndex) in question.options"
            :key="oIndex"
            :label="option"
            :value="option"
          ></v-radio>
        </v-radio-group>
      </div>

      <!-- Submit Button -->
      <div class="text-right">
        <v-btn color="success" @click="submitExam">Submit</v-btn>
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
import axios from "axios";
import router from "@/router";
export default {
  data: () => ({
    examId: null,
    examTitle: "Untitled",
    questions: [],
    remainingTime: null,
    timerInterval: null,
    examDuration: 0,
    examStartTime: null,
  }),
  created() {
    if (this.$route.params.examId) {
      this.examId = atob(this.$route.params.examId); // Decode the ID
      this.fetchExamData(this.examId);
    }
  },
  methods: {
    async fetchExamData(examId) {
      try {
        const response = await axios.get(`/getStudentExam/${examId}`);
        this.populateFormData(response.data);
      } catch (error) {
        console.error("Error fetching exam data:", error);
      }
    },
    startTimer() {
      const savedStartTime = localStorage.getItem(
        `examStartTime-${this.examId}`
      );
      if (savedStartTime) {
        this.examStartTime = new Date(savedStartTime);
      } else {
        this.examStartTime = new Date();
        localStorage.setItem(
          `examStartTime-${this.examId}`,
          this.examStartTime.toISOString()
        );
      }

      this.updateRemainingTime();

      this.timerInterval = setInterval(() => {
        this.updateRemainingTime();
      }, 1000);
    },
    updateRemainingTime() {
      const currentTime = new Date();
      const elapsedSeconds = Math.floor(
        (currentTime - this.examStartTime) / 1000
      );
      const totalDurationSeconds = this.examDuration * 60;
      let remainingSeconds = totalDurationSeconds - elapsedSeconds;

      if (remainingSeconds <= 0) {
        remainingSeconds = 0;
        clearInterval(this.timerInterval);
        this.submitExam();
      }

      this.remainingTime = this.formatTime(remainingSeconds);
    },
    formatTime(seconds) {
      const hours = Math.floor(seconds / 3600);
      const minutes = Math.floor((seconds % 3600) / 60);
      const secs = seconds % 60;
      return [hours, minutes, secs]
        .map((val) => val.toString().padStart(2, "0"))
        .join(":");
    },
    async submitExam() {
      try {
        const userId = this.$store.state.userId; // Retrieve the student's user ID from the Vuex store

        // Prepare answers data
        const answers = this.questions.map((q) => ({
          questionId: q.Question_ID,
          selectedAnswer: q.selectedAnswer,
        }));

        // Submit data
        const response = await axios.post("/submitExamAnswers", {
          examId: this.examId,
          userId: userId, // Send student's user ID
          answers: answers,
        });

        if (response.data.success === true) {
          router.replace({ path: "/student/exams" });
          console.log("Exam submitted successfully");
        } else {
          // Handle failure (e.g., show an error message)
          console.error("Failed to submit exam:", response.data.error);
        }
      } catch (error) {
        console.error("Error while submitting exam:", error);
      } finally {
        clearInterval(this.timerInterval);
        localStorage.removeItem(`examStartTime-${this.examId}`); // Clear the stored start time
      }
    },
    populateFormData(data) {
      this.examTitle = data.Exam_Title;
      this.examDuration = parseInt(data.Duration_Minutes);

      this.questions = data.questions.map((question) => ({
        Question_ID: question.Question_ID,
        text: question.Question_Text,
        options: question.options,
        selectedAnswer: null,
      }));

      this.startTimer(); // Start the timer after data is populated
    },
  },
  beforeDestroy() {
    clearInterval(this.timerInterval);
  },
};
</script>

<style>
/* Add your CSS styles here */
</style>
