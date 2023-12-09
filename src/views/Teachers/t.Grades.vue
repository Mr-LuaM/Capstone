<template>
  <v-container fluid class="rounded-lg bg-surface">
    <v-card flat>
      <v-card-title class="d-flex align-center pe-2 bg-primary">
        <v-icon icon="mdi-human-greeting"></v-icon> &nbsp; Manage Students

        <v-spacer></v-spacer>

        <v-text-field
          v-model="search"
          prepend-inner-icon="mdi-magnify"
          density="compact"
          label="Search"
          single-line
          flat
          hide-details
          variant="outlined"
        ></v-text-field>
        <v-col cols="auto">
          <v-btn @click="exportStudents" color="success">Download</v-btn>
        </v-col>
      </v-card-title>

      <v-divider></v-divider>

      <v-data-table
        v-model:search="search"
        :headers="headers"
        :items="enrollmentsByCourse"
        :item-value="(item) => `${item.Course_ID}-${item.Stud_ID}`"
        class="elevation-1 rounded-lg"
        show-select
        v-model="selected"
        return-object
        hover="primary"
      >
        <template v-slot:item.Enrollment_Status="{ value }">
          <v-chip :color="getChipColor(value)" size="small" label>
            {{ value }}
          </v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn
            density="compact"
            size="x-large"
            icon="mdi-pencil"
            @click="editCourse(item)"
            color="secondary"
            variant="plain"
          ></v-btn>
          <v-btn
            size="x-large"
            @click="openConfirmDialog(item.Course_ID)"
            :color="item.Enrollment_Status === 'inactive' ? 'success' : 'error'"
            density="compact"
            icon="mdi-swap-horizontal-circle-outline"
            variant="plain"
          ></v-btn>
        </template>
      </v-data-table>
    </v-card>
  </v-container>

  <!-- Edit Station Modal -->

  <infoSnack ref="snackbar" />
  <confirmationModal
    ref="confirmationModal"
    @confirm="archiveItem"
    @cancel="this.$refs.confirmationModal.dialog = false"
  />
</template>

