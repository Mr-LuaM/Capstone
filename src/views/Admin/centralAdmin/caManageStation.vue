<template>
  <v-container fluid class="rounded-lg bg-surface">
    <v-card flat>
      <v-card-title class="d-flex align-center pe-2 bg-primary">
        <v-icon icon="mdi-lo"></v-icon> &nbsp; Manage Station

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
        :items="filteredStations"
        :item-value="(item) => `${item.Station_Name}-${item.Station_ID}`"
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
                      <th class="text-left">Courses Offered</th>
                      <th class="text-left">Course_Description</th>
                      <th class="text-left">Duration</th>
                      <th class="text-left">Credits</th>
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
                      v-for="course in item.Courses_Offered"
                      :key="course.Course_ID"
                    >
                      <td class="text-left">{{ course.Course_Name }}</td>
                      <td class="text-left">{{ course.Course_Description }}</td>
                      <td class="text-left">{{ course.Duration }}</td>
                      <td class="text-left">{{ course.Credits }}</td>
                      <td class="text-left">
                        <v-chip
                          :color="
                            course.status === 'active' ? 'success' : 'error'
                          "
                          size="small"
                          label
                        >
                          {{ course.status }}
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
            @click="editStation(item)"
            color="secondary"
            variant="plain"
          >
          </v-btn>

          <v-btn
            size="x-large"
            @click="openConfirmDialog(item.Station_ID)"
            :color="item.status === 'inactive' ? 'success' : 'error'"
            density="compact"
            icon="mdi-swap-horizontal-circle-outline"
            variant="plain"
          >
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

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
                <v-col cols="12" sm="6" md="4">
                  <GenericTextField
                    customLabel="Station Name*"
                    required
                    v-model="editedStation.Station_Name"
                  />
                </v-col>

                <v-col cols="12" sm="6" md="4">
                  <Address
                    divLabel=""
                    v-model="editedStation.Location"
                    customAppendInnerIcon=""
                    customLabel="Location"
                    customHint=""
                  />
                </v-col>

                <v-col cols="12" sm="6" md="4">
                  <GenericAutocomplete
                    v-model.lazy="editedStation.status"
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
                        <th class="text-left">Courses Offered</th>
                        <th class="text-right">
                          <v-btn
                            icon
                            @click="addCourse"
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
                        v-for="(course, index) in editedStation.Courses_Offered"
                        :key="index"
                      >
                        <td class="pt-3">
                          <CourseSelection
                            required
                            v-model="course.Course_ID"
                            :model-value="course.Course_ID"
                            variant=""
                            density="compact"
                          />
                        </td>

                        <td class="text-right">
                          <v-btn
                            icon
                            @click="removeCourse(index)"
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
                  <!-- <p>Course IDs: {{ editedStation.Courses_Offered.map(course => course.Course_ID).join(', ') }}</p> -->
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
            @click="saveStationChanges"
            color="primary"
          >
            Save Changes
          </v-btn>
          <v-btn v-else variant="text" @click="addStation" color="primary">
            Add Station
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
  <infoSnack ref="snackbar" />
  <confirmationModal
    ref="confirmationModal"
    @confirm="archiveItem"
    @cancel="this.$refs.confirmationModal.dialog = false"
  />
</template>

<script>
import { getStations } from "../../../services/BackendApi";
import axios from "axios";

