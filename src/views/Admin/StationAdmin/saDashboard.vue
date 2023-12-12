<template>
  <v-container class="bg-surface rounded">
    <div id="chart">
      <apexchart
        type="line"
        height="350"
        :options="chartOptions"
        :series="series"
      ></apexchart>
    </div>
  </v-container>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      series: [],
      chartOptions: {
        chart: {
          height: 350,
          type: "line",
        },
        stroke: {
          width: 5,
          curve: "smooth",
        },
        xaxis: {
          type: "datetime",
          categories: [],
          tickAmount: 10,
          labels: {
            formatter: function (value, timestamp, opts) {
              return opts.dateFormatter(new Date(timestamp), "dd MMM");
            },
          },
        },
        title: {
          text: "Enrollment Trends",
          align: "left",
          style: {
            fontSize: "16px",
            color: "#666",
          },
        },
        fill: {
          type: "gradient",
          gradient: {
            shade: "dark",
            gradientToColors: ["#FDD835"],
            shadeIntensity: 1,
            type: "horizontal",
            opacityFrom: 1,
            opacityTo: 1,
            stops: [0, 100, 100, 100],
          },
        },
        yaxis: {
          min: 0, // Adjust as needed
        },
      },
    };
  },
  mounted() {
    // Fetch data from your backend using Axios
    this.fetchEnrollmentData();
  },
  methods: {
    async fetchEnrollmentData() {
      try {
        const response = await axios.get("getEnrollmentDetails");
        const enrollmentData = response.data;

        // Process the fetched data and update series and categories
        this.series = enrollmentData.map((entry) => ({
          name: entry.Stud_Name,
          data: [
            {
              x: new Date(entry.Enrollment_Date).getTime(),
              y: 1,
            },
          ],
        }));
        this.chartOptions.xaxis.categories = enrollmentData.map((entry) =>
          new Date(entry.Enrollment_Date).toISOString()
        );

        // Emit an event to update the chart
        this.$refs.chart.updateSeries(this.series);
      } catch (error) {
        console.error("Error fetching enrollment data:", error);
      }
    },
  },
};
</script>

<style>
/* Add any custom styles here */
</style>
