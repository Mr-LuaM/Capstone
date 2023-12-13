<template>
  <v-container class="rounded-lg bg-surface" width="500px">
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
        type: "bar",
        height: 350,
        stacked: true,
      },
      plotOptions: {
        bar: {
          horizontal: false,
        },
      },
      xaxis: {
        categories: [],
        title: {
          text: "Courses",
        },
      },
      yaxis: {
        title: {
          text: "Enrollments",
        },
      },
      legend: {
        position: "top",
      },
      colors: ["#F44336", "#E91E63", "#9C27B0"],
    });

    const series = ref([]);

    async function fetchData() {
      try {
        const response = await axios.get("/getCourseTrends");
        const newChartOptions = {
          chart: {
            type: "bar",
            height: 350,
            stacked: true,
          },
          plotOptions: {
            bar: {
              horizontal: false,
            },
          },
          xaxis: {
            categories: response.data.map((item) => item.Course),
            title: {
              text: "Courses",
            },
          },
          yaxis: {
            title: {
              text: "Enrollments",
            },
          },
          legend: {
            position: "top",
          },
        };
        chartOptions.value = newChartOptions;
        series.value = [
          {
            name: "Enrollments",
            data: response.data.map((item) => parseInt(item.Enrollments)),
          },
        ];
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