<script>
import * as XLSX from "xlsx";
import axios from "axios";
export default {
  data() {
    return {
      enrollmentsByCourse: [],
      selected: null,
      search: "",
      filter: ["active"],
      headers: [
        {
          title: "Student ID",
          value: "Stud_ID",
          align: "start",
          sortable: true,
        },
        {
          title: "Student Name",
          value: "First_Name",
          align: "start",
          sortable: true,
        },
        {
          title: "Enrollment Date",
          value: "Enrollment_Date",
          align: "start",
          sortable: true,
        },
        {
          title: "Enrollment Status",
          value: "Enrollment_Status",
          align: "start",
          sortable: true,
        },
        {
          title: "Phone Number",
          value: "Stud_PhoneNum",
          align: "start",
          sortable: true,
        },
        {
          title: "Grade",
          value: "Grade",
          align: "start",
          sortable: true,
        },
      ],

      dialog: false,
      editedCourse: {
        // Your form fields here
      },
      originalEditedCourse: {},
    };
  },
  created() {
    this.getStudents();
  },
  methods: {
    getChipColor(status) {
      switch (status.toLowerCase()) {
        case "ongoing":
          return "green";
        case "finished":
          return "blue";
        case "failed":
          return "red";
        default:
          return "grey";
      }
    },
    async getStudents() {
      try {
        const response = await axios.post("getStudents", {
          user_id: this.$store.state.userId,
        });

        // Filter students based on teaching assignments
        const teachingAssignments = response.data;
        const filteredStudents = teachingAssignments.reduce(
          (acc, assignment) => {
            return acc.concat(assignment.students);
          },
          []
        );

        this.enrollmentsByCourse = filteredStudents;
      } catch (error) {
        console.error("Failed to fetch enrollments by course:", error);
        // Handle errors as needed
      }
    },

    async exportStudents() {
      try {
        const response = await axios.post("getStudents", {
          user_id: this.$store.state.userId,
        });

        const teachingAssignments = response.data;

        // Create a workbook
        const wb = XLSX.utils.book_new();

        // Add a sheet for teaching assignments
        const assignmentData = teachingAssignments.map((assignment) => ({
          "Teaching Assignment ID":
            assignment.teaching_assignment.TeachingAssignment_ID,
          "Teacher ID": assignment.teaching_assignment.Teacher_ID,
          "Course ID": assignment.teaching_assignment.course_id,
          "Station ID": assignment.teaching_assignment.station_id,
          "Course Name": assignment.teaching_assignment.Course_Name,
          "Station Name": assignment.teaching_assignment.Station_Name,
          "User ID": assignment.teaching_assignment.User_ID,
          "Profile Picture": assignment.teaching_assignment.Profile_Picture,
          "First Name": assignment.teaching_assignment.First_Name,
          "Middle Name": assignment.teaching_assignment.Middle_Name,
          "Last Name": assignment.teaching_assignment.Last_Name,
          "Name Extension": assignment.teaching_assignment.Name_Extension,
          Age: assignment.teaching_assignment.Age,
          Sex: assignment.teaching_assignment.Sex,
          Address: assignment.teaching_assignment.Address,
          Birthday: assignment.teaching_assignment.Birthday,
          Status: assignment.teaching_assignment.Status,
          Nationality: assignment.teaching_assignment.Nationality,
          Religion: assignment.teaching_assignment.Religion,
          "Teacher Phone Number":
            assignment.teaching_assignment.Teacher_PhoneNum,
          Grade: "", // New column for grades
        }));
        const assignmentWs = XLSX.utils.json_to_sheet(assignmentData);

        // Style header row with color
        assignmentWs["!header"] = [
          {
            t: "s",
            v: "Header Text",
            s: {
              font: { color: { rgb: "FFFFFF" } },
              fill: { fgColor: { rgb: "333333" } },
            },
          },
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          {
            t: "s",
            v: "Grade",
            s: {
              font: { color: { rgb: "FFFFFF" } },
              fill: { fgColor: { rgb: "333333" } },
            },
          },
        ];

        XLSX.utils.book_append_sheet(wb, assignmentWs, "Teaching Assignments");

        // Add a sheet for students
        const studentData = teachingAssignments.reduce((acc, assignment) => {
          return acc.concat(
            assignment.students.map((student) => ({
              "Enrollment ID": student.Enrollment_ID,
              "Student ID": student.Stud_ID,
              "Course ID": student.Course_ID,
              "Station ID": student.Station_ID,
              "Enrollment Date": student.Enrollment_Date,
              "Enrollment Status": student.Enrollment_Status,
              "User ID": student.User_ID,
              Profile: student.Profile,
              "First Name": student.First_Name,
              "Middle Name": student.Middle_Name,
              "Last Name": student.Last_Name,
              "Name Extension": student.Name_Extension,
              Age: student.Age,
              Sex: student.Sex,
              Address: student.Address,
              Birthday: student.Birthday,
              Birthplace: student.Birthplace,
              Status: student.Status,
              Nationality: student.Nationality,
              Religion: student.Religion,
              "Phone Number": student.Stud_PhoneNum,
              Grade: "", // New column for grades
            }))
          );
        }, []);
        const studentWs = XLSX.utils.json_to_sheet(studentData);

        // Style header row with color
        studentWs["!header"] = [
          {
            t: "s",
            v: "Header Text",
            s: {
              font: { color: { rgb: "FFFFFF" } },
              fill: { fgColor: { rgb: "333333" } },
            },
          },
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          null,
          {
            t: "s",
            v: "Grade",
            s: {
              font: { color: { rgb: "FFFFFF" } },
              fill: { fgColor: { rgb: "333333" } },
            },
          },
        ];

        XLSX.utils.book_append_sheet(wb, studentWs, "Students");

        // Save the workbook to a file
        XLSX.writeFile(wb, "students_data.xlsx");
      } catch (error) {
        console.error("Failed to export students:", error);
        // Handle errors as needed
      }
    },
  },
};
</script>
