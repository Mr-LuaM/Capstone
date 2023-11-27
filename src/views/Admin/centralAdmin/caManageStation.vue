<template>
  <v-container fluid class="rounded-lg bg-surface">
    <v-card flat>
      <v-card-title class="d-flex align-center pe-2 bg-secondary-darken-1">
        <v-icon icon="mdi-account"></v-icon> &nbsp; Manage Station

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
      </v-card-title>

      <v-divider></v-divider>

      <v-data-table
        v-model:search="search"
        :headers="headers"
        :items="stations"
        :item-value="(item) => `${item.Station_Name}-${item.Station_ID}`"
        class="elevation-1 rounded-lg"
        show-select
        v-model="selected"
        return-object
        density="comfortable"
        hover="primary"
        show-expand
      >
      
    <template v-slot:expanded-row="{ columns, item }">
   <tr><td :colspan="columns.length"> 
    <v-container class="ml-6 bg-background rounded" fluid >
      <v-table  density="compact" class="rounded-lg">
    <thead>
      <tr >
        <th class="text-left">
          Courses Offered
        </th>
        <th class="text-left">
          Course_Description
        </th>
        <th class="text-left">
          Duration
        </th>
        <th class="text-left">
          Credits
        </th>

      </tr>
    </thead>
    <tbody>
      <tr v-for="course in item.Courses_Offered" :key="course.Course_ID">
                <td>{{ course.Course_Name }}</td>
                <td>{{ course.Course_Description }}</td>
                <td>{{ course.Duration }}</td>
                <td>{{ course.Credits }}</td>
              </tr>
    </tbody>
    
  </v-table>
    </v-container>
  </td></tr>
    </template>
      <template v-slot:item.status="{ value }">
          <v-chip :color="getColor(value)" size="small" label>
            {{ value }}
          </v-chip>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-btn density="compact" size="x-large" icon="mdi-pencil" @click="editStation(item)" color="secondary" variant="plain">
            
          </v-btn>

          <v-btn
            size="x-large"
            @click="openConfirmDialog(item.Station_ID)"
            color="primary"
            density="compact"
            icon="mdi-delete"
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
      <span class="text-h5">Edit Station</span>
    </v-card-title>
    <v-card-text>
      <v-container>

        <v-form ref="form" @submit.prevent="saveStationChanges">
  <v-row justify="center" align="center">
    <!-- Left side - Forms -->
    <v-col cols="12" sm="6" md="4">

      <v-text-field
        label="Station Name*"
        required
        v-model="editedStation.Station_Name"
        variant="outlined"
        density="compact"
      ></v-text-field>
    </v-col>

    <v-col cols="12" sm="6" md="4">
      <Address
      divLabel=""
        v-model="editedStation.Location"
        customAppendInnerIcon=""
        customLabel="Address"
        customHint=""
      />
    </v-col>

    <v-col cols="12" sm="6" md="4">
      <GenericAutocomplete
          v-model="editedStation.status"
          custom-label="Status"
          :options="['active', 'inactive']"
        />
    </v-col>


    <!-- Right side - Image -->
    <!-- <v-col cols="12" sm="6" md="8">
      <v-img
        src="../../../assets/img/designs/pnp-facade (1).png"
        alt="Image"
        width="100%"
        aspect-ratio="16/9"
        height="auto"
      ></v-img>
    </v-col> -->
  </v-row>
  <v-row justify="center" align="center">
    <v-container class="bg-background rounded">
      <v-table density="compact" class="rounded-lg">
        <thead>
          <tr>
            <th class="text-left">Courses Offered</th>
            <th class="text-right">
              <v-btn icon @click="addCourse" variant="text" color="success" size="small">
                <v-icon>mdi-plus</v-icon>
              </v-btn>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(course, index) in editedStation.Courses_Offered" :key="index">
            <td class="pt-3">
              <CourseSelection
                  required
                  v-model="course.Course_ID"
                  :model-value="course.Course_ID" 
                  variant=""
                  density="compact"
                />
                <p>{{course.Course_ID  }}</p>
            </td>

            <td class="text-right">
              <v-btn icon @click="removeCourse(index)" variant="text" color="error" size="small">
                <v-icon>mdi-minus</v-icon>
              </v-btn>
            </td>
          </tr>

        </tbody>
      </v-table>
      <p>Course IDs: {{ editedStation.Courses_Offered.map(course => course.Course_ID).join(', ') }}</p>
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
      <v-btn variant="text" @click="saveStationChanges" color="primary">
        Save Changes
      </v-btn>
    </v-card-actions>
  </v-card>
  
</v-dialog>

  </v-container>
  <infoSnack ref="snackbar" />
</template>

<script>
import { getStations } from "../../../services/BackendApi";
import axios from "axios";

export default {
  data() {
    return {
      selected: null,
      search: '',
      stations: [],
      headers: [
        
        // { title: "Station ID", value: "Station_ID", align: 'start'},
        { title: "Station Name", value: "Station_Name", align: 'start',},
        { title: "Location", value: "Location", align: 'start'},
        { title: "Status", value: "status", align: 'start'},
        { title: "Created Since", value: "created_at", align: 'start'},
        { title: "Last Update", value: "status_updated_at", align: 'start'},
        { title: "Actions", value: "actions", sortable: false, width:'150px',align: 'end',},
      ],
      dialog: false,
      editedStation: {
        // Your form fields here
      },
      originalEditedStation: {}
    };
  },
  async created() {
    try {
      // Call the getStation function from the service
      this.stations = await getStations();
    } catch (error) {
      console.error("Failed to fetch stations:", error);
    }
  },
  methods: {
    getColor(status) {
      if (status === "active") return "success";
      else return "primary";
    },
    editStation(station) {
  console.log('Editing station...', station);
  this.dialog = true;
  this.editedStation = { ...station };
  this.originalEditedStation = { ...station };
  console.log('Original state:', this.originalEditedStation);
},
    closeDialog() {
  console.log('Closing dialog...');
  this.dialog = false;
  console.log('Resetting to original state:', this.originalEditedStation);
  this.editedStation = { ...this.originalEditedStation };
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
    this.$refs.snackbar.openSnackbar("Error: Duplicate course selected", "error");
    return; // Stop further processing
  }

  // Add a new course with a default Course_Name (you can customize this)
  this.editedStation.Courses_Offered.push({
    Course_Name: '', // Customize this default value
    Course_ID: '',   // Keep this empty for the new course
  });
},


    removeCourse(index) {
    this.editedStation = {
        ...this.editedStation,
        Courses_Offered: this.editedStation.Courses_Offered.filter((_, i) => i !== index)
    };
},
  
async saveStationChanges() {
  const { valid } = await this.$refs.form.validate();
  if (valid) {
    try {
      // Check for duplicates before submitting
      if (this.checkForDuplicates()) {
        this.$refs.snackbar.openSnackbar("Error: Duplicate course selected", "error");
        return; // Stop further processing if duplicates are found
      }

      const formData = new FormData();
      formData.append("Station_ID", this.editedStation.Station_ID);
      formData.append("Station_Name", this.editedStation.Station_Name);
      formData.append("Location", this.editedStation.Location);
      formData.append("status", this.editedStation.status);

      // Append Courses_Offered data
      this.editedStation.Courses_Offered.forEach((course, index) => {
        formData.append(`Courses_Offered[${index}][Course_ID]`, course.Course_ID);
      });

      // Make the Axios POST request
      const response = await axios.post("editStation", formData);

      // Handle the response if needed
      console.log("Server response:", response.data);
    } catch (error) {
      console.error("Error saving changes:", error);
    }
  }
},

}
};
</script>
