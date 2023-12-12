<template>
  <v-row>
    <v-col col="8">
      <v-container class="bg-surface rounded">
        <FullCalendar class="demo-app-calendar" :options="calendarOptions" />
      </v-container>
    </v-col>

    <v-col col="4">
      <v-container class="bg-surface rounded">
        <v-form @submit.prevent="saveSchedule" ref="scheduleForm">
          <v-row>
            <v-col md="6">
              <FacultyAutoselect
                v-model="selectedTeacher"
                :customLabel="'Select Teacher'"
                :customVariant="'outlined'"
                :customPlaceholder="'Choose a Teacher'"
                :customDensity="'compact'"
                @input="onTeacherSelected"
              ></FacultyAutoselect>
            </v-col>
            <v-col md="6">
              <v-textarea
                v-model="formData"
                label="Title"
                rows="3"
                variant="outlined"
                density="compact"
              ></v-textarea>
              <!-- Other form fields... -->
            </v-col>
          </v-row>
          <v-row class="form-group for-repeating">
            <v-col>
              <v-label for="dow">Days of Week</v-label>
              <v-select
                v-model="selectedDays"
                multiple
                chips
                id="dow"
                :items="daysOfWeek"
                variant="outlined"
                density="compact"
              ></v-select>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" md="6">
              <v-row class="form-group for-repeating">
                <v-col>
                  <v-label>Month From</v-label>
                  <v-text-field
                    v-model="monthFrom"
                    type="month"
                    variant="outlined"
                    density="compact"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-col>

            <v-col cols="12" md="6">
              <v-row class="form-group for-repeating">
                <v-col>
                  <v-label>Month To</v-label>
                  <v-text-field
                    v-model="monthTo"
                    type="month"
                    variant="outlined"
                    density="compact"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="6">
              <v-row class="form-group for-nonrepeating" style="display: none">
                <v-col>
                  <v-label>Schedule Date</v-label>
                  <v-text-field
                    v-model="scheduleDate"
                    type="date"
                    variant="outlined"
                    density="compact"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" md="6">
              <v-row class="form-group">
                <v-col>
                  <v-label>Time From</v-label>
                  <v-text-field
                    v-model="timeFrom"
                    type="time"
                    variant="outlined"
                    density="compact"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-col>

            <v-col cols="12" md="6">
              <v-row class="form-group">
                <v-col>
                  <v-label>Time To</v-label>
                  <v-text-field
                    v-model="timeTo"
                    type="time"
                    variant="outlined"
                    density="compact"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <!-- Add the rest of your form fields... -->

          <v-row>
            <v-col>
              <v-btn type="submit" color="primary">Save</v-btn>
            </v-col>
            <v-col class="text-right">
              <v-btn @click="fetchScheduleData" color="secondary"
                >Fetch Teacher Schedule</v-btn
              >
            </v-col>
          </v-row>
        </v-form>
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
      selectedTeacher: null,
      formData: "",
      selectedDays: [],
      monthFrom: "",
      monthTo: "",
      scheduleDate: "",
      timeFrom: "",
      timeTo: "",

      daysOfWeek: [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
      ],
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

    saveSchedule() {
      // Create FormData object
      const formData = new FormData();

      // Append fields to FormData
      formData.append("teacherId", this.selectedTeacher); // Adjust according to your data structure
      formData.append("title", this.formData);
      formData.append("daysOfWeek", this.selectedDays.join(",")); // Assuming selectedDays is an array
      formData.append("monthFrom", this.monthFrom);
      formData.append("monthTo", this.monthTo);
      formData.append("scheduleDate", this.scheduleDate);
      formData.append("timeFrom", this.timeFrom);
      formData.append("timeTo", this.timeTo);
      // Append other fields as needed...

      // Send the FormData to the server
      axios
        .post("saveSchedule", formData)
        .then((response) => {
          console.log("Schedule saved successfully:", response.data);
          // Optionally, perform any additional actions after saving
        })
        .catch((error) => {
          console.error("Error saving schedule:", error);
          // Optionally, handle the error and provide feedback to the user
        });
    },

    handleEventClick(clickInfo) {
      const dataId = clickInfo.event.id;
      // Handle event click as needed, e.g., open a modal with details
      console.log("Event Clicked with ID:", dataId);
    },

    fetchScheduleData() {
      // Check if a teacher is selected
      if (!this.selectedTeacher) {
        console.warn("Please select a teacher first.");
        return;
      }

      // Prepare the data to be sent
      const requestData = {
        teacherId: this.selectedTeacher, // Assuming this is the correct property name
      };

      // Fetch teacher schedules
      axios
        .post("getTeacherSchedule", requestData)
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
