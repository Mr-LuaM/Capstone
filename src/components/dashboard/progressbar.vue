<template>
  <!-- ... other parts of your template ... -->

  <v-card>
    <v-card-title>Course Progress</v-card-title>
    <v-card-text>
      <div v-if="progress < 100">
        <v-progress-linear
          :value="progress"
          color="light-blue"
          height="25"
          striped
        >
          <template v-slot:default="{ value }">
            <strong>{{ progress.toFixed(2) }}% completed</strong>
          </template>
        </v-progress-linear>
      </div>

      <v-btn block density="compact" v-else color="primary" @click="enrollAgain">
        Enroll Again
      </v-btn>
    </v-card-text>
  </v-card>

  <!-- ... other parts of your template ... -->
</template>

<script>
import axios from "axios";
// ... other imports ...

export default {
  // ... other component options ...
  data() {
    return {
      progress: 0, // This will hold the progress percentage
      // ... other data properties ...
    };
  },
  methods: {
    fetchStudentProgress() {
      const userId = this.$store.state.userId; // Assuming this is the user's ID
      axios
        .post(`getStudentProgressByUserId`, { user_id: userId })
        .then((response) => {
          this.progress = response.data.progress;
        })
        .catch((error) => {
          console.error("Error fetching progress data:", error);
        });
    },
    enrollAgain() {
      // Logic to handle re-enrollment
      this.$router.push({ path: '/application' });
    },
    // ... other methods ...
  },
  mounted() {
    this.fetchStudentProgress();
  },
  // ... other component options ...
};
</script>
