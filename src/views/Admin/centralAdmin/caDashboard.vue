<template>
  <div>
    <div id="enrollmentTrendChart"></div>
    <div id="courseDistributionChart"></div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      enrollmentData: [],
    };
  },
  mounted() {
    this.fetchDashboardData();
  },
  methods: {
    async fetchDashboardData() {
      try {
        // Fetch data from the CI4 backend
        const response = await axios.get("getEnrollmentDetails");
        this.enrollmentData = response.data;

        // Render the charts
        this.renderEnrollmentTrendChart();
        this.renderCourseDistributionChart();
      } catch (error) {
        console.error("Error fetching dashboard data:", error);
      }
    },

    renderCourseDistributionChart() {
      const courseData = this.enrollmentData.map((entry) => ({
        name: entry.Course_Name || "Unknown Course", // Use 'name' instead of 'x'
        data: 1, // Assuming 1 student per entry for distribution
      }));

      const options = {
        chart: {
          type: "pie",
        },
        series: courseData,
        labels: courseData.map((entry) => entry.name), // Use 'name' instead of 'x'
      };

      const chart = new ApexCharts(
        document.querySelector("#courseDistributionChart"),
        options
      );
      chart.render();
    },
  },
};
</script>

<style>
/* Add any custom styles here */
</style>
