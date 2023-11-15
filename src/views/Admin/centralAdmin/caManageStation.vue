<template>
  <v-container fluid class="rounded-lg">
    <v-card flat>
      <v-card-title class="d-flex align-center pe-2 bg-primary-darken-1">
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
        class="elevation-1"
        show-select
        v-model="selected"
        return-object
      >
        <template v-slot:item.actions="{ item }">
          <v-btn small @click="editStation(item)" color="success" rounded tile>
            <v-icon>mdi-pencil</v-icon>
          </v-btn>

          <v-btn
            small
            @click="openConfirmDialog(item.Station_ID)"
            color="red"
            rounded
            tile
          >
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>

    <!-- Edit Station Modal -->
    <v-dialog v-model="dialog" max-width="500">
      <v-card>
        <v-card-title>Edit Station</v-card-title>
        <v-card-text>
          <!-- Your form with input fields for editing -->
          <v-text-field
            v-model="editedStation.Station_Name"
            label="Station Name"
          ></v-text-field>
          <v-text-field
            v-model="editedStation.Location"
            label="Location"
          ></v-text-field>
          <!-- Add other fields as needed -->
        </v-card-text>
        <v-card-actions>
          <v-btn @click="updateStation(editedStation)" color="primary">
            Save Changes
          </v-btn>
          <v-btn @click="closeDialog" color="grey lighten-1">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import { getStation } from "../../../services/BackendApi";

export default {
  data() {
    return {
      search: "",
      stations: [],
      headers: [
        { title: "Actions", value: "actions", sortable: false },
        { title: "Station ID", value: "Station_ID" },
        { title: "Station Name", value: "Station_Name" },
        { title: "Location", value: "Location" },
        { title: "Status", value: "status" },
        { title: "Created Since", value: "created_at" },
        { title: "Last Update", value: "status_updated_at" },
      ],
      dialog: false,
      editedStation: {},
    };
  },
  async created() {
    try {
      // Call the getStation function from the service
      this.stations = await getStation();
    } catch (error) {
      console.error("Failed to fetch stations:", error);
    }
  },
  methods: {
    editStation(station) {
      // Open the edit modal and populate the form with the selected station data
      this.dialog = true;
      this.editedStation = { ...station }; // Create a copy to avoid modifying the original data
    },
    closeDialog() {
      // Close the edit modal
      this.dialog = false;
    },
    async updateStation(station) {
      try {
        console.log(station.Station_ID);
        // Make a PUT request to your backend API endpoint
        const response = await axios.post(
          `updateStation/${station.Station_ID}`,
          station
        );
        return response.data;
      } catch (error) {
        console.error("Error updating station:", error);
        throw error;
      }
    },
  },
};
</script>