export default {
  data() {
    return {
      selected: null,
      search: "",
      filter: ["active"],
      stations: [],
      headers: [
        // { title: "Station ID", value: "Station_ID", align: 'start'},
        {
          title: "Station Name",
          value: "Station_Name",
          align: "start",
          sortable: true,
        },
        {
          title: "Location",
          value: "Location",
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
      editedStation: {
        // Your form fields here
      },
      originalEditedStation: {},
    };
  },
  created() {
    this.loadData();
  },
  methods: {
    openAddDialog() {
      // Reset the editedStation fields when opening the dialog for adding a new station
      this.editedStation = {
        Station_Name: "",
        Location: "",
        status: "",
        Courses_Offered: [],
      };

      // Set dialog to true to open the dialog
      this.dialog = true;
    },
    async loadData() {
      try {
        // Call the getStation function from the service
        this.stations = await getStations();
      } catch (error) {
        console.error("Failed to fetch stations:", error);
      }
    },
    getColor(status) {
      if (status === "active") return "success";
      else return "primary";
    },
    editStation(station) {
      console.log("Editing station...", station);
      this.dialog = true;
      this.editedStation = { ...station };
      this.originalEditedStation = { ...station };
      console.log("Original state:", this.originalEditedStation);
    },
    closeDialog() {
      if (this.isEditing) {
        this.dialog = false;
        console.log("Resetting to original state:", this.originalEditedStation);
        this.editedStation = { ...this.originalEditedStation };
      } else {
        this.dialog = false;
      }
    },
    checkForDuplicates() {
      const uniqueCourseIds = new Set();

      for (const course of this.editedStation.Courses_Offered) {
        if (uniqueCourseIds.has(course.Course_ID)) {
          return true; // Indicates duplicates were found
        }

        uniqueCourseIds.add(course.Course_ID);
      }

      return false; // Indicates no duplicates were found
    },

    addCourse() {
      // Check for duplicates before adding a new course
      if (this.checkForDuplicates()) {
        this.$refs.snackbar.openSnackbar(
          "Error: Duplicate course selected",
          "error"
        );
        return; // Stop further processing
      }

      this.editedStation = {
        ...this.editedStation,
        Courses_Offered: [
          ...this.editedStation.Courses_Offered,
          { Course_Name: "" },
        ],
      };

      // Force a reactivity update
      this.$forceUpdate();
    },

    removeCourse(index) {
      this.editedStation = {
        ...this.editedStation,
        Courses_Offered: this.editedStation.Courses_Offered.filter(
          (_, i) => i !== index
        ),
      };
    },
    async addStation() {
      const { valid } = await this.$refs.form.validate();
      if (valid) {
        try {
          if (this.checkForDuplicates()) {
            this.$refs.snackbar.openSnackbar(
              "Error: Duplicate course selected",
              "error"
            );
            return; // Stop further processing if duplicates are found
          }

          const formData = new FormData();
          formData.append("Station_Name", this.editedStation.Station_Name);
          formData.append("Location", this.editedStation.Location);
          formData.append("status", this.editedStation.status);

          // Append Courses_Offered data
          this.editedStation.Courses_Offered.forEach((course, index) => {
            formData.append(
              `Courses_Offered[${index}][Course_ID]`,
              course.Course_ID
            );
          });

          // Make an HTTP POST request to the backend
          const response = await axios.post("addStation", formData);

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
          console.error("Error adding station:", error);
          // Handle the error and show an error message if needed
          this.$refs.snackbar.openSnackbar("Error adding station", "error");
        }
      }
    },
    async saveStationChanges() {
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

          const formData = new FormData();
          formData.append("Station_ID", this.editedStation.Station_ID);
          formData.append("Station_Name", this.editedStation.Station_Name);
          formData.append("Location", this.editedStation.Location);
          formData.append("status", this.editedStation.status);

          // Append Courses_Offered data
          this.editedStation.Courses_Offered.forEach((course, index) => {
            formData.append(
              `Courses_Offered[${index}][Course_ID]`,
              course.Course_ID
            );
          });

          // Make the Axios POST request
          const response = await axios.post("editStation", formData);

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
        const response = await axios.post("toggleStatus/" + id, {
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
    filteredStations() {
      // Apply filters based on the selected checkboxes
      return this.stations.filter((station) => {
        const statusFilter =
          this.filter.length === 0 || this.filter.includes(station.status);

        const searchFilter =
          !this.search ||
          Object.values(station).some(
            (value) =>
              typeof value === "string" &&
              value.toLowerCase().includes(this.search.toLowerCase())
          );

        return statusFilter && searchFilter;
      });
    },
    formTitle() {
      // Use a computed property to determine the form title based on the context (add or edit)
      return this.isEditing ? "Edit Station" : "Add Station";
    },
    isEditing() {
      // Determine if it's an edit operation based on your logic (e.g., checking if Station_ID exists and is not empty)
      return (
        Boolean(this.editedStation.Station_ID) ||
        this.editedStation.Station_ID === 0
      ); // Check for non-emptiness
    },
  },
};
</script>
