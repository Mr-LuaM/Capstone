<template>
  <apexchart
    class="bg-surface pa-5 rounded-lg"
    width="100%"
    :options="chartOptions"
    :series="series"
  ></apexchart>
</template>

<script>
import { ref } from "vue";
import VueApexCharts from "vue3-apexcharts";
import axios from "axios";

export default {
  components: {
    apexchart: VueApexCharts,
  },
  setup() {
    const chartOptions = ref({
      chart: {
        type: "line",
        height: 350,
        animations: {
          enabled: true,
          easing: "easeinout",
          speed: 800,
          animateGradually: {
            enabled: true,
            delay: 150,
          },
          dynamicAnimation: {
            enabled: true,
            speed: 350,
          },
        },
        stroke: {
          curve: "smooth",
        },
        markers: {
          size: 10,
        },
        toolbar: {
          show: true,
          tools: {
            download: true,
            selection: true,
            zoom: true,
            zoomin: true,
            zoomout: true,
            pan: true,
            reset: true,
          },
          export: {
            csv: {
              filename: "enrollment-trends",
              columnDelimiter: ",",
              headerCategory: "Year",
              headerValue: "TotalEnrollments",
              dateFormatter(timestamp) {
                return new Date(timestamp).toDateString();
              },
            },
            svg: {
              filename: "enrollment-trends",
            },
            png: {
              filename: "enrollment-trends",
            },
          },
          autoSelected: "zoom",
        },
      },
      xaxis: {
        categories: [2020, 2021, 2022, 2023], // Years will be set here
        title: {
          text: "Year",
        },
      },
      yaxis: {
        title: {
          text: "Total Enrollments",
        },
      },
      fill: {
        opacity: 1,
      },
      tooltip: {
        y: {
          formatter: function (val) {
            return val + " enrollments";
          },
        },
      },
    });

    const series = ref([
      {
        name: "Approved",
        data: [],
        color: "#00E396", // Green color for approved
      },
      {
        name: "Rejected",
        data: [],
        color: "#FF4560", // Red color for rejected
      },
    ]);

    async function fetchData() {
      try {
        const response = await axios.get("/getEnrollmentTrends");

        // Assuming the response data is in the format you provided
        chartOptions.value.xaxis.categories = response.data.map(
          (item) => item.Year
        );
        series.value[0].data = response.data.map((item) =>
          parseInt(item.Approved)
        );
        series.value[1].data = response.data.map((item) =>
          parseInt(item.Rejected)
        );
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    }

    return { chartOptions, series, fetchData };
  },
  mounted() {
    this.fetchData();
  },
};
</script>
