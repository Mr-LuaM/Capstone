<template>
  <v-container fluid class="rounded-lg bg-surface">
    <v-card flat>
      <v-card-title class="d-flex align-center pe-2 bg-primary">
        <v-icon icon="mdi-book-open-page-variant"></v-icon> &nbsp; Manage Course

        <v-spacer></v-spacer>

        <v-col cols="auto">
          <v-btn
            icon="mdi-filter-variant"
            size="small"
            id="menu-activator"
            color="white"
            variant="text"
          ></v-btn>
        </v-col>
        <v-menu activator="#menu-activator" :close-on-content-click="false">
          <v-list>
            <v-container fluid>
              <v-checkbox
                v-model="filter"
                label="active"
                value="active"
                class="px-0 mx-0"
                hide-details
              ></v-checkbox>
              <v-checkbox
                v-model="filter"
                label="inactive"
                value="inactive"
                class="px-0 mx-0"
                hide-details
              ></v-checkbox>
            </v-container>
          </v-list>
        </v-menu>

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
          <v-btn variant="flat" @click="openAddDialog" color="secondary">
            Add New
          </v-btn>
        </v-col>
      </v-card-title>

      <v-divider></v-divider>

      <v-data-table
        v-model:search="search"
        :headers="headers"
        :items="filteredCourses"
        :item-value="(item) => `${item.Course_Name}-${item.Course_ID}`"
        class="elevation-1 rounded-lg"
        show-select
        v-model="selected"
        return-object
        hover="primary"
        show-expand
      >
        <template v-slot:expanded-row="{ columns, item }">
          <tr>
            <td :colspan="columns.length">
              <v-container class="ml-6 bg-background rounded" fluid>
                <v-table density="compact" class="rounded-lg">
                  <thead>
                    <tr>
                      <th class="text-left">Station Offerring</th>
                      <th class="text-left">Location</th>
                      <th class="text-left">Status</th>
                      <!-- <th class="text-left">
          Created
        </th>
        <th class="text-left">
          Last Update
        </th> -->
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="station in item.Stations_Offering"
                      :key="station.Station_ID"
                    >
                      <td class="text-left">{{ station.Station_Name }}</td>
                      <td class="text-left">{{ station.Location }}</td>
                      <td class="text-left">
                        <v-chip
                          :color="
                            station.status === 'active' ? 'success' : 'error'
                          "
                          size="small"
                          label
                        >
                          {{ station.status }}
                        </v-chip>
                      </td>
                      <!-- <td class="text-left">{{ course.created_at }}</td>
                <td class="text-left">{{ course.status_updated_at }}</td> -->
                    </tr>
                  </tbody>
                </v-table>
              </v-container>
            </td>
          </tr>
        </template>
        <template v-slot:item.status="{ value }">
          <v-chip :color="getColor(value)" size="small" label>
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
          >
          </v-btn>

          <v-btn
            size="x-large"
            @click="openConfirmDialog(item.Course_ID)"
            :color="item.status === 'inactive' ? 'success' : 'error'"
            density="compact"
            icon="mdi-swap-horizontal-circle-outline"
            variant="plain"
          >
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
  </v-container>

  <!-- Edit Station Modal -->

  <v-dialog v-model="dialog" persistent width="800">
    <v-card>
      <v-card-title>
        <span class="text-h5">{{ formTitle }}</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-form ref="form" @submit.prevent="submit">
            <v-row justify="center" align="center">
              <!-- Left side - Forms -->
              <v-col cols="12">
                <GenericTextField
                  customLabel="Course Name*"
                  required
                  v-model="editedCourse.Course_Name"
                />
              </v-col>

              <v-col cols="12">
                <GenericTextArea
                  customLabel="Course Description*"
                  required
                  v-model="editedCourse.Course_Description"
                />
              </v-col>
              <v-col cols="12" md="6" sm="4">
                <GenericNumber
                  customLabel="Duration"
                  :is-required="true"
                  v-model="editedCourse.Duration"
                />
              </v-col>
              <v-col cols="12" md="6" sm="4">
                <GenericNumber
                  customLabel="Credits"
                  :is-required="true"
                  v-model="editedCourse.Credits"
                />
              </v-col>

              <v-col cols="12">
                <GenericAutocomplete
                  v-model.lazy="editedCourse.status"
                  custom-label="Status"
                  :options="['active', 'inactive']"
                />
              </v-col>
            </v-row>

            <v-row justify="center" align="center">
              <v-container class="bg-background rounded">
                <v-table density="compact" class="rounded-lg">
                  <thead>
                    <tr>
                      <th class="text-left">Stations Offering</th>
                      <th class="text-right">
                        <v-btn
                          icon
                          @click="addStation"
                          variant="text"
                          color="success"
                          size="small"
                        >
                          <v-icon>mdi-plus</v-icon>
                        </v-btn>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(station, index) in editedCourse.Stations_Offering"
                      :key="index"
                    >
                      <td class="pt-3">
                        <StationSelection
                          required
                          v-model="station.Station_ID"
                          :model-value="station.Station_ID"
                          variant=""
                          density="compact"
                        />
                      </td>

                      <td class="text-right">
                        <v-btn
                          icon
                          @click="removeStation(index)"
                          variant="text"
                          color="error"
                          size="small"
                        >
                          <v-icon>mdi-minus</v-icon>
                        </v-btn>
                      </td>
                    </tr>
                  </tbody>
                </v-table>
              </v-container>
            </v-row>
          </v-form>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="secondary" variant="plain" @click="closeDialog">
          Close
        </v-btn>
        <v-btn
          v-if="isEditing"
          variant="text"
          @click="saveCourseChanges"
          color="primary"
        >
          Save Changes
        </v-btn>
        <v-btn v-else variant="text" @click="addCourses" color="primary">
          Add Course
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
  <infoSnack ref="snackbar" />
  <confirmationModal
    ref="confirmationModal"
    @confirm="archiveItem"
    @cancel="this.$refs.confirmationModal.dialog = false"
  />
