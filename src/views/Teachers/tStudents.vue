<template>
  <v-container fluid class="rounded-lg bg-surface">
    <v-card flat>
      <v-card-title class="d-flex align-center pe-2 bg-background">
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
          <v-btn @click="exportStudents" color="success">
            <v-icon>mdi-download</v-icon>
          </v-btn>
        </v-col>
        <input
          ref="gradesFileInput"
          type="file"
          accept=".xlsx"
          style="display: none"
          @change="importGrades"
        />

        <v-btn @click="openFileInput" color="secondary-darken-1">
          <v-icon>mdi-upload</v-icon>
        </v-btn>
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
            icon="mdi-plus"
            @click="editGrade(item)"
            color="success"
            variant="plain"
          ></v-btn>
        </template>
      </v-data-table>
    </v-card>
  </v-container>

  <v-dialog v-model="dialog" persistent width="800">
    <v-card>
      <v-card-title>
        <span class="text-h5">Input Grade</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-form ref="form" @submit.prevent="submit">
            <v-row justify="center" align="center">
              <v-col cols="12">
                Student ID: {{ editedStudents.Stud_ID }}
              </v-col>
              <v-col cols="12">
                <GradeFields
                  customLabel="Grade"
                  required
                  v-model="editedStudents.Grade"
                />
              </v-col>
            </v-row>
          </v-form>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="secondary" variant="plain" @click="this.dialog = false">
          Close
        </v-btn>
        <v-btn variant="text" @click="saveGrades" color="primary"> Save </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Assuming you have components named `infoSnack` and `confirmationModal` -->

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
      teachingAssignments: [],
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
        {
          title: "Duration",
          value: "Duration",
          align: "start",
          sortable: true,
        },
        {
          title: "Action",
          value: "actions",
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
    editGrade(enrollmentsByCourse) {
      // Get the current date
      const currentDate = new Date();

      const enrollmentDate = new Date(enrollmentsByCourse.Enrollment_Date);
      const durationInMonths = parseInt(enrollmentsByCourse.Duration, 10);

      // Calculate the difference in months
      const monthsDiff =
        (currentDate.getFullYear() - enrollmentDate.getFullYear()) * 12 +
        currentDate.getMonth() -
        enrollmentDate.getMonth();

      console.log("Months Difference:", monthsDiff);

      if (monthsDiff >= durationInMonths) {
        this.dialog = true;
        this.editedStudents = { ...enrollmentsByCourse };
        this.originalEditedStudents = { ...enrollmentsByCourse };
      } else {
        // Provide some feedback to the user that editing is not allowed
        // You can show a message, disable the button, etc.
        this.$refs.snackbar.openSnackbar("Duration is not met", "error");
      }
    },

    async openFileInput() {
      // Trigger the file input dialog
      this.$refs.gradesFileInput.click();
    },

    getChipColor(status) {
      switch (status.toLowerCase()) {
        case "ongoing":
          return "blue";
        case "passed":
          return "green";
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

        // Log the response data to inspect its structure
        console.log("Response data:", response.data);

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
    async saveGrades() {
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        try {
          // Check for duplicates before submitting

          const formData = new FormData();
          formData.append("Stud_ID", this.editedStudents.Stud_ID);
          formData.append("Grade", this.editedStudents.Grade);

          // Convert Duration and Credits to integers if needed

          // Make the Axios POST request
          const response = await axios.post("editGrade", formData);

          if (response.data.success === true) {
            // Show a success alert or perform other success-related actions here
            this.$refs.snackbar.openSnackbar(response.data.message, "success");
            this.dialog = false;
            this.getStudents();
          } else {
            // Show the error Snackbar
            this.$refs.snackbar.openSnackbar(success, "success");
            this.dialog = false;
            this.getStudents();
          }
          this.getStudents();

          // Handle the response if needed
          console.log("Server response:", response.data);
        } catch (error) {
          // Log the error details
        }
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

    // Process students data for the sheet
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
          "Profile Picture": student.Profile,
          "First Name": student.First_Name,
          "Middle Name": student.Middle_Name,
          "Last Name": student.Last_Name,
          "Name Extension": student.Name_Extension,
          "Age": student.Age,
          "Sex": student.Sex,
          "Address": student.Address,
          "Birthday": student.Birthday,
          "Birthplace": student.Birthplace,
          "Status": student.Status,
          "Nationality": student.Nationality,
          "Religion": student.Religion,
          "Phone Number": student.Stud_PhoneNum,
          "Grade": student.Grade, // Assuming 'Grade' is a field in your student data
        }))
      );
    }, []);
    const studentWs = XLSX.utils.json_to_sheet(studentData);

    // Style header row with color (if needed)
    // You can customize this section based on your needs

    XLSX.utils.book_append_sheet(wb, studentWs, "Students");

    // Save the workbook to a file
    XLSX.writeFile(wb, "students_data.xlsx");
  } catch (error) {
    console.error("Failed to export students:", error);
    // Handle errors as needed
  }
},


async importGrades(event) {
  // Get the current date
  const currentDate = new Date();

  const firstEnrollment = this.enrollmentsByCourse[0];

const enrollmentDate = new Date(firstEnrollment.Enrollment_Date);
const durationInMonths = parseInt(firstEnrollment.Duration, 10);

  // Calculate the difference in months
  const monthsDiff =
    (currentDate.getFullYear() - enrollmentDate.getFullYear()) * 12 +
    currentDate.getMonth() - 
    enrollmentDate.getMonth();

  console.log("Months Difference:", monthsDiff);

  // Check if the duration criteria is met
  if (monthsDiff < durationInMonths) {
    // Provide feedback that editing is not allowed
    this.$refs.snackbar.openSnackbar("Duration is not met", "error");
    return; // Exit the function if the duration criteria is not met
  }

  // Proceed if the duration criteria is met
  const file = event.target.files[0];
  if (!file) {
    console.error("No file selected");
    return; // Exit the function if no file is selected
  }

  // If a file is selected, proceed with form data creation and Axios call
  const formData = new FormData();
  formData.append("grades_file", file);

  try {
    const response = await axios.post("importGradesHandler", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    if (response.data.success) {
      console.log("Grades imported successfully");
      // Handle success
    } else {
      console.error(response.data.message);
      // Handle failure
    }
  } catch (error) {
    console.error("Error importing grades:", error);
    // Handle error
  }
},
  }
};
</script>
