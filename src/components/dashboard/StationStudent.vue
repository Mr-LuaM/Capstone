<template>
  <v-card width="auto" height="130" flat :elevation="1" class="rounded-lg">
    <div class="d-flex pt-5 justify-space-between">
      <v-card-subtitle class="font-weight-bold">
        Students<br />
      </v-card-subtitle>
      <v-chip class="mt-n2 mr-3" color="success" text-color="white">
        <p class="font-weight-bold">{{ studentStats.percentageIncrease }}%</p>
      </v-chip>
    </div>
    <div class="d-flex align-center justify-center text-center mt-3">
      <v-card-text class="text-h4 mx-auto text-center font-weight-bold">
        {{ studentStats.totalStudents }}
      </v-card-text>
    </div>
  </v-card>
</template>

<script>
import axios from "axios";
import { mapState } from "vuex"; // Import mapState helper

export default {
  computed: {
    // Use mapState to access Vuex state
    ...mapState({
      stationId: (state) => state.stationId,
    }),
  },
  data() {
    return {
      studentStats: {
        totalStudents: 0,
        percentageIncrease: 0,
      },
    };
  },
  async created() {
    try {
      console.log("Station ID from Vuex:", this.stationId);
      const response = await axios.get(
        `getStudentStatisticsperStation/${this.stationId}`
      );
      this.studentStats = response.data;
    } catch (error) {
      console.error("Error fetching data:", error);
    }
  },
};
</script>
