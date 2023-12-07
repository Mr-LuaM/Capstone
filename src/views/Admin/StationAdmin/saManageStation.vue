<template>
  <v-container
    class="rounded-lg bg-surface ml-auto text-left"
    style="max-width: 500px"
  >
    <v-card flat>
      <v-card-title class="ml-8">
        <span class="text-h5">{{ editedStation.Station_Name }}</span>
      </v-card-title>
      <v-card-subtitle class="ml-8">
        <span class="text-subtitle"
          >Station ID:{{ editedStation.Station_ID }}</span
        >
      </v-card-subtitle>
      <v-card-text>
        <v-container>
          <v-form ref="form" @submit.prevent="submit">
            <!-- ... Other form fields ... -->

            <!-- Left side - Forms -->
            <v-col cols="12">
              <GenericTextField
                customLabel="Station Name*"
                required
                v-model="editedStation.Station_Name"
              />
            </v-col>

            <v-col cols="12">
              <Address
                divLabel=""
                v-model="editedStation.Location"
                customAppendInnerIcon=""
                customLabel="Location"
                customHint=""
              />
            </v-col>

            <v-col cols="12">
              <GenericAutocomplete
                v-model.lazy="editedStation.status"
                custom-label="Status"
                :options="['active', 'inactive']"
              />
            </v-col>

            <!-- ... Other form fields ... -->

            <!-- Courses Offered -->
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
              </v-container>
            </v-row>
          </v-form>
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="secondary" variant="plain" @click="closeDialog">
          Reset
        </v-btn>
        <v-btn
          v-if="isEditing"
          variant="text"
          @click="saveStationChanges"
          color="primary"
        >
          Save Changes
        </v-btn>
        <v-btn
          v-else
          variant="text"
          @click="saveStationChanges"
          color="primary"
        >
          Save changes
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
  <infoSnack ref="snackbar" />
</template>

<script>
import axios from "axios";
export default {
  // ... Other component options ...

  data() {
    return {
      editedStation: {
        Station_Name: "", // Initialize with default values
        Location: "",
        status: "",
        Courses_Offered: [],
        // ... Other properties ...
      },
    };
  },

  methods: {
    // ... Other methods ...

    // Method to fetch and set station details based on the logged station admin
    async fetchStationDetails() {
      try {
        const formData = new FormData();

        // formData.append("sendingemail", this.selectedApplicant.email);
        formData.append("id", this.$store.getters.userId);
        // Make an API call to retrieve station details based on the logged station admin
        const response = await axios.post(
          "getStationDetailsperStation",
          formData
        );

        // Update the editedStation with the retrieved details
        this.editedStation = response.data;
      } catch (error) {
        console.error("Error fetching station details:", error);
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
    closeDialog() {
      this.fetchStationDetails();
    },
  },

  mounted() {
    // Call the method to fetch and set station details when the component is mounted
    this.fetchStationDetails();
  },
};
</script>
