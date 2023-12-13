<template>
  <v-row>
    <v-col col="8">
      <v-container class="bg-surface rounded">
        <FullCalendar class="demo-app-calendar" :options="calendarOptions" />
      </v-container>
    </v-col>
  </v-row>
</template>

<script>
import axios from "axios";
import { defineComponent } from "vue";
import FullCalendar from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import moment from "moment";

export default defineComponent({
  components: {
    FullCalendar,
  },
  data() {
    return {
      scheduleData: [],
    };
  },
  computed: {
    calendarOptions() {
      return {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        headerToolbar: {
          left: "prev,next today",
          center: "title",
          right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
        },
        initialView: "dayGridMonth",
        events: this.generateRecurringEvents(),
        editable: false,
        selectable: true,
        dayMaxEvents: true,
        eventClick: this.handleEventClick,
      };
    },
  },

  methods: {
    generateRecurringEvents() {
      const recurringEvents = [];

      for (const schedule of this.scheduleData) {
        const daysOfWeek = schedule.DaysOfWeek.split(","); // Split the days of the week
        const startTime = schedule.TimeFrom;
        const endTime = schedule.TimeTo;

        // Loop through each day of the week
        for (const day of daysOfWeek) {
          // Find the start date of the month for the specified day of the week
          const startDate = moment(schedule.MonthFrom)
            .startOf("month")
            .day(day);

          // Find the end date of the month for the specified day of the week
          const endDate = moment(schedule.MonthTo).endOf("month").day(day);

          // Create events for each occurrence within the specified date range
          let currentDate = startDate.clone();
          while (currentDate.isSameOrBefore(endDate)) {
            const startDateTime = moment(
              `${currentDate.format("YYYY-MM-DD")} ${startTime}`
            );
            const endDateTime = moment(
              `${currentDate.format("YYYY-MM-DD")} ${endTime}`
            );

            recurringEvents.push({
              title: schedule.Title || "Untitled Event",
              start: startDateTime.format(),
              end: endDateTime.format(),
              // Add other event properties as needed
            });

            // Move to the next occurrence based on the recurring pattern
            currentDate.add(1, "weeks");
          }
        }
      }

      return recurringEvents;
    },

    handleEventClick(clickInfo) {
      const dataId = clickInfo.event.id;
      // Handle event click as needed, e.g., open a modal with details
      console.log("Event Clicked with ID:", dataId);
    },

    fetchScheduleData() {
      // Check if a teacher is selected
      if (!this.$store.state.userId) {
        console.warn(
          "User ID not found. Please make sure the user is logged in."
        );
        return;
      }

      // Prepare the data to be sent
      const requestData = {
        user_id: this.$store.state.userId,
      };

      // Fetch teacher schedules
      axios
        .post("getStudentSchedule", requestData)
        .then((response) => {
          this.scheduleData = response.data;
        })
        .catch((error) => {
          console.error("Error fetching schedule data:", error);
        });
    },
  },
  mounted() {
    this.fetchScheduleData();
  },
});
</script>

<style lang="css">
h2 {
  margin: 0;
  font-size: 16px;
}

ul {
  margin: 0;
  padding: 0 0 0 1.5em;
}

li {
  margin: 1.5em 0;
  padding: 0;
}

b {
  /* used for event dates/times */
  margin-right: 3px;
}

.demo-app {
  display: flex;
  min-height: 100%;
  font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
  font-size: 14px;
}

.demo-app-sidebar {
  width: 300px;
  line-height: 1.5;
  background: #eaf9ff;
  border-right: 1px solid #d3e2e8;
}

.demo-app-sidebar-section {
  padding: 2em;
}

.demo-app-main {
  flex-grow: 1;
  padding: 3em;
}

.fc {
  /* the calendar root */
  max-width: 1100px;
  margin: 0 auto;
}
</style>
