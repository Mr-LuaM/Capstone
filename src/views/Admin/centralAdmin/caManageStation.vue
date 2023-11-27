<template>
  <v-container fluid class="rounded-lg bg-surface">
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

      <v-data-table-server
        v-model:search="search"
        :headers="headers"
        :items="stations"
        :item-value="(item) => `${item.Station_Name}-${item.Station_ID}`"
        class="elevation-1"
        show-select
        v-model="selected"
        return-object
        density="comfortable"
        hover="primary"
      >
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
      </v-data-table-server>
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
      <v-text-field
        label="Location*"
        required
        v-model="editedStation.Location"
        variant="outlined"
        density="compact"
      ></v-text-field>
    </v-col>

    <v-col cols="12" sm="6" md="4">
      <v-combobox
        label="Status*"
        required
        v-model="editedStation.status"
        variant="outlined"
        density="compact"
        :items="['active', 'inactive']"
      ></v-combobox>
    </v-col>


    <!-- Right side - Image -->
    <v-col cols="12" sm="6" md="8">
      <v-img
        src="../../../assets/img/designs/pnp-facade (1).png"
        alt="Image"
        width="100%"
        aspect-ratio="16/9"
        height="auto"
      ></v-img>
    </v-col>
  </v-row>
</v-form>

</v-container>



    
    </v-card-text>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn color="secondary" variant="plain" @click="dialog = false">
        Close
      </v-btn>
      <v-btn variant="tonal" @click="saveStationChanges" color="primary">
        Save Changes
      </v-btn>
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
        { title: "Actions", value: "actions", sortable: false, width:'150px',align: 'end'},
        { title: "Station ID", value: "Station_ID", align: 'end'},
        { title: "Station Name", value: "Station_Name", align: 'end'},
        { title: "Location", value: "Location", align: 'end'},
        { title: "Status", value: "status", align: 'end'},
        { title: "Created Since", value: "created_at", align: 'end'},
        { title: "Last Update", value: "status_updated_at", align: 'end'},
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
    getColor(status) {
      if (status === "active") return "success";
      else return "primary";
    },
    editStation(station) {
      // Open the edit modal and populate the form with the selected station data
      this.dialog = true;
      this.editedStation = { ...station }; // Create a copy to avoid modifying the original data
    },
    closeDialog() {
      // Close the edit modal
      this.dialog = false;
    },
    // async saveStationChanges() {
    //   try {
    //     const formData = new FormData();
    //     formData.append("lastName", this.lastName);
    //     formData.append("firstName", this.firstName);
    //     formData.append("middleName", this.middleName);
  //},
  },
};
</script>