</template>
<script>
import { getCourses } from "../../../services/BackendApi";
import axios from "axios";
export default {
  data() {
    return {
      courses: [],
      selected: null,
      search: "",
      filter: ["active"],
      headers: [
        {
          title: "Course Name",
          value: "Course_Name",
          align: "start",
          sortable: true,
        },
        {
          title: "Course_Description",
          value: "Course_Description",
          align: "start",
          sortable: true,
        },
        {
          title: "Duration (y)",
          value: "Duration",
          align: "start",
          sortable: true,
        },
        {
          title: "Credits",
          value: "Credits",
          align: "start",
          sortable: true,
        },
        { title: "Status", value: "status", align: "start", sortable: true },
        {
          title: "Created Since",
          value: "created_at",
          align: "start",
          sortable: true,
        },
        {
          title: "Last Update",
          value: "status_updated_at",
          align: "start",
          sortable: true,
        },
        {
          title: "Actions",
          value: "actions",
          sortable: false,
          width: "150px",
          align: "end",
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
    this.loadData();
  },
  methods: {
    async loadData() {
      try {
        // Call the getStation function from the service
        this.courses = await getCourses();
      } catch (error) {
        console.error("Failed to fetch stations:", error);
      }
    },
    getColor(status) {
      if (status === "active") return "success";
      else return "primary";
    },
    openAddDialog() {
      // Reset the editedCourse fields when opening the dialog for adding a new station
      this.editedCourse = {
        Course_Name: "",
        Course_Description: "",
        Duration: "",
        Credits: "",
        status: "",
        Stations_Offering: [],
      };

      // Set dialog to true to open the dialog
      this.dialog = true;
    },
    editCourse(course) {
      this.dialog = true;
      this.editedCourse = { ...course };
      this.originalEditedCourse = { ...course };
      console.log("Original state:", this.originalEditedCourse);
    },
    closeDialog() {
      if (this.isEditing) {
        this.dialog = false;
        console.log("Resetting to original state:", this.originalEditedCourse);
        this.editedCourse = { ...this.originalEditedCourse };
      } else {
        this.dialog = false;
      }
    },
    checkForDuplicates() {
      const uniqueStationIds = new Set();

      for (const station of this.editedCourse.Stations_Offering) {
        if (uniqueStationIds.has(station.Station_ID)) {
          return true; // Indicates duplicates were found
        }

        uniqueStationIds.add(station.Station_ID);
      }

      return false; // Indicates no duplicates were found
    },

    addStation() {
      // Check for duplicates before adding a new course
      if (this.checkForDuplicates()) {
        this.$refs.snackbar.openSnackbar(
          "Error: Duplicate stations selected",
          "error"
        );
        return; // Stop further processing
      }

      this.editedCourse = {
        ...this.editedCourse,
        Stations_Offering: [
          ...this.editedCourse.Stations_Offering,
          { Station_Name: null },
        ],
      };

      // Force a reactivity update
      this.$forceUpdate();
    },

    removeStation(index) {
      this.editedCourse = {
        ...this.editedCourse,
        Stations_Offering: this.editedCourse.Stations_Offering.filter(
          (_, i) => i !== index
        ),
      };
    },
    async addCourses() {
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        try {
          if (this.checkForDuplicates()) {
            this.$refs.snackbar.openSnackbar(
              "Error: Duplicate station selected",
              "error"
            );
            return; // Stop further processing if duplicates are found
          }

          const formData = new FormData();

          // Function to ensure the value is an integer
          function toInt(value) {
            return Number.isInteger(value) ? value : parseInt(value, 10);
          }

          formData.append("Course_Name", this.editedCourse.Course_Name);
          formData.append(
            "Course_Description",
            this.editedCourse.Course_Details
          );

          // Convert Duration and Credits to integers
          formData.append("Duration", toInt(this.editedCourse.Duration));
          formData.append("Credits", toInt(this.editedCourse.Credits));

          formData.append("status", this.editedCourse.status);

          this.editedCourse.Stations_Offering.forEach((station, index) => {
            formData.append(
              `Station_Offering[${index}][Station_ID]`,
              toInt(station.Station_ID)
            );
          });

          // Now formData contains values with Duration and Credits as integers

          // Make an HTTP POST request to the backend
          const response = await axios.post("addCourse", formData);

          // Check the response and show a success message or handle errors
          if (response.data.success === true) {
            this.$refs.snackbar.openSnackbar(response.data.message, "success");
            // Optionally, reset the form or perform other actions after a successful addition
            this.dialog = false;
            this.loadData();
          } else {
            this.$refs.snackbar.openSnackbar(response.data.message, "error");
          }
        } catch (error) {
          console.error("Error adding course:", error);
          // Handle the error and show an error message if needed
          this.$refs.snackbar.openSnackbar("Error adding course", "error");
        }
      }
    },
    async saveCourseChanges() {
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        try {
          // Check for duplicates before submitting
          if (this.checkForDuplicates()) {
            this.$refs.snackbar.openSnackbar(
              "Error: Duplicate course selected",
              "error"
            );
            return; // Stop further processing if duplicates are found
          }
          function toInt(value) {
            return Number.isInteger(value) ? value : parseInt(value, 10);
          }
          const formData = new FormData();
          formData.append("Course_ID", this.editedCourse.Course_ID);
          formData.append("Course_Name", this.editedCourse.Course_Name);
          formData.append(
            "Course_Description",
            this.editedCourse.Course_Description
          );

          // Convert Duration and Credits to integers
          formData.append("Duration", toInt(this.editedCourse.Duration));
          formData.append("Credits", toInt(this.editedCourse.Credits));

          formData.append("status", this.editedCourse.status);

          this.editedCourse.Stations_Offering.forEach((station, index) => {
            formData.append(
              `Station_Offering[${index}][Station_ID]`,
              toInt(station.Station_ID)
            );
          });

          // Make the Axios POST request
          const response = await axios.post("editCourse", formData);

          if (response.data.success === true) {
            // Show a success alert or perform other success-related actions here
            this.$refs.snackbar.openSnackbar(response.data.message, "success");
            this.dialog = false;
            this.loadData();
          } else {
            // Show the error Snackbar
            this.$refs.snackbar.openSnackbar(response.data.message, "error");
          }

          // Handle the response if needed
          console.log("Server response:", response.data);
        } catch (error) {
          console.error("Error saving changes:", error);
        }
      }
    },
    openConfirmDialog(id) {
      // Open the confirm dialog for approving or rejecting an application
      this.$refs.confirmationModal.dialog = true;
      this.$refs.confirmationModal.confirmAction = () => this.toggleStatus(id);
    },
    async toggleStatus(id) {
      try {
        const secureTokenResponse = await axios.get(
          "generateSecureToken/" + id
        );
        const secureToken = secureTokenResponse.data;
        // Use axios or your preferred HTTP library to send a request to your backend
        const response = await axios.post("toggleCourseStatus/" + id, {
          secureToken,
        });
        if (response.data.success === true) {
          // Show a success alert or perform other success-related actions here
          this.$refs.snackbar.openSnackbar(response.data.message, "success");
          this.$refs.confirmationModal.dialog = false;
          this.loadData();
        } else {
          // Show the error Snackbar
          this.$refs.snackbar.openSnackbar(response.data.message, "error");
        }
      } catch (error) {
        console.error("Error inactivating station:", error);
        // Handle errors as needed
      }
    },
  },
  computed: {
    filteredCourses() {
      // Apply filters based on the selected checkboxes
      return this.courses.filter((course) => {
        const statusFilter =
          this.filter.length === 0 || this.filter.includes(course.status);

        const searchFilter =
          !this.search ||
          Object.values(course).some(
            (value) =>
              typeof value === "string" &&
              value.toLowerCase().includes(this.search.toLowerCase())
          );

        return statusFilter && searchFilter;
      });
    },
    formTitle() {
      // Use a computed property to determine the form title based on the context (add or edit)
      return this.isEditing ? "Edit Course" : "Add Course";
    },
    isEditing() {
      // Determine if it's an edit operation based on your logic (e.g., checking if Station_ID exists and is not empty)
      return (
        Boolean(this.editedCourse.Course_ID) ||
        this.editedCourse.Course_ID === 0
      ); // Check for non-emptiness
    },
  },
};
</script>
