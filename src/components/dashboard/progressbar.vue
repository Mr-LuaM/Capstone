<template>
  <!-- ... other parts of your template ... -->

  <v-card>
    <v-card-title>Course Progress</v-card-title>
    <v-card-text>
      <v-progress-linear
        :value="progress"
        color="primary"
        striped
      ></v-progress-linear>
      <div>{{ progress.toFixed(2) }}% completed</div>
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
    // ... other methods ...
  },
  mounted() {
    this.fetchStudentProgress();
  },
  // ... other component options ...
};
</script>
