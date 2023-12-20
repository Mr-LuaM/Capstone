<template>
    <v-container class="rounded-lg bg-surface" width="500px">
        <h6 class="chart-title mx-auto">Student Demographics</h6>
      <apexchart
        width="100%"
        :options="chartOptions"
        :series="series"
      ></apexchart>
    </v-container>
  </template>
  
  <script>
  import { ref, watchEffect } from "vue";
  import VueApexCharts from "vue3-apexcharts";
  import axios from "axios";
  
  export default {
    components: {
      apexchart: VueApexCharts,
    },
    setup() {
      const chartOptions = ref({
        chart: {
          type: "pie",
          height: 350,
        },
        labels: [],
        legend: {
          position: "top",
        },
        colors: ["#F44336", "#E91E63", "#9C27B0"],
      });
  
      const series = ref([]);
  
      async function fetchData() {
        try {
          const response = await axios.get("/getStudentDemographics"); // Replace with your API endpoint
          const labels = Object.keys(response.data);
          const values = Object.values(response.data);
  
          const newChartOptions = {
            chart: {
              type: "pie",
              height: 350,
            },
            labels: labels,
            legend: {
              position: "top",
            },
          };
  
          chartOptions.value = newChartOptions;
          series.value = values;
        } catch (error) {
          console.error("Error fetching data:", error);
        }
      }
  
      watchEffect(() => {
        // This ensures that changes in chartOptions or series trigger updates
        console.log(
          "Chart options or series changed",
          chartOptions.value,
          series.value
        );
      });
  
      return { chartOptions, series, fetchData };
    },
    mounted() {
      this.fetchData();
    },
  };
  </script>
  